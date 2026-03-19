<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicClass;
use App\Models\Batch;
use App\Models\Earning;
use App\Models\EarningCategory;
use App\Models\Section;
use App\Models\Shift;
use App\Models\StudentBasicInfo;
use App\Models\StudentMonthlyDue;
use App\Services\DueCalculationService;
use Carbon\Carbon;
// use Illuminate\Support\Facades\Gate;
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
                return '<button type="button" class="btn btn-xs btn-primary pay-btn" data-id="'.$row->id.'" data-due-amount="'.$row->due_amount.'" data-remaining="'.$row->due_remaining.'">Pay</button>';
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
            'stats', 'month', 'year', 'batches', 'classes', 'sections'
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
            ->get();

        return response()->json($dues);
    }

    public function payDue(Request $request)
    {
        abort_if(Gate::denies('due_collection_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'due_id' => 'required|exists:student_monthly_dues,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $due = StudentMonthlyDue::with('batch')->findOrFail($request->input('due_id'));
        
        $amount = (float) $request->input('amount');

        if ($amount > $due->due_remaining) {
            $amount = $due->due_remaining;
        }

        $this->dueService->allocatePayment($due, $amount);

        $due->refresh();
        
        
        $earningCategory = EarningCategory::where('is_student_connected', 1)->first();
        
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
        return match($status) {
            'paid' => '<span class="badge bg-success">Paid</span>',
            'partial' => '<span class="badge bg-warning">Partial</span>',
            'unpaid' => '<span class="badge bg-danger">Unpaid</span>',
            default => $status,
        };
    }
}
