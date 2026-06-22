<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\AcademicClass;
use App\Models\Batch;
use App\Models\BatchAttendance;
use App\Models\Earning;
use App\Models\EarningCategory;
use App\Models\Section;
use App\Models\Shift;
use App\Models\StudentBasicInfo;
use App\Models\StudentDetailsInformation;
use App\Models\StudentFlag;
use App\Models\StudentMonthlyDue;
use App\Models\StudentOtherDue;
use App\Models\CashBook;
use App\Models\CashBookTransaction;
use App\Models\EarningTransaction;
use App\Services\DueCalculationService;
use App\Services\TeacherSalaryCalculationService;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class DueCollectionController extends Controller
{
    protected $dueService;
    protected $salaryService;

    public function __construct(DueCalculationService $dueService, TeacherSalaryCalculationService $salaryService)
    {
        $this->dueService = $dueService;
        $this->salaryService = $salaryService;
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('due_collection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $status = $request->input('status', '');
        $batchId = $request->input('batch_id', '');
        $classId = $request->input('class_id', '');
        $sectionId = $request->input('section_id', '');

        $stats = $this->dueService->getDashboardStats($month, $year);

        if ($request->ajax()) {
            $query = StudentMonthlyDue::with(['student', 'batch', 'academicClass', 'section'])
                ->forMonth($month, $year)
                ->select(sprintf('%s.*', (new StudentMonthlyDue)->table));

            if ($batchId) {
                $query->where('batch_id', $batchId);
            }
            if ($classId) {
                $query->where('academic_class_id', $classId);
            }
            if ($sectionId) {
                $query->where('section_id', $sectionId);
            }
            if ($status) {
                $query->where('status', $status);
            }

            $table = DataTables::of($query);
            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                return '<button type="button" class="btn btn-xs btn-primary pay-btn" data-id="' . $row->id . '" data-due-amount="' . $row->due_amount . '" data-remaining="' . $row->due_remaining . '">Pay</button>';
            });

            $table->addColumn('student_name', fn($row) => $row->student->first_name . ' ' . $row->student->last_name);
            $table->addColumn('student_id_no', fn($row) => $row->student->id_no ?? '');
            $table->addColumn('batch_name', fn($row) => $row->batch->batch_name ?? '');
            $table->addColumn('class_name', fn($row) => $row->academicClass->class_name ?? '');
            $table->addColumn('section_name', fn($row) => $row->section->section_name ?? '');
            $table->addColumn('month_year', fn($row) => $this->getMonthName($row->month) . ' ' . $row->year);
            $table->editColumn('due_amount', fn($row) => number_format($row->due_amount, 2));
            $table->editColumn('paid_amount', fn($row) => number_format($row->paid_amount, 2));
            $table->editColumn('due_remaining', fn($row) => number_format($row->due_remaining, 2));
            $table->editColumn('status', function ($row) {
                if ($row->due_amount == 0 && $row->paid_amount == 0) {
                    return '<span class="badge bg-warning">Free</span>';
                }
                return $this->getStatusBadge($row->status);
            });

            $table->rawColumns(['actions', 'status', 'placeholder']);
            $table->setRowAttr(['data-entry-id' => fn($row) => $row->id]);

            return $table->make(true);
        }

        $batches = Batch::pluck('batch_name', 'id');
        $classes = AcademicClass::pluck('class_name', 'id');
        $sections = Section::pluck('section_name', 'id');
        $cashBooks = CashBook::where('is_financial_account', true)->orderBy('order')->orderBy('title')->get();

        return view('admin.dueCollections.index', compact(
            'stats',
            'month',
            'year',
            'status',
            'batchId',
            'classId',
            'sectionId',
            'batches',
            'classes',
            'sections',
            'cashBooks'
        ));
    }

    public function summary(Request $request)
    {
        abort_if(Gate::denies('due_collection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $status = $request->input('status', 'unpaid');
        $batchId = $request->input('batch_id', '');

        $stats = $this->dueService->getDashboardStats($month, $year, $batchId, $status);
        $filteredStats = $this->dueService->getFilteredStats($month, $year, $batchId, '', $status);

        if ($request->ajax()) {
            $query = StudentMonthlyDue::forMonth($month, $year)
                ->join('student_basic_infos', 'student_monthly_dues.student_id', '=', 'student_basic_infos.id')
                ->leftJoin('student_details_informations', 'student_basic_infos.id', '=', 'student_details_informations.student_id')
                ->selectRaw("
                    student_monthly_dues.student_id,
                    CONCAT_WS(' ', student_basic_infos.first_name, student_basic_infos.last_name) as student_name,
                    student_basic_infos.id_no as student_id_no,
                    student_basic_infos.contact_number as student_contact,
                    student_details_informations.guardian_contact_number as guardian_contact,
                    SUM(student_monthly_dues.due_amount) as total_due,
                    SUM(student_monthly_dues.paid_amount) as total_paid,
                    SUM(student_monthly_dues.due_remaining) as total_remaining,
                    SUM(student_monthly_dues.discount_amount) as total_discount,
                    COUNT(student_monthly_dues.id) as due_count,
                    SUM(CASE WHEN student_monthly_dues.status IN ('paid','free') THEN 1 ELSE 0 END) as paid_count,
                    SUM(CASE WHEN student_monthly_dues.status = 'partial' THEN 1 ELSE 0 END) as partial_count,
                    SUM(CASE WHEN student_monthly_dues.status = 'unpaid' THEN 1 ELSE 0 END) as unpaid_count
                ")
                ->groupBy('student_monthly_dues.student_id', 'student_basic_infos.first_name', 'student_basic_infos.last_name', 'student_basic_infos.id_no', 'student_basic_infos.contact_number', 'student_details_informations.guardian_contact_number');

            if ($batchId) {
                $query->where('student_monthly_dues.batch_id', $batchId);
            }
            if ($status) {
                $query->where('student_monthly_dues.status', $status);
            }

            return DataTables::of($query)
                ->addColumn('placeholder', '&nbsp;')
                ->addColumn('actions', function ($row) {
                    return '<button type="button" class="btn btn-xs btn-primary view-details-btn" data-student-id="' . $row->student_id . '">View Details</button>';
                })
                ->editColumn('total_due', fn($row) => number_format($row->total_due, 2))
                ->editColumn('total_paid', fn($row) => number_format($row->total_paid, 2))
                ->editColumn('total_remaining', fn($row) => number_format($row->total_remaining, 2))
                ->editColumn('total_discount', fn($row) => number_format($row->total_discount, 2))
                ->filterColumn('student_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT_WS(' ', student_basic_infos.first_name, student_basic_infos.last_name) LIKE ?", ["%{$keyword}%"]);
                })
                ->rawColumns(['actions', 'placeholder'])
                ->make(true);
        }

        $batches = Batch::pluck('batch_name', 'id');

        return view('admin.dueCollections.summary', compact(
            'stats',
            'filteredStats',
            'month',
            'year',
            'status',
            'batchId',
            'batches'
        ));
    }

    public function getStudentDueSummary($studentId)
    {
        abort_if(Gate::denies('due_collection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $month = request('month', Carbon::now()->month);
        $year = request('year', Carbon::now()->year);

        $dues = StudentMonthlyDue::with('batch')
            ->where('student_id', $studentId)
            ->where('year', $year)
            ->when($month !== 'all', fn($q) => $q->where('month', $month))
            ->orderBy('batch_id')
            ->get()
            ->map(function ($due) {
                return [
                    'id' => $due->id,
                    'batch_id' => $due->batch_id,
                    'batch_name' => $due->batch->batch_name ?? 'N/A',
                    'month' => $due->month,
                    'year' => $due->year,
                    'month_name' => $this->getMonthName($due->month),
                    'due_amount' => (float) $due->due_amount,
                    'paid_amount' => (float) $due->paid_amount,
                    'due_remaining' => (float) $due->due_remaining,
                    'discount_amount' => (float) $due->discount_amount,
                    'status' => $due->status,
                    'status_badge' => $due->due_amount == 0 && $due->paid_amount == 0
                        ? '<span class="badge bg-warning">Free</span>'
                        : $this->getStatusBadge($due->status),
                ];
            });

        $student = StudentBasicInfo::with('studentDetails')->find($studentId);
        $studentName = $student ? $student->first_name . ' ' . $student->last_name : 'Unknown';
        $studentIdNo = $student->id_no ?? '';

        return response()->json([
            'student_name' => $studentName,
            'student_id_no' => $studentIdNo,
            'student_contact' => $student->contact_number ?? '',
            'guardian_contact' => $student->studentDetails->guardian_contact_number ?? '',
            'dues' => $dues,
        ]);
    }

    public function generateDues(Request $request)
    {
        abort_if(Gate::denies('due_collection_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $results = $this->dueService->generateMonthlyDues($month, $year);

        return back()->with('message', "Generated: {$results['monthly_generated']} monthly, {$results['course_generated']} course-wise. Skipped: {$results['skipped']}");
    }

    public function getStudentDues($studentId)
    {
        $dues = StudentMonthlyDue::where('student_id', $studentId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->with('batch')
            ->get()
            ->map(function ($due) {
                $pivot = DB::table('batch_student_basic_info')
                    ->where('batch_id', $due->batch_id)
                    ->where('student_basic_info_id', $due->student_id)
                    ->whereMonth('enrolled_at', '<=', $due->month)
                    ->whereYear('enrolled_at', '<=', $due->year)
                    ->orderBy('enrolled_at', 'desc')
                    ->first();

                $due->pivot_permanent_discount = $pivot->per_student_discount ?? 0;
                $due->pivot_one_time_discount = $pivot->one_time_discount ?? 0;

                return $due;
            });

        return response()->json($dues);
    }

    public function payDue(Request $request)
    {
        // Allow if user has either due_collection_access or due_collection_create
        if (Gate::denies('due_collection_access') && Gate::denies('due_collection_create')) {
            abort_if(true, Response::HTTP_FORBIDDEN, '403 Forbidden');
        }

        $request->validate([
            'due_id' => 'required|exists:student_monthly_dues,id',
            'amount' => 'required|numeric|min:0',
            'one_time_discount' => 'nullable|numeric|min:0',
            'cash_book_id' => 'required|integer|exists:cash_books,id',
        ]);

        $due = StudentMonthlyDue::with('batch')->findOrFail($request->input('due_id'));

        $oneTimeDiscount = (float) ($request->input('one_time_discount') ?? 0);

        if ($oneTimeDiscount > 0) {
            DB::table('batch_student_basic_info')
                ->where('batch_id', $due->batch_id)
                ->where('student_basic_info_id', $due->student_id)
                ->whereMonth('enrolled_at', $due->month)
                ->whereYear('enrolled_at', $due->year)
                ->update([
                    'one_time_discount' => $oneTimeDiscount,
                ]);

            $pivot = DB::table('batch_student_basic_info')
                ->where('batch_id', $due->batch_id)
                ->where('student_basic_info_id', $due->student_id)
                ->whereMonth('enrolled_at', '<=', $due->month)
                ->whereYear('enrolled_at', '<=', $due->year)
                ->orderBy('enrolled_at', 'desc')
                ->first();

            $permanentDiscount = $pivot->per_student_discount ?? 0;
            $newTotalDiscount = $permanentDiscount + $oneTimeDiscount;
            $newDueAmount = max(0, $due->batch->fee_amount - $newTotalDiscount);

            $due->update([
                'due_amount' => $newDueAmount,
                'discount_amount' => $newTotalDiscount,
                'due_remaining' => max(0, $newDueAmount - $due->paid_amount),
            ]);

            if ($due->paid_amount >= $newDueAmount) {
                $status = $due->paid_amount > 0 ? 'paid' : 'free';
                $due->update([
                    'status' => $status,
                    'paid_date' => now()->format('Y-m-d'),
                ]);
            } elseif ($due->paid_amount > 0) {
                $due->update([
                    'status' => 'partial',
                ]);
            }
        }

        $amount = (float) $request->input('amount');

        if ($amount > $due->due_remaining) {
            $amount = $due->due_remaining;
        }

        $this->dueService->allocatePayment($due, $amount);

        $due->refresh();


        // $earningCategory = EarningCategory::where('is_student_connected', 1)->first();
        $earningCategory = EarningCategory::where('is_student_connected', 1)
            ->where('name', 'Coaching Fees')
            ->first();

        if (!$earningCategory) {
            $earningCategory = EarningCategory::create([
                'name' => 'Coaching Fees',
                'is_student_connected' => 1,
            ]);
        }

        $cashBook = CashBook::findOrFail($request->input('cash_book_id'));

        $receiptNumber = $this->generateReceiptNo();

        $tx = EarningTransaction::create([
            'receipt_no' => $receiptNumber,
            'student_id' => $due->student_id,
            'total_amount' => $amount,
            'payment_method' => $cashBook->title,
            'cash_book_id' => $cashBook->id,
            'payment_date' => now(),
            'total_items' => 1,
            'created_by_id' => auth()->id(),
        ]);

        $batchName = $due->batch->batch_name ?? 'N/A';
        $isPartial = $due->due_remaining > 0;
        $monthName = $this->getMonthName($due->month) . ' ' . $due->year;
        $title = $isPartial ? "Due Payment (partial) - $batchName - $monthName" : "Due Payment - $batchName - $monthName";

        $details = "Batch: $batchName | Due Amount: " . number_format($due->due_amount, 2) . " | Month: $monthName | Paid: " . number_format($amount, 2) . " | Remaining: " . number_format($due->due_remaining, 2);

        $studentName = $due->student->first_name . ' ' . $due->student->last_name . ' (' . ($due->student->id_no ?? 'N/A') . ')';
        $paymentStatus = $due->due_remaining > 0 ? 'Partial' : 'Full';

        $earning = Earning::create([
            'earning_transaction_id' => $tx->id,
            'earning_category_id' => $earningCategory?->id,
            'student_id' => $due->student_id,
            'batch_id' => $due->batch_id,
            'cash_book_id' => $cashBook->id,
            'title' => $title,
            'amount' => $amount,
            'details' => $details,
            'earning_date' => now(),
            'earning_month' => $due->month,
            'earning_year' => $due->year,
            'payment_method' => $cashBook->title,
            'paid_by' => $due->student->first_name . ' ' . $due->student->last_name,
            'recieved_by' => auth()->user()->name,
            'created_by_id' => auth()->id(),
            'student_monthly_due_id' => $due->id,
            'earning_reference' => $receiptNumber,
        ]);

        $oldAmount = $cashBook->amount;
        $newAmount = $oldAmount + $amount;
        $cashBook->update(['amount' => $newAmount]);

        CashBookTransaction::create([
            'cash_book_id' => $cashBook->id,
            'old_amount' => $oldAmount,
            'new_amount' => $newAmount,
            'action_type' => 'student_payment_added',
            'note' => "Student {$studentName} paid Tk " . number_format($amount, 2) . " for {$due->batch->batch_name} - {$monthName} ({$paymentStatus})",
            'created_by_id' => auth()->id(),
        ]);

        $this->salaryService->recalculatePercentageSalaries($due->batch_id, $due->month, $due->year);

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully',
            'receipt' => [
                'transaction_id' => $tx->id,
                'receipt_no' => $tx->receipt_no,
                'student_name' => trim(($due->student->first_name ?? '') . ' ' . ($due->student->last_name ?? '')),
                'student_id_no' => $due->student->id_no ?? '',
                'payment_date' => now()->format('d M, Y'),
                'total' => (float) $amount,
                'payment_method' => $cashBook->title,
                'items' => [[
                    'month' => $this->getMonthName($due->month),
                    'year' => $due->year,
                    'batch' => $batchName,
                    'amount' => (float) $amount,
                ]],
            ],
        ]);
    }

    public function payAllDues(Request $request)
    {
        if (Gate::denies('due_collection_access') && Gate::denies('due_collection_create')) {
            abort_if(true, Response::HTTP_FORBIDDEN, '403 Forbidden');
        }

        $request->validate([
            'student_id' => 'required|exists:student_basic_infos,id',
            'amount' => 'required|numeric|min:0',
            'one_time_discount' => 'nullable|numeric|min:0',
            'one_time_discount_batch_id' => 'nullable|exists:batches,id',
            'one_time_discount_month' => 'nullable|integer|min:1|max:12',
            'one_time_discount_year' => 'nullable|integer|min:2000',
            'cash_book_id' => 'required|integer|exists:cash_books,id',
        ]);

        $studentId = $request->input('student_id');
        $totalAmount = (float) $request->input('amount');
        $oneTimeDiscount = (float) ($request->input('one_time_discount') ?? 0);

        if ($oneTimeDiscount > 0) {
            $batchId = $request->input('one_time_discount_batch_id');
            $discountMonth = $request->input('one_time_discount_month');
            $discountYear = $request->input('one_time_discount_year');

            if ($batchId && $discountMonth && $discountYear) {
                DB::table('batch_student_basic_info')
                    ->where('batch_id', $batchId)
                    ->where('student_basic_info_id', $studentId)
                    ->whereMonth('enrolled_at', $discountMonth)
                    ->whereYear('enrolled_at', $discountYear)
                    ->update([
                        'one_time_discount' => $oneTimeDiscount,
                    ]);

                $batch = Batch::find($batchId);
                if ($batch) {
                    $pivot = DB::table('batch_student_basic_info')
                        ->where('batch_id', $batchId)
                        ->where('student_basic_info_id', $studentId)
                        ->whereMonth('enrolled_at', '<=', $discountMonth)
                        ->whereYear('enrolled_at', '<=', $discountYear)
                        ->orderBy('enrolled_at', 'desc')
                        ->first();

                    $permanentDiscount = $pivot->per_student_discount ?? 0;
                    $newTotalDiscount = $permanentDiscount + $oneTimeDiscount;
                    $newDueAmount = max(0, $batch->fee_amount - $newTotalDiscount);

                    $due = StudentMonthlyDue::where('student_id', $studentId)
                        ->where('batch_id', $batchId)
                        ->where('month', $discountMonth)
                        ->where('year', $discountYear)
                        ->first();

                    if ($due) {
                        $due->update([
                            'due_amount' => $newDueAmount,
                            'discount_amount' => $newTotalDiscount,
                            'due_remaining' => max(0, $newDueAmount - $due->paid_amount),
                        ]);

                        if ($due->paid_amount >= $newDueAmount) {
                            $status = $due->paid_amount > 0 ? 'paid' : 'free';
                            $due->update([
                                'status' => $status,
                                'paid_date' => now()->format('Y-m-d'),
                            ]);
                        } elseif ($due->paid_amount > 0) {
                            $due->update([
                                'status' => 'partial',
                            ]);
                        }
                    }
                }
            }
        }

        $dues = StudentMonthlyDue::where('student_id', $studentId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        if ($dues->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No unpaid dues found for this student'], 400);
        }

        $cashBook = CashBook::findOrFail($request->input('cash_book_id'));

        $student = StudentBasicInfo::find($studentId);
        $studentName = $student ? trim($student->first_name . ' ' . $student->last_name) . ' (' . ($student->id_no ?? 'N/A') . ')' : 'Unknown';

        $remainingAmount = $totalAmount;
        $paidDues = [];
        $createdEarnings = [];
        $recalculatedBatches = [];

        // Create a single transaction header for the entire bulk payment
        $receiptNo = $this->generateReceiptNo();
        $tx = EarningTransaction::create([
            'receipt_no' => $receiptNo,
            'student_id' => $studentId,
            'total_amount' => 0, // will update after loop
            'payment_method' => $cashBook->title,
            'cash_book_id' => $cashBook->id,
            'payment_date' => now(),
            'total_items' => 0, // will update after loop
            'created_by_id' => auth()->id(),
        ]);

        foreach ($dues as $due) {
            if ($remainingAmount <= 0) {
                break;
            }

            $payAmount = min($remainingAmount, $due->due_remaining);

            $this->dueService->allocatePayment($due, $payAmount);
            $due->refresh();

            $earningCategory = EarningCategory::where('is_student_connected', 1)
                ->where('name', 'Coaching Fees')
                ->first();

            if (!$earningCategory) {
                $earningCategory = EarningCategory::create([
                    'name' => 'Coaching Fees',
                    'is_student_connected' => 1,
                ]);
            }

            $batchName = $due->batch->batch_name ?? 'N/A';
            $isPartial = $due->due_remaining > 0;
            $monthName = $this->getMonthName($due->month) . ' ' . $due->year;
            $title = $isPartial ? "Due Payment (partial) - $batchName - $monthName" : "Due Payment - $batchName - $monthName";

            $details = "Batch: $batchName | Due Amount: " . number_format($due->due_amount, 2) . " | Month: $monthName | Paid: " . number_format($payAmount, 2) . " | Remaining: " . number_format($due->due_remaining, 2);

            $earning = Earning::create([
                'earning_transaction_id' => $tx->id,
                'earning_category_id' => $earningCategory?->id,
                'student_id' => $due->student_id,
                'batch_id' => $due->batch_id,
                'cash_book_id' => $cashBook->id,
                'title' => $title,
                'amount' => $payAmount,
                'details' => $details,
                'earning_date' => now(),
                'earning_month' => $due->month,
                'earning_year' => $due->year,
                'payment_method' => $cashBook->title,
                'paid_by' => $due->student->first_name . ' ' . $due->student->last_name,
                'recieved_by' => auth()->user()->name,
                'created_by_id' => auth()->id(),
                'student_monthly_due_id' => $due->id,
                'earning_reference' => $receiptNo,
            ]);

            $paidDues[] = [
                'due_id' => $due->id,
                'month_name' => $monthName,
                'batch_name' => $batchName,
                'paid_amount' => $payAmount,
                'remaining' => $due->due_remaining,
                'status' => $due->status,
            ];

            $createdEarnings[] = $earning->id;
            $remainingAmount -= $payAmount;

            $recalcKey = $due->batch_id . '-' . $due->month . '-' . $due->year;
            if (!in_array($recalcKey, $recalculatedBatches)) {
                $this->salaryService->recalculatePercentageSalaries($due->batch_id, $due->month, $due->year);
                $recalculatedBatches[] = $recalcKey;
            }
        }

        $totalPaid = $totalAmount - $remainingAmount;

        // Update transaction totals
        $tx->update([
            'total_amount' => $totalPaid,
            'total_items' => count($paidDues),
        ]);

        if ($totalPaid > 0) {
            $dueDetails = [];
            foreach ($paidDues as $pd) {
                $dueDetails[] = $pd['batch_name'] . ' - ' . $pd['month_name'] . ' (' . $pd['status'] . ')';
            }

            $oldAmount = $cashBook->amount;
            $newAmount = $oldAmount + $totalPaid;
            $cashBook->update(['amount' => $newAmount]);

            CashBookTransaction::create([
                'cash_book_id' => $cashBook->id,
                'old_amount' => $oldAmount,
                'new_amount' => $newAmount,
                'action_type' => 'student_payment_added',
                'note' => "Student {$studentName} paid Tk " . number_format($totalPaid, 2) . ": " . implode(', ', $dueDetails),
                'created_by_id' => auth()->id(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully',
            'total_paid' => $totalPaid,
            'remaining_to_pay' => $remainingAmount,
            'paid_dues' => $paidDues,
            'receipt' => [
                'transaction_id' => $tx->id,
                'receipt_no' => $tx->receipt_no,
                'student_name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                'student_id_no' => $student->id_no ?? '',
                'payment_date' => now()->format('d M, Y'),
                'total' => (float) $totalPaid,
                'payment_method' => $cashBook->title,
                'items' => array_map(function ($pd) {
                    return [
                        'month' => $pd['month_name'],
                        'year' => '',
                        'batch' => $pd['batch_name'],
                        'amount' => (float) $pd['paid_amount'],
                    ];
                }, $paidDues),
            ],
        ]);
    }

    public function getStudentList(Request $request)
    {
        $search = $request->term;

        $students = StudentBasicInfo::where(function ($query) use ($search) {
            $query->where('first_name', 'LIKE', "%$search%")
                ->orWhere('last_name', 'LIKE', "%$search%")
                ->orWhere('id_no', 'LIKE', "%$search%");
        })
            ->limit(10)
            ->get();

        $formatted = [];
        foreach ($students as $student) {
            $dueInfo = $this->dueService->calculateStudentTotalDue($student->id);
            $formatted[] = [
                'id' => $student->id,
                'text' => ($student->first_name ?? '') . ' ' . ($student->last_name ?? '') . ' (' . ($student->id_no ?? '') . ')',
                'total_due' => $dueInfo['total_due'],
            ];
        }

        return response()->json($formatted);
    }

    protected function getMonthName($month)
    {
        return Carbon::createFromDate(null, $month, 1)->format('F');
    }

    protected function getStatusBadge($status)
    {
        return match ($status) {
            'paid' => '<span class="badge bg-success">Paid</span>',
            'free' => '<span class="badge bg-warning">Free</span>',
            'partial' => '<span class="badge bg-warning">Partial</span>',
            'unpaid' => '<span class="badge bg-danger">Unpaid</span>',
            default => $status,
        };
    }

    protected function generateReceiptNo()
    {
        $year = date('Y');
        $count = EarningTransaction::whereYear('payment_date', $year)->count();
        return 'REC-' . $year . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }

    public function collectOtherDue(Request $request)
    {
        abort_if(Gate::denies('due_collection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'other_due_id' => 'required|integer|exists:student_other_dues,id',
            'amount' => 'required|numeric|min:0.01',
            'cash_book_id' => 'required|integer|exists:cash_books,id',
        ]);

        $otherDue = StudentOtherDue::findOrFail($data['other_due_id']);
        $cashBook = CashBook::findOrFail($data['cash_book_id']);

        $payAmount = min($data['amount'], $otherDue->amount - $otherDue->paid_amount);
        if ($payAmount <= 0) {
            return response()->json(['error' => 'No pending amount to collect.'], 400);
        }

        $receiptNo = $this->generateReceiptNo();

        $tx = EarningTransaction::create([
            'receipt_no' => $receiptNo,
            'student_id' => $otherDue->student_id,
            'total_amount' => $payAmount,
            'payment_method' => $otherDue->payment_method ?? 'cash',
            'cash_book_id' => $cashBook->id,
            'payment_date' => now(),
            'total_items' => 1,
            'created_by_id' => auth()->id(),
        ]);

        $earning = Earning::create([
            'earning_transaction_id' => $tx->id,
            'earning_category_id' => $otherDue->earning_category_id,
            'student_id' => $otherDue->student_id,
            'batch_id' => $otherDue->batch_id,
            'subject_id' => $otherDue->subject_id,
            'cash_book_id' => $cashBook->id,
            'title' => $otherDue->title,
            'academic_background' => $otherDue->academic_background,
            'exam_year' => $otherDue->exam_year,
            'details' => $otherDue->details,
            'amount' => $payAmount,
            'earning_date' => now(),
            'earning_month' => now()->month,
            'earning_year' => now()->year,
            'earning_reference' => $receiptNo,
            'payment_method' => $otherDue->payment_method ?? 'cash',
            'payment_proof_details' => $otherDue->payment_proof_details,
            'paid_by' => $otherDue->paid_by,
            'recieved_by' => auth()->user()->name,
            'created_by_id' => auth()->id(),
        ]);

        $oldAmount = $cashBook->amount;
        $newAmount = $oldAmount + $payAmount;
        $cashBook->update(['amount' => $newAmount]);

        CashBookTransaction::create([
            'cash_book_id' => $cashBook->id,
            'old_amount' => $oldAmount,
            'new_amount' => $newAmount,
            'action_type' => 'earning_added',
            'note' => "Other Due collection '{$otherDue->title}' of Tk " . number_format($payAmount, 2) . " added.",
            'created_by_id' => auth()->id(),
        ]);

        $newPaid = $otherDue->paid_amount + $payAmount;
        $newStatus = $newPaid >= $otherDue->amount ? 'paid' : 'partial';
        $otherDue->update([
            'paid_amount' => $newPaid,
            'status' => $newStatus,
            'earning_id' => $earning->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment collected successfully. Receipt: ' . $receiptNo,
            'earning_id' => $earning->id,
            'receipt' => [
                'transaction_id' => $tx->id,
                'receipt_no' => $tx->receipt_no,
                'student_name' => $otherDue->student->first_name ?? '',
                'student_id_no' => $otherDue->student->id_no ?? '',
                'payment_date' => now()->format('d M, Y'),
                'total' => (float) $payAmount,
                'payment_method' => $otherDue->payment_method ?? 'cash',
                'items' => [[
                    'month' => now()->format('F'),
                    'year' => now()->year,
                    'batch' => $otherDue->batch->batch_name ?? 'N/A',
                    'amount' => (float) $payAmount,
                ]],
            ],
            'other_due' => [
                'id' => $otherDue->id,
                'paid_amount' => (float) $newPaid,
                'status' => $newStatus,
                'due_remaining' => (float) ($otherDue->amount - $newPaid),
            ],
        ]);
    }

    public function checker(Request $request)
    {
        abort_if(Gate::denies('due_collection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 2, $currentYear + 1);
        $cashBooks = CashBook::where('is_financial_account', true)->orderBy('order')->orderBy('title')->get();
        $defaultCashBook = CashBook::where('is_financial_account', true)->where('is_default', true)->first();

        return view('admin.dueCollections.checker', compact('years', 'currentYear', 'cashBooks', 'defaultCashBook'));
    }

    public function searchStudentsForChecker(Request $request)
    {
        abort_if(Gate::denies('due_collection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $search = $request->term;

        $students = StudentBasicInfo::leftJoin('student_details_informations', 'student_basic_infos.id', '=', 'student_details_informations.student_id')
            ->leftJoin('users', 'student_basic_infos.user_id', '=', 'users.id')
            ->where(function ($query) use ($search) {
                $query->where('student_basic_infos.first_name', 'LIKE', "%$search%")
                    ->orWhere('student_basic_infos.last_name', 'LIKE', "%$search%")
                    ->orWhere('student_basic_infos.id_no', 'LIKE', "%$search%")
                    ->orWhere('users.admission_id', 'LIKE', "%$search%")
                    ->orWhere('student_details_informations.fathers_name', 'LIKE', "%$search%")
                    ->orWhere('student_details_informations.mothers_name', 'LIKE', "%$search%");
            })
            ->select([
                'student_basic_infos.id',
                'student_basic_infos.first_name',
                'student_basic_infos.last_name',
                'student_basic_infos.id_no',
                'users.admission_id',
                'student_details_informations.fathers_name',
                'student_details_informations.mothers_name',
            ])
            ->limit(20)
            ->get();

        $formatted = [];
        foreach ($students as $student) {
            $formatted[] = [
                'id' => $student->id,
                'text' => ($student->first_name ?? '') . ' ' . ($student->last_name ?? '') . ' - ' . ($student->id_no ?? $student->admission_id ?? 'N/A'),
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'id_no' => $student->id_no,
                'admission_id' => $student->admission_id,
                'fathers_name' => $student->fathers_name,
                'mothers_name' => $student->mothers_name,
            ];
        }

        return response()->json($formatted);
    }

    public function getStudentFullHistory(Request $request, $studentId)
    {
        abort_if(Gate::denies('due_collection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $year = $request->input('year', Carbon::now()->year);

        $student = StudentBasicInfo::with(['studentDetails', 'class', 'media'])
            ->leftJoin('users', 'student_basic_infos.user_id', '=', 'users.id')
            ->where('student_basic_infos.id', $studentId)
            ->select([
                'student_basic_infos.*',
                'users.admission_id',
            ])
            ->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $query = StudentMonthlyDue::where('student_id', $studentId);
        if ($year !== 'all') {
            $query->where('year', $year);
        }
        $dues = $query->with('batch')
            ->orderByRaw("CASE WHEN status IN ('unpaid', 'partial') AND NOT (due_amount = 0 AND paid_amount = 0) THEN 0 ELSE 1 END")
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $dueSummary = [
            'total_due' => (float) $dues->sum('due_amount'),
            'total_paid' => (float) $dues->sum('paid_amount'),
            'total_discount' => (float) $dues->sum('discount_amount'),
            'total_remaining' => (float) $dues->sum('due_remaining'),
        ];

        $dueHistory = $dues->map(function ($due) {
            $pivot = DB::table('batch_student_basic_info')
                ->where('batch_id', $due->batch_id)
                ->where('student_basic_info_id', $due->student_id)
                ->whereMonth('enrolled_at', '<=', $due->month)
                ->whereYear('enrolled_at', '<=', $due->year)
                ->orderBy('enrolled_at', 'desc')
                ->first();

            return [
                'id' => $due->id,
                'batch_id' => $due->batch_id,
                'month' => $due->month,
                'year' => $due->year,
                'month_name' => $this->getMonthName($due->month),
                'batch_name' => $due->batch->batch_name ?? 'N/A',
                'due_amount' => (float) $due->due_amount,
                'paid_amount' => (float) $due->paid_amount,
                'discount_amount' => (float) $due->discount_amount,
                'pivot_permanent_discount' => $pivot->per_student_discount ?? 0,
                'pivot_one_time_discount' => $pivot->one_time_discount ?? 0,
                'due_remaining' => (float) $due->due_remaining,
                'status' => $due->due_amount == 0 && $due->paid_amount == 0 ? 'free' : $due->status,
            ];
        })->values();

        $earningQuery = Earning::where('student_id', $studentId);
        if ($year !== 'all') {
            $earningQuery->where('earning_year', $year);
        }
        $earnings = $earningQuery->orderBy('earning_date', 'desc')->get();

        $paymentHistory = $earnings->map(function ($earning) {
            return [
                'date' => $earning->earning_date,
                'batch_name' => $earning->batch->batch_name ?? 'N/A',
                'amount' => (float) $earning->amount,
                'received_by' => $earning->recieved_by,
                'reference' => $earning->earning_reference,
                'title' => $earning->title,
                'earning_transaction_id' => $earning->earning_transaction_id,
                'receipt_no' => $earning->earningTransaction?->receipt_no,
            ];
        })->values();

        $batches = Batch::whereHas('students', function ($query) use ($studentId) {
            $query->where('student_basic_infos.id', $studentId);
        })->with(['subject', 'class'])->get();

        $activeBatches = $batches->map(function ($batch) use ($studentId) {
            $pivot = DB::table('batch_student_basic_info')
                ->where('batch_id', $batch->id)
                ->where('student_basic_info_id', $studentId)
                ->first();

            return [
                'id' => $batch->id,
                'batch_name' => $batch->batch_name,
                'subject_name' => $batch->subject->name ?? 'N/A',
                'class_name' => $batch->class->class_name ?? 'N/A',
                'enrolled_at' => $pivot->enrolled_at ?? null,
                'fee_type' => $batch->fee_type,
                'fee_amount' => (float) $batch->fee_amount,
            ];
        })->values();

        $attendanceData = [];
        foreach ($batches as $batch) {
            $attendances = BatchAttendance::where('batch_id', $batch->id)
                ->where('student_id', $studentId);

            if ($year !== 'all') {
                $attendances->whereYear('attendance_date', $year);
            }

            $attendances = $attendances->get();

            $present = $attendances->where('status', 'present')->count();
            $absent = $attendances->where('status', 'absent')->count();
            $late = $attendances->where('status', 'late')->count();
            $total = $present + $absent + $late;
            $percentage = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;

            $attendanceData[] = [
                'batch_id' => $batch->id,
                'batch_name' => $batch->batch_name,
                'total_days' => $total,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'percentage' => $percentage,
            ];
        }

        $flagData = $student->flags()->withPivot('comment')->get()->map(function ($flag) {
            return [
                'id' => $flag->id,
                'name' => $flag->name,
                'color' => $flag->color,
                'comment' => $flag->pivot->comment,
            ];
        })->values();

        $generalComment = DB::table('student_flag_assignments')
            ->where('student_basic_info_id', $studentId)
            ->whereNull('student_flag_id')
            ->first();

        if ($generalComment?->comment) {
            $flagData->push([
                'id' => null,
                'name' => 'General Comment',
                'color' => '#6c757d',
                'comment' => $generalComment->comment,
            ]);
        }

        $otherDuesQuery = StudentOtherDue::where('student_id', $studentId);
        if ($year !== 'all') {
            $otherDuesQuery->whereYear('created_at', $year);
        }
        $otherDues = $otherDuesQuery->orderBy('created_at', 'desc')->get()->map(function ($due) {
            return [
                'id' => $due->id,
                'title' => $due->title,
                'category' => $due->earningCategory->name ?? 'N/A',
                'amount' => (float) $due->amount,
                'paid_amount' => (float) $due->paid_amount,
                'due_remaining' => (float) ($due->amount - $due->paid_amount),
                'status' => $due->status,
                'created_at' => $due->created_at->toDateTimeString(),
            ];
        })->values();

        return response()->json([
            'student' => [
                'id' => $student->id,
                'name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                'id_no' => $student->id_no,
                'admission_id' => $student->admission_id,
                'class_name' => $student->class->class_name ?? 'N/A',
                'fathers_name' => $student->studentDetails->fathers_name ?? 'N/A',
                'mothers_name' => $student->studentDetails->mothers_name ?? 'N/A',
                'contact_number' => $student->contact_number,
                'guardian_contact_number' => $student->studentDetails->guardian_contact_number ?? 'N/A',
                'image' => $student->image?->thumbnail ?? null,
            ],
            'due_summary' => $dueSummary,
            'due_history' => $dueHistory,
            'payment_history' => $paymentHistory,
            'active_batches' => $activeBatches,
            'attendance_analysis' => $attendanceData,
            'flags' => $flagData,
            'other_dues' => $otherDues,
        ]);
    }

    public function receipt(EarningTransaction $earningTransaction, $output = 'pdf')
    {
        abort_if(Gate::denies('due_collection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $earningTransaction->load(['student', 'earnings.batch', 'createdBy']);

        if ($output === 'html') {
            return view('admin.dueCollections.receipt-pdf', compact('earningTransaction'));
        }

        $pdf = Pdf::loadView('admin.dueCollections.receipt-pdf', compact('earningTransaction'));
        return $pdf->download('receipt-' . $earningTransaction->receipt_no . '.pdf');
    }
}
