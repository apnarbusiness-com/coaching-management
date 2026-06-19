<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchAttendance;
use App\Models\StudentBasicInfo;
use App\Models\StudentMonthlyDue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class BatchAttendanceController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('batch_attendance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $attendanceMonth = Carbon::today()->month;
        $attendanceYear = Carbon::today()->year;

        $batches = Batch::with('subject')
            ->withCount([
                'students as students_count' => function ($query) use ($attendanceMonth, $attendanceYear) {
                    $query->whereMonth('batch_student_basic_info.enrolled_at', $attendanceMonth)
                        ->whereYear('batch_student_basic_info.enrolled_at', $attendanceYear);
                },
            ]);

        if (auth()->user()->isTeacher()) {
            $teacher = auth()->user()->teacher;
            if ($teacher) {
                $batches = $batches->whereHas('teachers', function ($query) use ($teacher) {
                    $query->where('teachers.id', $teacher->id);
                });
            }
        }

        $batches = $batches->orderBy('batch_name')->get();
        // ->map(function ($batch) {
        //     return [
        //         'id' => $batch->id,
        //         'batch_name' => $batch->batch_name,
        //         'subject_name' => $batch->subject?->name ?? 'N/A',
        //         'student_count' => $batch->students()->wherePivot('enrolled_at', '<=', Carbon::today())->count(),
        //     ];
        // });

        // return  $batches[0]->students;

        $attendanceMonthLabel = Carbon::createFromDate($attendanceYear, $attendanceMonth, 1)->format('F Y');

        return view('admin.batchAttendances.index', compact(
            'batches',
            'attendanceMonth',
            'attendanceYear',
            'attendanceMonthLabel'
        ));
    }

    public function showAttendanceForm(Request $request, $batchId)
    {
        abort_if(Gate::denies('batch_attendance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $batch = Batch::with('subject')->findOrFail($batchId);

        if (auth()->user()->isTeacher()) {
            $teacher = auth()->user()->teacher;
            if ($teacher && ! $batch->teachers()->where('teachers.id', $teacher->id)->exists()) {
                abort_if(true, Response::HTTP_FORBIDDEN, '403 Forbidden');
            }
        }

        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $attendanceMonth = Carbon::parse($date)->month;
        $attendanceYear = Carbon::parse($date)->year;

        $students = $batch->students()
            ->with('studentDetails')
            ->whereMonth('batch_student_basic_info.enrolled_at', $attendanceMonth)
            ->whereYear('batch_student_basic_info.enrolled_at', $attendanceYear)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $existingAttendances = BatchAttendance::where('batch_id', $batchId)
            ->where('attendance_date', $date)
            ->pluck('status', 'student_id')
            ->toArray();

        $currentMonth = Carbon::parse($date)->month;
        $currentYear = Carbon::parse($date)->year;

        $studentTotalDues = StudentMonthlyDue::whereIn('student_id', $students->pluck('id'))
            ->where('batch_id', $batchId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->groupBy('student_id')
            ->select('student_id', DB::raw('sum(due_remaining) as due_remaining_total'))
            ->pluck('due_remaining_total', 'student_id');

        $studentAttendanceHistory = BatchAttendance::where('batch_id', $batchId)
            ->whereIn('student_id', $students->pluck('id'))
            ->where('attendance_date', '<=', $date)
            ->orderBy('attendance_date', 'desc')
            ->get()
            ->groupBy('student_id')
            ->map(fn ($records) => $records->take(10));

        $formattedStudents = $students->map(function ($student) use ($existingAttendances, $studentTotalDues, $studentAttendanceHistory) {
            $totalDueRemaining = (float) ($studentTotalDues[$student->id] ?? 0);
            $hasDue = $totalDueRemaining > 0;

            $attendanceHistory = $studentAttendanceHistory->get($student->id, collect())
                ->map(fn ($record) => [
                    'date' => Carbon::parse($record->attendance_date)->format('d M'),
                    'status' => $record->status,
                ]);

            return [
                'id' => $student->id,
                'name' => trim(($student->first_name ?? '').' '.($student->last_name ?? '')),
                'roll' => $student->roll ?? '',
                'id_no' => $student->id_no ?? '',
                'image' => $student->image?->thumbnail ?? null,
                'status' => $existingAttendances[$student->id] ?? 'absent',
                'has_due' => $hasDue,
                'due_amount' => $totalDueRemaining,
                'attendance_history' => $attendanceHistory,
            ];
        });

        $stats = [
            'total' => $students->count(),
            'present' => collect($existingAttendances)->where(fn ($v) => $v === 'present')->count(),
            'absent' => collect($existingAttendances)->where(fn ($v) => $v === 'absent')->count(),
            'late' => collect($existingAttendances)->where(fn ($v) => $v === 'late')->count(),
            'marked' => count($existingAttendances),
        ];

        $attendanceMonthLabel = Carbon::createFromDate($attendanceYear, $attendanceMonth, 1)->format('F Y');

        return view('admin.batchAttendances.take', compact(
            'batch',
            'date',
            'formattedStudents',
            'stats',
            'attendanceMonth',
            'attendanceYear',
            'attendanceMonthLabel'
        ));
    }

    public function store(Request $request, $batchId)
    {
        abort_if(Gate::denies('batch_attendance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $batch = Batch::findOrFail($batchId);

        if (auth()->user()->isTeacher()) {
            $teacher = auth()->user()->teacher;
            if ($teacher && ! $batch->teachers()->where('teachers.id', $teacher->id)->exists()) {
                abort_if(true, Response::HTTP_FORBIDDEN, '403 Forbidden');
            }
        }

        $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent,late',
        ]);

        $date = $request->input('date');
        $attendanceData = $request->input('attendance', []);
        $remarks = $request->input('remarks', []);

        $records = [];
        foreach ($attendanceData as $studentId => $status) {
            $records[] = [
                'batch_id' => $batchId,
                'student_id' => $studentId,
                'attendance_date' => $date,
                'status' => $status,
                'remarks' => $remarks[$studentId] ?? null,
                'recorded_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        BatchAttendance::upsert(
            $records,
            ['batch_id', 'student_id', 'attendance_date'],
            ['status', 'remarks', 'recorded_by', 'updated_at']
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance saved successfully',
            'marked_count' => count($records),
        ]);
    }

    public function getReport(Request $request, $batchId)
    {
        abort_if(Gate::denies('batch_attendance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $batch = Batch::with('subject')->findOrFail($batchId);

        if (auth()->user()->isTeacher()) {
            $teacher = auth()->user()->teacher;
            if ($teacher && ! $batch->teachers()->where('teachers.id', $teacher->id)->exists()) {
                abort_if(true, Response::HTTP_FORBIDDEN, '403 Forbidden');
            }
        }

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $attendances = BatchAttendance::where('batch_id', $batchId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get()
            ->groupBy('student_id');

        $students = $batch->students()
            ->wherePivot('enrolled_at', '<=', $endDate)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $reportData = $students->map(function ($student) use ($attendances, $startDate, $endDate) {
            $enrolledAt = $student->pivot->enrolled_at ?? $startDate;
            $effectiveStart = max(Carbon::parse($startDate), Carbon::parse($enrolledAt));

            $studentAttendances = $attendances->get($student->id, collect())
                ->filter(fn ($a) => Carbon::parse($a->attendance_date)->gte($effectiveStart));

            $totalDays = $effectiveStart->diffInDays(Carbon::parse($endDate)) + 1;
            $present = $studentAttendances->where('status', 'present')->count();
            $absent = $studentAttendances->where('status', 'absent')->count();
            $late = $studentAttendances->where('status', 'late')->count();
            $percentage = $totalDays > 0 ? round((($present + $late) / $totalDays) * 100, 1) : 0;

            return [
                'id' => $student->id,
                'name' => trim(($student->first_name ?? '').' '.($student->last_name ?? '')),
                'roll' => $student->roll ?? '',
                'total_days' => $totalDays,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'percentage' => $percentage,
            ];
        });

        return view('admin.batchAttendances.report', compact(
            'batch', 'startDate', 'endDate', 'reportData'
        ));
    }

    public function getStudentDueSummary(Request $request, $batchId, $studentId)
    {
        abort_if(Gate::denies('batch_attendance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $batch = Batch::findOrFail($batchId);

        if (auth()->user()->isTeacher()) {
            $teacher = auth()->user()->teacher;
            if ($teacher && ! $batch->teachers()->where('teachers.id', $teacher->id)->exists()) {
                abort_if(true, Response::HTTP_FORBIDDEN, '403 Forbidden');
            }
        }

        $student = StudentBasicInfo::findOrFail($studentId);

        $isEnrolled = $batch->students()
            ->where('student_basic_infos.id', $studentId)
            ->exists();

        if (! $isEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found in this batch.',
            ], 404);
        }

        $dues = StudentMonthlyDue::where('batch_id', $batchId)
            ->where('student_id', $studentId)
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $totals = [
            'total_due' => (float) $dues->sum('due_amount'),
            'total_paid' => (float) $dues->sum('paid_amount'),
            'total_discount' => (float) $dues->sum('discount_amount'),
            'total_remaining' => (float) $dues->sum('due_remaining'),
        ];

        $items = $dues->map(function ($due) {
            return [
                'month' => $due->month,
                'year' => $due->year,
                'month_name' => $due->month_name,
                'due_amount' => (float) $due->due_amount,
                'paid_amount' => (float) $due->paid_amount,
                'discount_amount' => (float) $due->discount_amount,
                'due_remaining' => (float) $due->due_remaining,
                'status' => $due->status,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'name' => trim(($student->first_name ?? '').' '.($student->last_name ?? '')),
            ],
            'batch' => [
                'id' => $batch->id,
                'name' => $batch->batch_name,
            ],
            'totals' => $totals,
            'items' => $items,
        ]);
    }

    public function calendar(Request $request)
    {
        abort_if(Gate::denies('batch_attendance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $selectedBatchId = $request->input('batch_id');
        $monthInput = $request->input('month', Carbon::now()->format('Y-m'));

        $parsedDate = Carbon::parse($monthInput.'-01');
        $selectedMonth = $parsedDate->month;
        $selectedYear = $parsedDate->year;

        $batches = Batch::with('subject')
            ->withCount([
                'students as students_count' => function ($query) use ($selectedMonth, $selectedYear) {
                    $query->whereMonth('batch_student_basic_info.enrolled_at', $selectedMonth)
                        ->whereYear('batch_student_basic_info.enrolled_at', $selectedYear);
                },
            ]);

        if (auth()->user()->isTeacher()) {
            $teacher = auth()->user()->teacher;
            if ($teacher) {
                $batches = $batches->whereHas('teachers', function ($query) use ($teacher) {
                    $query->where('teachers.id', $teacher->id);
                });
            }
        }

        $batches = $batches->orderBy('batch_name')->get();

        $calendarData = [];
        $totalStudents = 0;

        if ($selectedBatchId) {
            $batch = Batch::with('subject')->find($selectedBatchId);

            if ($batch) {
                $rawSchedule = $batch->getAttributes()['class_schedule'] ?? null;
                $decodedSchedule = $rawSchedule ? json_decode($rawSchedule, true) : null;

                file_put_contents(storage_path('debug_calendar.txt'),
                    "Batch: {$batch->batch_name}\n".
                    "Batch ID: {$selectedBatchId}\n".
                    'Raw: '.var_export($rawSchedule, true)."\n".
                    'Decoded: '.json_encode($decodedSchedule)."\n".
                    "Month: {$selectedMonth}/{$selectedYear}\n"
                );
                $totalStudents = $batch->students()
                    ->whereMonth('batch_student_basic_info.enrolled_at', $selectedMonth)
                    ->whereYear('batch_student_basic_info.enrolled_at', $selectedYear)
                    ->count();

                $startOfMonth = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
                $endOfMonth = $startOfMonth->copy()->endOfMonth();

                $attendances = BatchAttendance::where('batch_id', $selectedBatchId)
                    ->whereBetween('attendance_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                    ->get()
                    ->groupBy('attendance_date');

                for ($day = 1; $day <= $endOfMonth->daysInMonth; $day++) {
                    $date = Carbon::createFromDate($selectedYear, $selectedMonth, $day);
                    $dateStr = $date->format('Y-m-d');
                    $dayName = strtolower($date->format('l'));

                    // Use decoded schedule directly from raw
                    $schedule = $decodedSchedule ?? [];
                    $hasClass = isset($schedule[$dayName]) && ! empty($schedule[$dayName]);

                    $dayAttendances = $attendances->get($dateStr, collect());
                    $present = $dayAttendances->where('status', 'present')->count();
                    $absent = $dayAttendances->where('status', 'absent')->count();
                    $late = $dayAttendances->where('status', 'late')->count();

                    $calendarData[] = [
                        'date' => $dateStr,
                        'day' => $day,
                        'day_name' => $dayName,
                        'has_class' => $hasClass,
                        'total_marked' => $dayAttendances->count(),
                        'present' => $present,
                        'absent' => $absent,
                        'late' => $late,
                        'percentage' => $totalStudents > 0 && $dayAttendances->count() > 0
                            ? round((($present + $late) / $dayAttendances->count()) * 100, 1)
                            : ($hasClass ? 0 : null),
                    ];
                }
            }
        }

        $monthLabel = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->format('F Y');

        return view('admin.batchAttendances.calendar', compact(
            'batches',
            'selectedBatchId',
            'selectedMonth',
            'selectedYear',
            'monthLabel',
            'calendarData',
            'totalStudents'
        ));
    }

    public function view(Request $request)
    {
        abort_if(Gate::denies('batch_attendance_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $selectedBatchId = $request->input('batch_id');

        $monthInput = $request->input('month');
        if ($monthInput && str_contains($monthInput, '-')) {
            $parsed = explode('-', $monthInput);
            $selectedYear = (int) $parsed[0];
            $selectedMonth = (int) $parsed[1];
        } else {
            $selectedMonth = (int) ($monthInput ?: Carbon::now()->month);
            $selectedYear = (int) ($request->input('year', Carbon::now()->year));
        }

        $batches = Batch::with('subject')
            ->orderBy('batch_name')
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'name' => $b->batch_name,
                'subject' => $b->subject?->name ?? 'N/A',
            ]);

        $rows = collect();

        if ($selectedBatchId) {
            $batch = Batch::with('subject')->findOrFail($selectedBatchId);
            $students = $batch->students()
                ->whereMonth('batch_student_basic_info.enrolled_at', $selectedMonth)
                ->whereYear('batch_student_basic_info.enrolled_at', $selectedYear)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();

            $attendances = BatchAttendance::where('batch_id', $selectedBatchId)
                ->whereMonth('attendance_date', $selectedMonth)
                ->whereYear('attendance_date', $selectedYear)
                ->get()
                ->groupBy('student_id');

            foreach ($students as $student) {
                $rows->push($this->buildViewRow($student, $batch, $attendances->get($student->id, collect()), $selectedMonth, $selectedYear));
            }
        } else {
            $batchesWithStudents = Batch::with('subject')
                ->whereHas('students', function ($q) use ($selectedMonth, $selectedYear) {
                    $q->whereMonth('batch_student_basic_info.enrolled_at', $selectedMonth)
                        ->whereYear('batch_student_basic_info.enrolled_at', $selectedYear);
                })
                ->with(['students' => function ($q) use ($selectedMonth, $selectedYear) {
                    $q->whereMonth('batch_student_basic_info.enrolled_at', $selectedMonth)
                        ->whereYear('batch_student_basic_info.enrolled_at', $selectedYear)
                        ->orderBy('first_name')
                        ->orderBy('last_name');
                }])
                ->orderBy('batch_name')
                ->get();

            $allAttendance = BatchAttendance::whereMonth('attendance_date', $selectedMonth)
                ->whereYear('attendance_date', $selectedYear)
                ->get()
                ->groupBy(fn ($a) => $a->batch_id.'_'.$a->student_id);

            foreach ($batchesWithStudents as $batch) {
                foreach ($batch->students as $student) {
                    $key = $batch->id.'_'.$student->id;
                    $rows->push($this->buildViewRow($student, $batch, $allAttendance->get($key, collect()), $selectedMonth, $selectedYear));
                }
            }
        }

        $totalStudents = $rows->count();
        $totalPresent = $rows->sum('present');
        $totalAbsent = $rows->sum('absent');
        $totalLate = $rows->sum('late');
        $avgRate = $totalStudents > 0 ? round($rows->avg('att_rate'), 1) : 0;

        $criticalDrop = $rows->filter(fn ($r) => $r['att_rate'] < 50)->count();
        $topBatch = $rows->groupBy('batch_name')->map(fn ($g) => round($g->avg('att_rate'), 1))->sortDesc()->keys()->first();

        $monthLabel = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->format('F Y');

        return view('admin.batchAttendances.view', compact(
            'rows', 'batches', 'selectedMonth', 'selectedYear', 'selectedBatchId',
            'monthLabel', 'totalStudents', 'totalPresent', 'totalAbsent', 'totalLate',
            'avgRate', 'criticalDrop', 'topBatch'
        ));
    }

    public function viewCompact(Request $request)
    {
        abort_if(Gate::denies('batch_attendance_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $selectedBatchId = $request->input('batch_id');

        $monthInput = $request->input('month');
        if ($monthInput && str_contains($monthInput, '-')) {
            $parsed = explode('-', $monthInput);
            $selectedYear = (int) $parsed[0];
            $selectedMonth = (int) $parsed[1];
        } else {
            $selectedMonth = (int) ($monthInput ?: Carbon::now()->month);
            $selectedYear = (int) ($request->input('year', Carbon::now()->year));
        }

        $batches = Batch::with('subject')
            ->orderBy('batch_name')
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'name' => $b->batch_name,
                'subject' => $b->subject?->name ?? 'N/A',
            ]);

        $rows = collect();

        if ($selectedBatchId) {
            $batch = Batch::with('subject')->findOrFail($selectedBatchId);
            $students = $batch->students()
                ->with('studentDetails')
                ->whereMonth('batch_student_basic_info.enrolled_at', $selectedMonth)
                ->whereYear('batch_student_basic_info.enrolled_at', $selectedYear)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();

            $attendances = BatchAttendance::where('batch_id', $selectedBatchId)
                ->whereMonth('attendance_date', $selectedMonth)
                ->whereYear('attendance_date', $selectedYear)
                ->get()
                ->groupBy('student_id');

            foreach ($students as $student) {
                $rows->push($this->buildViewRow($student, $batch, $attendances->get($student->id, collect()), $selectedMonth, $selectedYear));
            }
        } else {
            $batchesWithStudents = Batch::with('subject')
                ->whereHas('students', function ($q) use ($selectedMonth, $selectedYear) {
                    $q->whereMonth('batch_student_basic_info.enrolled_at', $selectedMonth)
                        ->whereYear('batch_student_basic_info.enrolled_at', $selectedYear);
                })
                ->with(['students' => function ($q) use ($selectedMonth, $selectedYear) {
                    $q->with('studentDetails')
                        ->whereMonth('batch_student_basic_info.enrolled_at', $selectedMonth)
                        ->whereYear('batch_student_basic_info.enrolled_at', $selectedYear)
                        ->orderBy('first_name')
                        ->orderBy('last_name');
                }])
                ->orderBy('batch_name')
                ->get();

            $allAttendance = BatchAttendance::whereMonth('attendance_date', $selectedMonth)
                ->whereYear('attendance_date', $selectedYear)
                ->get()
                ->groupBy(fn ($a) => $a->batch_id.'_'.$a->student_id);

            foreach ($batchesWithStudents as $batch) {
                foreach ($batch->students as $student) {
                    $key = $batch->id.'_'.$student->id;
                    $rows->push($this->buildViewRow($student, $batch, $allAttendance->get($key, collect()), $selectedMonth, $selectedYear));
                }
            }
        }

        $groupedRows = $rows->groupBy('student_id');
        $totalStudents = $groupedRows->count();
        $totalPresent = $rows->sum('present');
        $totalAbsent = $rows->sum('absent');
        $totalLate = $rows->sum('late');
        $avgRate = $totalStudents > 0 ? round($rows->avg('att_rate'), 1) : 0;

        $criticalDrop = $rows->filter(fn ($r) => $r['att_rate'] < 50)->count();
        $topBatch = $rows->groupBy('batch_name')->map(fn ($g) => round($g->avg('att_rate'), 1))->sortDesc()->keys()->first();

        $monthLabel = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->format('F Y');

        return view('admin.batchAttendances.view-compact', compact(
            'groupedRows', 'batches', 'selectedMonth', 'selectedYear', 'selectedBatchId',
            'monthLabel', 'totalStudents', 'totalPresent', 'totalAbsent', 'totalLate',
            'avgRate', 'criticalDrop', 'topBatch'
        ));
    }

    private function buildViewRow($student, $batch, $attendances, $month, $year)
    {
        $attendancesByDay = $attendances->keyBy(fn ($a) => (int) Carbon::parse($a->attendance_date)->format('d'));

        $schedule = $batch->getAttributes()['class_schedule'] ?? null;
        $classDays = $schedule ? array_keys(is_array($schedule) ? $schedule : json_decode($schedule, true) ?? []) : [];

        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $present = 0;
        $absent = 0;
        $late = 0;
        $totalClassDays = 0;
        $daily = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day);
            $dayName = strtolower($date->format('l'));
            $hasClass = in_array($dayName, $classDays);

            if (isset($attendancesByDay[$day])) {
                $status = $attendancesByDay[$day]->status;
                $daily[$day] = $status;
                if ($status === 'present') $present++;
                elseif ($status === 'absent') $absent++;
                elseif ($status === 'late') $late++;
                $totalClassDays++;
            } else {
                $daily[$day] = $hasClass ? 'not_marked' : 'no_class';
                if ($hasClass) $totalClassDays++;
            }
        }

        $attRate = $totalClassDays > 0 ? round(($present + $late) / $totalClassDays * 100, 1) : 0;

        $details = $student->studentDetails;

        return [
            'student_id' => $student->id,
            'student_name' => trim(($student->first_name ?? '').' '.($student->last_name ?? '')),
            'roll' => $student->roll ?? '',
            'id_no' => $student->id_no ?? '',
            'batch_id' => $batch->id,
            'batch_name' => $batch->batch_name,
            'subject' => $batch->subject?->name ?? 'N/A',
            'total_days' => $totalClassDays,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'att_rate' => $attRate,
            'daily' => $daily,
            'fathers_name' => $details->fathers_name ?? '',
            'mothers_name' => $details->mothers_name ?? '',
            'guardian_name' => $details->guardian_name ?? '',
            'guardian_relation' => $details->guardian_relation ?? '',
            'guardian_contact_number' => $details->guardian_contact_number ?? '',
            'student_contact' => $student->contact_number ?? '',
        ];
    }
}
