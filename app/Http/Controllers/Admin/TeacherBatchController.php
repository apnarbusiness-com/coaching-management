<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\StudentMonthlyDue;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class TeacherBatchController extends Controller
{
    private function getBatchExpectedRevenue(int $batchId, int $month, int $year): float
    {
        $batch = Batch::find($batchId);
        if (! $batch) {
            return 0;
        }

        $enrolledStudents = DB::table('batch_student_basic_info')
            ->where('batch_id', $batchId)
            ->whereMonth('enrolled_at', $month)
            ->whereYear('enrolled_at', $year)
            ->get();

        $totalExpected = 0;
        $enrolledStudentsIds = $enrolledStudents->pluck('student_basic_info_id')->toArray();

        $totalExpected = StudentMonthlyDue::whereIn('student_id', $enrolledStudentsIds)
            ->where('batch_id', $batchId)
            ->where('month', $month)
            ->where('year', $year)
            ->sum('due_amount');

        return $totalExpected;
    }

    private function calculateTeacherSalary(float $salaryAmount, string $salaryAmountType, float $batchRevenue): float
    {
        if ($salaryAmountType === 'fixed') {
            return (float) $salaryAmount;
        }

        return ($batchRevenue * $salaryAmount) / 100;
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('teacher_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $batches = Batch::orderBy('batch_name')->get();
        $teachers = Teacher::orderBy('name')->get();

        $teacherBatches = DB::table('batch_teacher')
            ->join('batches', 'batch_teacher.batch_id', '=', 'batches.id')
            ->join('teachers', 'batch_teacher.teacher_id', '=', 'teachers.id')
            ->select(
                'batch_teacher.salary_amount',
                'batch_teacher.salary_amount_type',
                'batch_teacher.role',
                'batch_teacher.month',
                'batch_teacher.year',
                'batches.batch_name',
                'batches.id as batch_id',
                'teachers.name as teacher_name',
                'teachers.emloyee_code',
                'teachers.id as teacher_id'
            )
            ->where('batch_teacher.month', $month)
            ->where('batch_teacher.year', $year)
            ->when($request->batch_id, function ($query) use ($request) {
                return $query->where('batch_teacher.batch_id', $request->batch_id);
            })
            ->when($request->teacher_id, function ($query) use ($request) {
                return $query->where('batch_teacher.teacher_id', $request->teacher_id);
            })
            ->orderBy('batches.batch_name')
            ->orderBy('teachers.name')
            ->get();

        return view('admin.teacherBatch.index', compact('teacherBatches', 'batches', 'teachers', 'month', 'year'));
    }

    public function filter(Request $request)
    {
        abort_if(Gate::denies('teacher_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $teacherBatches = DB::table('batch_teacher')
            ->join('batches', 'batch_teacher.batch_id', '=', 'batches.id')
            ->join('teachers', 'batch_teacher.teacher_id', '=', 'teachers.id')
            ->select(
                'batch_teacher.salary_amount',
                'batch_teacher.salary_amount_type',
                'batch_teacher.role',
                'batch_teacher.month',
                'batch_teacher.year',
                'batches.batch_name',
                'batches.id as batch_id',
                'teachers.name as teacher_name',
                'teachers.emloyee_code',
                'teachers.id as teacher_id'
            )
            ->where('batch_teacher.month', $month)
            ->where('batch_teacher.year', $year)
            ->when($request->batch_id, function ($query) use ($request) {
                return $query->where('batch_teacher.batch_id', $request->batch_id);
            })
            ->when($request->teacher_id, function ($query) use ($request) {
                return $query->where('batch_teacher.teacher_id', $request->teacher_id);
            })
            ->orderBy('batches.batch_name')
            ->orderBy('teachers.name')
            ->get();

        $data = $teacherBatches->map(function ($item) use ($month, $year) {
            $batchRevenue = $this->getBatchExpectedRevenue($item->batch_id, $month, $year);
            $teacherSalary = $this->calculateTeacherSalary(
                (float) $item->salary_amount,
                $item->salary_amount_type,
                $batchRevenue
            );

            // dd($batchRevenue, $teacherSalary);

            return [
                'batch_id' => $item->batch_id,
                'batch_name' => $item->batch_name,
                'teacher_id' => $item->teacher_id,
                'teacher_name' => $item->teacher_name,
                'emloyee_code' => $item->emloyee_code,
                'role' => $item->role,
                'salary_amount' => $item->salary_amount,
                'salary_amount_type' => $item->salary_amount_type,
                'batch_revenue' => $batchRevenue,
                'teacher_salary' => $teacherSalary,
            ];
        });




        return response()->json([
            'data' => $data,
            'month' => $month,
            'year' => $year,
        ]);
    }
}
