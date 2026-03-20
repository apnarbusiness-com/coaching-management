<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchAttendance;
use App\Models\StudentBasicInfo;
use App\Models\StudentMonthlyDue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class BatchAttendanceController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('batch_attendance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $batches = Batch::orderBy('batch_name')
            ->get();
            // ->map(function ($batch) {
            //     return [
            //         'id' => $batch->id,
            //         'batch_name' => $batch->batch_name,
            //         'subject_name' => $batch->subject?->name ?? 'N/A',
            //         'student_count' => $batch->students()->wherePivot('enrolled_at', '<=', Carbon::today())->count(),
            //     ];
            // });

            // return  $batches[0]->students;

        return view('admin.batchAttendances.index', compact('batches'));
    }

    public function showAttendanceForm(Request $request, $batchId)
    {
        abort_if(Gate::denies('batch_attendance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $batch = Batch::with('subject')->findOrFail($batchId);
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));

        $students = $batch->students()
            ->with('studentDetails')
            ->wherePivot('enrolled_at', '<=', $date)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $existingAttendances = BatchAttendance::where('batch_id', $batchId)
            ->where('attendance_date', $date)
            ->pluck('status', 'student_id')
            ->toArray();

        $currentMonth = Carbon::parse($date)->month;
        $currentYear = Carbon::parse($date)->year;

        $studentDues = StudentMonthlyDue::whereIn('student_id', $students->pluck('id'))
            ->where('batch_id', $batchId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->where(function ($query) use ($currentMonth, $currentYear) {
                $query->where(function ($q) use ($currentMonth, $currentYear) {
                    $q->where('month', '<', $currentMonth)
                      ->orWhere(function ($q2) use ($currentMonth, $currentYear) {
                          $q2->where('month', '=', $currentMonth)
                             ->where('year', '<=', $currentYear);
                      });
                });
            })
            ->orWhere(function ($query) use ($currentMonth, $currentYear) {
                $query->where('month', $currentMonth)
                      ->where('year', $currentYear)
                      ->whereIn('status', ['unpaid', 'partial']);
            })
            ->get()
            ->groupBy('student_id');

        $formattedStudents = $students->map(function ($student) use ($existingAttendances, $studentDues, $batchId) {
            $dueInfo = $studentDues->get($student->id)?->first();
            $hasDue = $dueInfo !== null && $dueInfo->due_remaining > 0;

            return [
                'id' => $student->id,
                'name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                'roll' => $student->roll ?? '',
                'id_no' => $student->id_no ?? '',
                'image' => $student->image?->thumbnail ?? null,
                'status' => $existingAttendances[$student->id] ?? null,
                'has_due' => $hasDue,
                'due_amount' => $dueInfo?->due_remaining ?? 0,
            ];
        });

        $stats = [
            'total' => $students->count(),
            'present' => collect($existingAttendances)->where(fn($v) => $v === 'present')->count(),
            'absent' => collect($existingAttendances)->where(fn($v) => $v === 'absent')->count(),
            'late' => collect($existingAttendances)->where(fn($v) => $v === 'late')->count(),
            'marked' => count($existingAttendances),
        ];

        return view('admin.batchAttendances.take', compact(
            'batch', 'date', 'formattedStudents', 'stats'
        ));
    }

    public function store(Request $request, $batchId)
    {
        abort_if(Gate::denies('batch_attendance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent,late',
        ]);

        $batch = Batch::findOrFail($batchId);
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
}
