<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTeachersPaymentRequest;
use App\Http\Requests\StoreTeachersPaymentRequest;
use App\Http\Requests\UpdateTeachersPaymentRequest;
use App\Models\Teacher;
use App\Models\TeachersPayment;
use App\Models\TeacherPaymentTransaction;
use App\Services\TeacherSalaryCalculationService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeachersPaymentController extends Controller
{
    protected $salaryService;

    public function __construct()
    {
        $this->salaryService = new TeacherSalaryCalculationService();
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('teachers_payment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = TeachersPayment::with(['teacher', 'transactions']);

        if ($request->filled('month') && $request->filled('year')) {
            $query->where('month', $request->month)->where('year', $request->year);
        }

        $teachersPayments = $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('admin.teachersPayments.index', compact('teachersPayments'));
    }

    public function create()
    {
        abort_if(Gate::denies('teachers_payment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teachers = Teacher::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.teachersPayments.create', compact('teachers'));
    }

    public function store(StoreTeachersPaymentRequest $request)
    {
        $teachersPayment = TeachersPayment::create($request->all());

        return redirect()->route('admin.teachers-payments.index');
    }

    public function edit(TeachersPayment $teachersPayment)
    {
        abort_if(Gate::denies('teachers_payment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teachers = Teacher::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $teachersPayment->load('teacher');

        return view('admin.teachersPayments.edit', compact('teachers', 'teachersPayment'));
    }

    public function update(UpdateTeachersPaymentRequest $request, TeachersPayment $teachersPayment)
    {
        $teachersPayment->update($request->all());

        return redirect()->route('admin.teachers-payments.index');
    }

    public function show(TeachersPayment $teachersPayment)
    {
        abort_if(Gate::denies('teachers_payment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teachersPayment->load(['teacher', 'transactions']);

        $breakdown = [];
        if ($teachersPayment->month && $teachersPayment->year) {
            $breakdown = $this->salaryService->getBatchTeacherBreakdown(
                $teachersPayment->teacher_id,
                $teachersPayment->month,
                $teachersPayment->year
            );
        }

        return view('admin.teachersPayments.show', compact('teachersPayment', 'breakdown'));
    }

    public function destroy(TeachersPayment $teachersPayment)
    {
        abort_if(Gate::denies('teachers_payment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teachersPayment->delete();

        return back();
    }

    public function massDestroy(MassDestroyTeachersPaymentRequest $request)
    {
        $teachersPayments = TeachersPayment::find(request('ids'));

        foreach ($teachersPayments as $teachersPayment) {
            $teachersPayment->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function generate(Request $request)
    {
        abort_if(Gate::denies('teachers_payment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);



        $month = (int) $request->month;
        $year = (int) $request->year;

        $existingPayments = TeachersPayment::where('month', $month)
            ->where('year', $year)
            ->pluck('teacher_id')
            ->toArray();

        return response()->json([
            'status'    => 'success',
            'data'      => $request->all(),
            'month'     => $month,
            'year'      => $year,
            'existingPayments' => $existingPayments
        ]);

        $salaries = $this->salaryService->calculateAllTeachersForMonth($month, $year);
        $created = 0;
        $skipped = 0;

        foreach ($salaries as $salaryData) {
            if (in_array($salaryData['teacher_id'], $existingPayments)) {
                $skipped++;
                continue;
            }

            if ($salaryData['calculated_salary'] <= 0) {
                $skipped++;
                continue;
            }

            TeachersPayment::create([
                'teacher_id' => $salaryData['teacher_id'],
                'month' => $month,
                'year' => $year,
                'amount' => $salaryData['calculated_salary'],
                'payment_details' => json_encode([
                    'salary_type' => $salaryData['salary_type'],
                    'calculated_from' => 'auto',
                ]),
                'payment_status' => 'due',
            ]);
            $created++;
        }

        return redirect()->route('admin.teachers-payments.index', ['month' => $month, 'year' => $year])
            ->with('status', "Generated {$created} salary records. Skipped {$skipped} (already exist or zero salary).");
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'teacher_id' => ['required', 'exists:teachers,id'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $salary = $this->salaryService->calculateMonthlySalary(
            $request->teacher_id,
            $request->month,
            $request->year
        );

        $breakdown = $this->salaryService->getBatchTeacherBreakdown(
            $request->teacher_id,
            $request->month,
            $request->year
        );

        return response()->json([
            'salary' => $salary,
            'breakdown' => $breakdown,
        ]);
    }

    public function storeTransaction(Request $request, TeachersPayment $teachersPayment)
    {
        abort_if(Gate::denies('teachers_payment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'string', 'in:cash,bank_transfer,mobile_banking'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $transaction = TeacherPaymentTransaction::create([
            'teachers_payment_id' => $teachersPayment->id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'reference' => $request->reference,
            'notes' => $request->notes,
            'created_by_id' => auth()->id(),
        ]);

        $teachersPayment->updatePaymentStatus();

        return back()->with('status', 'Payment transaction added successfully.');
    }

    public function destroyTransaction(TeachersPayment $teachersPayment, TeacherPaymentTransaction $transaction)
    {
        abort_if(Gate::denies('teachers_payment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($transaction->teachers_payment_id !== $teachersPayment->id) {
            abort(403, 'Transaction does not belong to this payment.');
        }

        $transaction->delete();

        $teachersPayment->updatePaymentStatus();

        return back()->with('status', 'Transaction deleted successfully.');
    }
}
