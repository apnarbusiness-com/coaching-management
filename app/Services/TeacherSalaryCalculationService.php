<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\StudentMonthlyDue;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

class TeacherSalaryCalculationService
{
    public function calculateMonthlySalary(int $teacherId, int $month, int $year): float
    {
        $teacher = Teacher::findOrFail($teacherId);
        $totalSalary = 0;

        if ($teacher->salary_type === 'fixed' && $teacher->salary_amount > 0) {
            $totalSalary += (float) $teacher->salary_amount;
        }

        // Get batch assignments for this specific month/year
        $batchTeachers = DB::table('batch_teacher')
            ->where('teacher_id', $teacherId)
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        foreach ($batchTeachers as $bt) {
            $batch = Batch::find($bt->batch_id);
            if (!$batch) continue;

            $batchSalary = $this->calculateBatchTeacherSalary($batch, $month, $year, $bt->salary_amount, $bt->salary_amount_type);
            $totalSalary += $batchSalary;
        }

        return $totalSalary;
    }

    public function calculateBatchTeacherSalary(Batch $batch, int $month, int $year, float $salaryAmount, string $salaryAmountType): float
    {
        if ($salaryAmountType === 'fixed') {
            return (float) $salaryAmount;
        }

        $batchRevenue = $this->getBatchRevenue($batch->id, $month, $year);
        return ($batchRevenue * $salaryAmount) / 100;
    }

    public function getBatchRevenue(int $batchId, int $month, int $year): float
    {
        $monthlyDues = StudentMonthlyDue::where('batch_id', $batchId)
            ->where('month', $month)
            ->where('year', $year)
            ->whereIn('status', ['paid', 'partial'])
            ->get();

        $totalRevenue = 0;
        foreach ($monthlyDues as $due) {
            $totalRevenue += (float) $due->paid_amount;
        }

        $customFees = DB::table('batch_student_basic_info')
            ->where('batch_id', $batchId)
            ->whereMonth('enrolled_at', $month)
            ->whereYear('enrolled_at', $year)
            ->whereNotNull('custom_monthly_fee')
            ->sum('custom_monthly_fee');

        return $totalRevenue + (float) $customFees;
    }

    public function calculateAllTeachersForMonth(int $month, int $year): array
    {
        $teachers = Teacher::where('status', 1)->get();
        $salaries = [];

        foreach ($teachers as $teacher) {
            $salary = $this->calculateMonthlySalary($teacher->id, $month, $year);
            $salaries[] = [
                'teacher_id' => $teacher->id,
                'teacher_name' => $teacher->name,
                'employee_code' => $teacher->emloyee_code,
                'salary_type' => $teacher->salary_type,
                'month' => $month,
                'year' => $year,
                'calculated_salary' => $salary,
            ];
        }

        return $salaries;
    }

    public function getBatchTeacherBreakdown(int $teacherId, int $month, int $year): array
    {
        $teacher = Teacher::findOrFail($teacherId);
        $breakdown = [
            'fixed_salary' => 0,
            'batch_salaries' => [],
            'total' => 0,
        ];

        if ($teacher->salary_type === 'fixed' && $teacher->salary_amount > 0) {
            $breakdown['fixed_salary'] = (float) $teacher->salary_amount;
            $breakdown['total'] += $breakdown['fixed_salary'];
        }

        // Get batch assignments for this specific month/year
        $batchTeachers = DB::table('batch_teacher')
            ->where('teacher_id', $teacherId)
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        foreach ($batchTeachers as $bt) {
            $batch = Batch::find($bt->batch_id);
            if (!$batch) continue;

            $batchRevenue = $this->getBatchRevenue($batch->id, $month, $year);
            $salary = $this->calculateBatchTeacherSalary($batch, $month, $year, $bt->salary_amount, $bt->salary_amount_type);

            $breakdown['batch_salaries'][] = [
                'batch_id' => $batch->id,
                'batch_name' => $batch->batch_name,
                'salary_amount' => $bt->salary_amount,
                'salary_amount_type' => $bt->salary_amount_type,
                'batch_revenue' => $batchRevenue,
                'calculated_salary' => $salary,
            ];

            $breakdown['total'] += $salary;
        }

        return $breakdown;
    }

    public function getBatchTeacherAssignmentsForMonth(int $batchId, int $month, int $year): array
    {
        return DB::table('batch_teacher')
            ->where('batch_id', $batchId)
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->toArray();
    }

    public function getAllBatchTeacherAssignmentsForMonth(int $month, int $year): array
    {
        return DB::table('batch_teacher')
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->toArray();
    }
}
