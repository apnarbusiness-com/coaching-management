<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchAttendance;
use App\Models\StudentMonthlyDue;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class TeacherApiController extends Controller
{
    public function myBatches(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isTeacher()) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN,
                'message' => 'Only teachers can access this resource',
                'errors' => [],
            ], Response::HTTP_FORBIDDEN);
        }

        $teacher = $user->teacher;
        if (!$teacher) {
            return response()->json([
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'Teacher profile not found',
                'errors' => [],
            ], Response::HTTP_NOT_FOUND);
        }

        $batches = Batch::whereHas('teachers', function ($query) use ($teacher) {
            $query->where('teachers.id', $teacher->id);
        })
        ->where('status', 1)
        ->with(['subject'])
        ->get()
        ->map(function ($batch) {
            return [
                'id' => $batch->id,
                'batch_name' => $batch->batch_name,
                'subject' => $batch->subject->name ?? 'N/A',
                'students_count' => $batch->students()->count(),
            ];
        });

        return response()->json([
            'code' => Response::HTTP_OK,
            'message' => 'Batches retrieved successfully',
            'data' => $batches,
        ], Response::HTTP_OK);
    }

    public function batchStudents(Request $request, int $batchId): JsonResponse
    {
        $user = $request->user();

        if (!$user->isTeacher()) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN,
                'message' => 'Only teachers can access this resource',
                'errors' => [],
            ], Response::HTTP_FORBIDDEN);
        }

        $teacher = $user->teacher;
        $batch = Batch::whereHas('teachers', function ($query) use ($teacher) {
            $query->where('teachers.id', $teacher->id);
        })->find($batchId);

        if (!$batch) {
            return response()->json([
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'Batch not found or not assigned to you',
                'errors' => [],
            ], Response::HTTP_NOT_FOUND);
        }

        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $attendanceDate = Carbon::parse($date)->format('Y-m-d');

        $students = $batch->students()
            ->with('studentDetails')
            ->orderBy('roll')
            ->orderBy('first_name')
            ->get();

        $existingAttendances = BatchAttendance::where('batch_id', $batchId)
            ->where('attendance_date', $attendanceDate)
            ->pluck('status', 'student_id')
            ->toArray();

        $studentDues = StudentMonthlyDue::where('batch_id', $batchId)
            ->whereIn('student_id', $students->pluck('id'))
            ->whereIn('status', ['unpaid', 'partial'])
            ->select('student_id')
            ->selectRaw('SUM(due_remaining) as due_remaining')
            ->groupBy('student_id')
            ->pluck('due_remaining', 'student_id');

        $formattedStudents = $students->map(function ($student) use ($existingAttendances, $studentDues) {
            $dueAmount = (float) ($studentDues[$student->id] ?? 0);
            return [
                'id' => $student->id,
                'name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                'roll' => $student->roll ?? '',
                'id_no' => $student->id_no ?? '',
                'image' => $student->image?->thumbnail ?? null,
                'status' => $existingAttendances[$student->id] ?? null,
                'due_amount' => $dueAmount,
                'has_due' => $dueAmount > 0,
            ];
        });

        return response()->json([
            'code' => Response::HTTP_OK,
            'message' => 'Students retrieved successfully',
            'data' => [
                'batch' => [
                    'id' => $batch->id,
                    'batch_name' => $batch->batch_name,
                ],
                'date' => $attendanceDate,
                'students' => $formattedStudents,
            ],
        ], Response::HTTP_OK);
    }

    public function markAttendance(Request $request, int $batchId): JsonResponse
    {
        $user = $request->user();

        if (!$user->isTeacher()) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN,
                'message' => 'Only teachers can access this resource',
                'errors' => [],
            ], Response::HTTP_FORBIDDEN);
        }

        $teacher = $user->teacher;
        $batch = Batch::whereHas('teachers', function ($query) use ($teacher) {
            $query->where('teachers.id', $teacher->id);
        })->find($batchId);

        if (!$batch) {
            return response()->json([
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'Batch not found or not assigned to you',
                'errors' => [],
            ], Response::HTTP_NOT_FOUND);
        }

        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $attendanceDate = Carbon::parse($date);

        $today = Carbon::today();
        $minDate = $today->copy()->subDays(30);

        if ($attendanceDate->lt($minDate) || $attendanceDate->gt($today)) {
            return response()->json([
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Attendance can only be marked for today or up to 30 days back',
                'errors' => [
                    'date' => ['Date must be within the last 30 days'],
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent,late',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $attendanceData = $request->input('attendance', []);
        $dateStr = $attendanceDate->format('Y-m-d');

        $records = [];
        foreach ($attendanceData as $studentId => $status) {
            $records[] = [
                'batch_id' => $batchId,
                'student_id' => $studentId,
                'attendance_date' => $dateStr,
                'status' => $status,
                'recorded_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($records)) {
            BatchAttendance::upsert(
                $records,
                ['batch_id', 'student_id', 'attendance_date'],
                ['status', 'recorded_by', 'updated_at']
            );
        }

        return response()->json([
            'code' => Response::HTTP_OK,
            'message' => 'Attendance saved successfully',
            'data' => [
                'marked_count' => count($records),
                'date' => $dateStr,
            ],
        ], Response::HTTP_OK);
    }

    public function viewAttendance(Request $request, int $batchId): JsonResponse
    {
        $user = $request->user();

        if (!$user->isTeacher()) {
            return response()->json([
                'code' => Response::HTTP_FORBIDDEN,
                'message' => 'Only teachers can access this resource',
                'errors' => [],
            ], Response::HTTP_FORBIDDEN);
        }

        $teacher = $user->teacher;
        $batch = Batch::whereHas('teachers', function ($query) use ($teacher) {
            $query->where('teachers.id', $teacher->id);
        })->find($batchId);

        if (!$batch) {
            return response()->json([
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'Batch not found or not assigned to you',
                'errors' => [],
            ], Response::HTTP_NOT_FOUND);
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $date = $request->input('date');

        if ($date) {
            $attendances = BatchAttendance::where('batch_id', $batchId)
                ->where('attendance_date', $date)
                ->get();

            $students = $batch->students()->orderBy('roll')->get();
            $studentMap = $students->mapWithKeys(function ($s) {
                return [$s->id => trim(($s->first_name ?? '') . ' ' . ($s->last_name ?? ''))];
            });

            $formatted = $attendances->map(function ($att) use ($studentMap) {
                return [
                    'student_id' => $att->student_id,
                    'student_name' => $studentMap[$att->student_id] ?? 'Unknown',
                    'status' => $att->status,
                    'attendance_date' => $att->attendance_date,
                ];
            });

            return response()->json([
                'code' => Response::HTTP_OK,
                'message' => 'Attendance retrieved successfully',
                'data' => [
                    'date' => $date,
                    'attendances' => $formatted,
                ],
            ], Response::HTTP_OK);
        }

        if ($startDate && $endDate) {
            $attendances = BatchAttendance::where('batch_id', $batchId)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->orderBy('attendance_date')
                ->get()
                ->groupBy('attendance_date');

            $summary = [];
            foreach ($attendances as $dateKey => $records) {
                $summary[] = [
                    'date' => $dateKey,
                    'present' => $records->where('status', 'present')->count(),
                    'absent' => $records->where('status', 'absent')->count(),
                    'late' => $records->where('status', 'late')->count(),
                ];
            }

            return response()->json([
                'code' => Response::HTTP_OK,
                'message' => 'Attendance retrieved successfully',
                'data' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'summary' => $summary,
                ],
            ], Response::HTTP_OK);
        }

        return response()->json([
            'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'message' => 'Please provide date or date range',
            'errors' => [
                'date' => ['Required: ?date=2026-04-26'],
                'start_date' => ['Optional: ?start_date=2026-04-01&end_date=2026-04-30'],
            ],
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}