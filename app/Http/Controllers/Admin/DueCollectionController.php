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
use App\Services\DueCalculationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class DueCollectionController extends Controller
{
    protected $dueService;

    public function __construct(DueCalculationService $dueService)
    {
        $this->dueService = $dueService;
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('due_collection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $stats = $this->dueService->getDashboardStats($month, $year);

        if ($request->ajax()) {
            $query = StudentMonthlyDue::with(['student', 'batch', 'academicClass', 'section'])
                ->forMonth($month, $year)
                ->select(sprintf('%s.*', (new StudentMonthlyDue)->table));

            if ($request->input('batch_id')) {
                $query->where('batch_id', $request->input('batch_id'));
            }
            if ($request->input('class_id')) {
                $query->where('academic_class_id', $request->input('class_id'));
            }
            if ($request->input('section_id')) {
                $query->where('section_id', $request->input('section_id'));
            }
            if ($request->input('status')) {
                $query->where('status', $request->input('status'));
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
            $table->editColumn('status', fn($row) => $this->getStatusBadge($row->status));

            $table->rawColumns(['actions', 'status', 'placeholder']);
            $table->setRowAttr(['data-entry-id' => fn($row) => $row->id]);

            return $table->make(true);
        }

        $batches = Batch::pluck('batch_name', 'id');
        $classes = AcademicClass::pluck('class_name', 'id');
        $sections = Section::pluck('section_name', 'id');

        return view('admin.dueCollections.index', compact(
            'stats',
            'month',
            'year',
            'batches',
            'classes',
            'sections'
        ));
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
                $due->update([
                    'status' => 'paid',
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

        $receiptNumber = 'REC-' . date('Y') . '-' . str_pad(Earning::whereYear('earning_date', date('Y'))->count() + 1, 3, '0', STR_PAD_LEFT);

        $batchName = $due->batch->batch_name ?? 'N/A';
        $isPartial = $due->due_remaining > 0;
        $monthName = $this->getMonthName($due->month) . ' ' . $due->year;
        $title = $isPartial ? "Due Payment (partial) - $batchName - $monthName" : "Due Payment - $batchName - $monthName";

        $details = "Batch: $batchName | Due Amount: " . number_format($due->due_amount, 2) . " | Month: $monthName | Paid: " . number_format($amount, 2) . " | Remaining: " . number_format($due->due_remaining, 2);

        // return response()->json(['success' => true, 'due' => $due]);
        Earning::create([
            'earning_category_id' => $earningCategory?->id,
            'student_id' => $due->student_id,
            'batch_id' => $due->batch_id,
            'title' => $title,
            'amount' => $amount,
            'details' => $details,
            'earning_date' => now(),
            'earning_month' => $due->month,
            'earning_year' => $due->year,
            'paid_by' => $due->student->first_name . ' ' . $due->student->last_name,
            'recieved_by' => auth()->user()->name,
            'created_by_id' => auth()->id(),
            'student_monthly_due_id' => $due->id,
            'earning_reference' => $receiptNumber,
        ]);

        return response()->json(['success' => true, 'message' => 'Payment recorded successfully']);
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
                            $due->update([
                                'status' => 'paid',
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

        $remainingAmount = $totalAmount;
        $paidDues = [];
        $createdEarnings = [];

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

            $receiptNumber = 'REC-' . date('Y') . '-' . str_pad(Earning::whereYear('earning_date', date('Y'))->count() + 1, 3, '0', STR_PAD_LEFT);

            $batchName = $due->batch->batch_name ?? 'N/A';
            $isPartial = $due->due_remaining > 0;
            $monthName = $this->getMonthName($due->month) . ' ' . $due->year;
            $title = $isPartial ? "Due Payment (partial) - $batchName - $monthName" : "Due Payment - $batchName - $monthName";

            $details = "Batch: $batchName | Due Amount: " . number_format($due->due_amount, 2) . " | Month: $monthName | Paid: " . number_format($payAmount, 2) . " | Remaining: " . number_format($due->due_remaining, 2);

            $earning = Earning::create([
                'earning_category_id' => $earningCategory?->id,
                'student_id' => $due->student_id,
                'batch_id' => $due->batch_id,
                'title' => $title,
                'amount' => $payAmount,
                'details' => $details,
                'earning_date' => now(),
                'earning_month' => $due->month,
                'earning_year' => $due->year,
                'paid_by' => $due->student->first_name . ' ' . $due->student->last_name,
                'recieved_by' => auth()->user()->name,
                'created_by_id' => auth()->id(),
                'student_monthly_due_id' => $due->id,
                'earning_reference' => $receiptNumber,
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
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully',
            'total_paid' => $totalAmount - $remainingAmount,
            'remaining_to_pay' => $remainingAmount,
            'paid_dues' => $paidDues,
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
            'partial' => '<span class="badge bg-warning">Partial</span>',
            'unpaid' => '<span class="badge bg-danger">Unpaid</span>',
            default => $status,
        };
    }

    public function checker(Request $request)
    {
        abort_if(Gate::denies('due_collection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 2, $currentYear + 1);

        return view('admin.dueCollections.checker', compact('years', 'currentYear'));
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
        $dues = $query->with('batch')->orderBy('year')->orderBy('month')->get();

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
                'status' => $due->status,
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
                'image' => $student->image?->thumbnail ?? null,
            ],
            'due_summary' => $dueSummary,
            'due_history' => $dueHistory,
            'payment_history' => $paymentHistory,
            'active_batches' => $activeBatches,
            'attendance_analysis' => $attendanceData,
            'flags' => $flagData,
        ]);
    }
}
