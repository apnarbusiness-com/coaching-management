<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\StudentBasicInfo;
use App\Models\StudentMonthlyDue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DueCalculationService
{
    protected $dueDateDay = 10;

    public function generateMonthlyDues(int $month = null, int $year = null): array
    {
        $month = $month ?? Carbon::now()->month;
        $year = $year ?? Carbon::now()->year;

        $results = [
            'monthly_generated' => 0,
            'course_generated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        $batches = Batch::all();

        foreach ($batches as $batch) {
            $pivots = DB::table('batch_student_basic_info')
                ->where('batch_id', $batch->id)
                ->whereMonth('enrolled_at', $month)
                ->whereYear('enrolled_at', $year)
                ->get();

            if ($pivots->isEmpty()) {
                continue;
            }

            $studentsById = StudentBasicInfo::whereIn('id', $pivots->pluck('student_basic_info_id')->all())
                ->get()
                ->keyBy('id');

            foreach ($pivots as $pivot) {
                $student = $studentsById->get($pivot->student_basic_info_id);

                if (!$student) {
                    $results['skipped']++;
                    continue;
                }
                try {
                    $existingDue = StudentMonthlyDue::where('student_id', $student->id)
                        ->where('batch_id', $batch->id)
                        ->where('month', $month)
                        ->where('year', $year)
                        ->first();

                    if ($existingDue) {
                        $results['skipped']++;
                        continue;
                    }

                    $dueAmount = $this->calculateDueAmount($batch, $pivot, $student);

                    $dueDate = Carbon::createFromDate($year, $month, $this->dueDateDay);

                    $dueRecord = StudentMonthlyDue::create([
                        'student_id' => $student->id,
                        'batch_id' => $batch->id,
                        'academic_class_id' => $student->class_id,
                        'section_id' => $student->section_id,
                        'shift_id' => $student->shift_id,
                        'month' => $month,
                        'year' => $year,
                        'due_amount' => $dueAmount,
                        'paid_amount' => 0,
                        'discount_amount' => $pivot->per_student_discount ?? 0,
                        'due_remaining' => $dueAmount,
                        'status' => 'unpaid',
                        'due_date' => $dueDate->format('Y-m-d'),
                    ]);

                    if ($batch->fee_type === 'course') {
                        $results['course_generated']++;
                    } else {
                        $results['monthly_generated']++;
                    }

                } catch (\Exception $e) {
                    $results['errors'][] = "Student ID {$student->id}: " . $e->getMessage();
                }
            }
        }

        return $results;
    }

    public function calculateDueAmount(Batch $batch, $pivot, StudentBasicInfo $student): float
    {
        $discount = $pivot->per_student_discount ?? 0;
        $customFee = $pivot->custom_monthly_fee;

        if ($customFee !== null) {
            return max(0, $customFee - $discount);
        }

        if ($batch->fee_type === 'course' && $batch->duration_in_months) {
            $monthlyFee = $batch->fee_amount / $batch->duration_in_months;
            return max(0, $monthlyFee - $discount);
        }

        return max(0, $batch->fee_amount - $discount);
    }

    public function calculateStudentTotalDue(int $studentId): array
    {
        $dues = StudentMonthlyDue::where('student_id', $studentId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->get();

        return [
            'total_due' => $dues->sum('due_remaining'),
            'unpaid_months' => $dues->count(),
            'dues' => $dues,
        ];
    }

    public function allocatePayment(StudentMonthlyDue $due, float $amount): void
    {
        $newPaid = $due->paid_amount + $amount;
        $due->paid_amount = $newPaid;
        $due->due_remaining = max(0, $due->due_amount - $newPaid);

        if ($due->due_remaining <= 0) {
            $due->status = 'paid';
            $due->paid_date = now()->format('Y-m-d');
        } elseif ($newPaid > 0) {
            $due->status = 'partial';
        }

        $due->save();
    }

    public function getDashboardStats(int $month = null, int $year = null): array
    {
        $month = $month ?? Carbon::now()->month;
        $year = $year ?? Carbon::now()->year;

        $query = StudentMonthlyDue::forMonth($month, $year);

        return [
            'total_due' => $query->sum('due_amount'),
            'total_collected' => $query->sum('paid_amount'),
            'total_remaining' => $query->sum('due_remaining'),
            'paid_count' => $query->where('status', 'paid')->count(),
            'partial_count' => $query->where('status', 'partial')->count(),
            'unpaid_count' => $query->where('status', 'unpaid')->count(),
            'total_students' => $query->distinct('student_id')->count('student_id'),
        ];
    }
}
