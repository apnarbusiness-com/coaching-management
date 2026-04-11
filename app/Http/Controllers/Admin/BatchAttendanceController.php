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
                }
            ]);

        if (auth()->user()->roles()->whereRaw('LOWER(title) = ?', ['teacher'])->exists()) {
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

        if (auth()->user()->roles()->whereRaw('LOWER(title) = ?', ['teacher'])->exists()) {
            $teacher = auth()->user()->teacher;
            if ($teacher && !$batch->teachers()->where('teachers.id', $teacher->id)->exists()) {
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

        $formattedStudents = $students->map(function ($student) use ($existingAttendances, $studentTotalDues) {
            $totalDueRemaining = (float) ($studentTotalDues[$student->id] ?? 0);
            $hasDue = $totalDueRemaining > 0;

            return [
                'id' => $student->id,
                'name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                'roll' => $student->roll ?? '',
                'id_no' => $student->id_no ?? '',
                'image' => $student->image?->thumbnail ?? null,
                'status' => $existingAttendances[$student->id] ?? null,
                'has_due' => $hasDue,
                'due_amount' => $totalDueRemaining,
            ];
        });

        $stats = [
            'total' => $students->count(),
            'present' => collect($existingAttendances)->where(fn($v) => $v === 'present')->count(),
            'absent' => collect($existingAttendances)->where(fn($v) => $v === 'absent')->count(),
            'late' => collect($existingAttendances)->where(fn($v) => $v === 'late')->count(),
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

        if (auth()->user()->roles()->whereRaw('LOWER(title) = ?', ['teacher'])->exists()) {
            $teacher = auth()->user()->teacher;
            if ($teacher && !$batch->teachers()->where('teachers.id', $teacher->id)->exists()) {
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

        if (auth()->user()->roles()->whereRaw('LOWER(title) = ?', ['teacher'])->exists()) {
            $teacher = auth()->user()->teacher;
            if ($teacher && !$batch->teachers()->where('teachers.id', $teacher->id)->exists()) {
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
                ->filter(fn($a) => Carbon::parse($a->attendance_date)->gte($effectiveStart));
            
            $totalDays = $effectiveStart->diffInDays(Carbon::parse($endDate)) + 1;
            $present = $studentAttendances->where('status', 'present')->count();
            $absent = $studentAttendances->where('status', 'absent')->count();
            $late = $studentAttendances->where('status', 'late')->count();
            $percentage = $totalDays > 0 ? round((($present + $late) / $totalDays) * 100, 1) : 0;

            return [
                'id' => $student->id,
                'name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
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

        if (auth()->user()->roles()->whereRaw('LOWER(title) = ?', ['teacher'])->exists()) {
            $teacher = auth()->user()->teacher;
            if ($teacher && !$batch->teachers()->where('teachers.id', $teacher->id)->exists()) {
                abort_if(true, Response::HTTP_FORBIDDEN, '403 Forbidden');
            }
        }

        $student = StudentBasicInfo::findOrFail($studentId);

        $isEnrolled = $batch->students()
            ->where('student_basic_infos.id', $studentId)
            ->exists();

        if (!$isEnrolled) {
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
                'name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
            ],
            'batch' => [
                'id' => $batch->id,
                'name' => $batch->batch_name,
            ],
            'totals' => $totals,
            'items' => $items,
        ]);
    }
}
