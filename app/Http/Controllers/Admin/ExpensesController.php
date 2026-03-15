<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyExpenseRequest;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
// use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use function PHPUnit\Framework\returnArgument;

class ExpensesController extends Controller
{
    use MediaUploadingTrait;

    /**
     * @var array<string, \App\Models\ExpenseCategory>
     */
    protected array $expenseCategoryCache = [];

    /**
     * @var array<string, int|null>
     */
    protected array $teacherIdCache = [];

    public function index(Request $request)
    {
        abort_if(Gate::denies('expense_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Expense::with(['expense_category', 'created_by', 'updated_by', 'teacher'])
                ->select(sprintf('%s.*', (new Expense)->table));

            $categoryId = $request->input('category_id');
            $month = $request->input('month');
            $year = $request->input('year');

            if (!empty($categoryId)) {
                $query->where('expense_category_id', $categoryId);
            }
            if (!empty($month)) {
                $query->whereMonth('expense_date', $month);
            }
            if (!empty($year)) {
                $query->whereYear('expense_date', $year);
            }

            $table = DataTables::of($query);
            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'expense_show';
                $editGate = 'expense_edit';
                $deleteGate = 'expense_delete';
                $crudRoutePart = 'expenses';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', fn($row) => $row->id ?? '');
            $table->editColumn('title', fn($row) => $row->title ?? '');
            $table->editColumn('amount', fn($row) => $row->amount ?? '');
            $table->editColumn('expense_date', fn($row) => $row->expense_date ?? '');
            $table->editColumn('paid_by', fn($row) => $row->paid_by ?? '');
            $table->addColumn('teacher_name', fn($row) => $row->teacher->name ?? '');

            $table->rawColumns(['actions', 'placeholder']);
            $table->setRowAttr([
                'data-entry-id' => fn($row) => $row->id,
            ]);

            return $table->make(true);
        }

        $expense_categories = ExpenseCategory::pluck('name', 'id');

        return view('admin.expenses.index', compact('expense_categories'));
    }

    public function summary(Request $request)
    {
        abort_if(Gate::denies('expense_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categoryId = $request->input('category_id');
        $month = $request->input('month');
        $year = $request->input('year');
        $summaryYear = !empty($year) ? (int) $year : (int) date('Y');

        $query = Expense::query()
            ->whereNotNull('expense_date')
            ->whereYear('expense_date', $summaryYear);

        if (!empty($categoryId)) {
            $query->where('expense_category_id', $categoryId);
        }
        if (!empty($month)) {
            $query->whereMonth('expense_date', $month);
        }

        $totals = $query
            ->selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
            ->groupBy(DB::raw('MONTH(expense_date)'))
            ->pluck('total', 'month');

        $result = [];
        for ($m = 1; $m <= 12; $m++) {
            $result[] = [
                'month' => $m,
                'total' => (float) ($totals[$m] ?? 0),
            ];
        }

        return response()->json([
            'year' => $summaryYear,
            'totals' => $result,
        ]);
    }

    public function create()
    {
        abort_if(Gate::denies('expense_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expense_categories = ExpenseCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $expense_category_flags = ExpenseCategory::pluck('is_teacher_connected', 'id');
        // return $expense_categories;
        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $updated_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $teachers = Teacher::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.expenses.create', compact('created_bies', 'expense_categories', 'expense_category_flags', 'teachers', 'updated_bies'));
    }

    public function store(StoreExpenseRequest $request)
    {
        $expense = Expense::create($request->all());

        foreach ($request->input('payment_proof', []) as $file) {
            $expense->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('payment_proof');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $expense->id]);
        }

        return redirect()->route('admin.expenses.index');
    }

    public function edit(Expense $expense)
    {
        abort_if(Gate::denies('expense_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expense_categories = ExpenseCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $expense_category_flags = ExpenseCategory::pluck('is_teacher_connected', 'id');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $updated_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $teachers = Teacher::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $expense->load('expense_category', 'created_by', 'updated_by', 'teacher', 'media');

        return view('admin.expenses.edit', compact('created_bies', 'expense', 'expense_categories', 'expense_category_flags', 'teachers', 'updated_bies'));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        $expense->update($request->all());

        if (count($expense->payment_proof) > 0) {
            foreach ($expense->payment_proof as $media) {
                if (!in_array($media->file_name, $request->input('payment_proof', []))) {
                    $media->delete();
                }
            }
        }
        $media = $expense->payment_proof->pluck('file_name')->toArray();
        foreach ($request->input('payment_proof', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $expense->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('payment_proof');
            }
        }

        return redirect()->route('admin.expenses.index');
    }

    public function show(Expense $expense)
    {
        abort_if(Gate::denies('expense_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expense->load('expense_category', 'created_by', 'updated_by', 'teacher');

        // return $expense->teacher;

        return view('admin.expenses.show', compact('expense'));
    }

    public function destroy(Expense $expense)
    {
        abort_if(Gate::denies('expense_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expense->delete();

        return back();
    }

    public function massDestroy(MassDestroyExpenseRequest $request)
    {
        $expenses = Expense::find(request('ids'));

        foreach ($expenses as $expense) {
            $expense->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function importExcel(Request $request)
    {
        abort_if(Gate::denies('expense_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv',
            'default_year' => 'nullable|integer|min:1900|max:2100',
        ]);

        $file = $request->file('import_file');
        $extension = $file->getClientOriginalExtension() ?: 'xlsx';
        $filename = Str::random(18) . '.' . strtolower($extension);
        $file->storeAs('expenses_import', $filename);

        $path = storage_path('app/expenses_import/' . $filename);
        $ext = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, ['xlsx', 'xls'], true) && !class_exists(\ZipArchive::class)) {
            File::delete($path);
            return redirect()->route('admin.expenses.index')
                ->with('error', 'Excel processing requires PHP zip extension (ZipArchive). Please upload CSV or enable php_zip.');
        }

        $rows = $this->readImportRows($path);
        if (empty($rows)) {
            File::delete($path);
            return redirect()->route('admin.expenses.index')->with('error', 'No rows found in the import file.');
        }

        $headerIndex = $this->detectHeaderIndex($rows);
        $headers = $rows[$headerIndex] ?? [];
        $headerMap = $this->buildHeaderMap($headers);
        if (!$this->hasRequiredImportHeaders($headerMap)) {
            File::delete($path);
            return redirect()->route('admin.expenses.index')
                ->with('error', 'Could not detect required headers (Date, Details, Category, Cost). Import aborted.');
        }

        $defaultYear = (int) ($request->input('default_year') ?: date('Y'));
        $summary = [
            'created' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($rows as $index => $row) {
            if ($index <= $headerIndex) {
                continue;
            }

            if (!$this->hasAnyData($row)) {
                continue;
            }

            $sourceRowNumber = $index + 1;

            try {
                $normalized = $this->normalizeExpenseImportRow($row, $headerMap, $defaultYear);
                if ($normalized['category_name'] === '' || $normalized['title'] === '' || $normalized['amount'] === null) {
                    throw new \RuntimeException('Missing required fields (Category, Details, or Cost).');
                }

                $expenseCategory = $this->resolveExpenseCategory($normalized['category_name']);
                $teacherId = $this->resolveTeacherId($normalized['employee_code']);

                Expense::create([
                    'expense_category_id' => $expenseCategory->id,
                    'teacher_id' => $teacherId,
                    'title' => $normalized['title'],
                    'details' => $normalized['details'],
                    'amount' => $normalized['amount'],
                    'expense_date' => $normalized['expense_date'],
                    'expense_month' => $normalized['expense_month'],
                    'expense_year' => $normalized['expense_year'],
                    'created_by_id' => auth()->id(),
                    'updated_by_id' => auth()->id(),
                ]);

                $summary['created']++;
            } catch (\Throwable $exception) {
                $summary['failed']++;
                $summary['errors'][] = "Row {$sourceRowNumber}: {$exception->getMessage()}";
            }
        }

        File::delete($path);

        $message = "Import completed. Created: {$summary['created']}, Failed: {$summary['failed']}.";
        session()->flash('message', $message);
        if (!empty($summary['errors'])) {
            session()->flash('import_errors', array_slice($summary['errors'], 0, 25));
        }

        return redirect()->route('admin.expenses.index');
    }

    /**
     * @param array<int, mixed> $rows
     */
    protected function detectHeaderIndex(array $rows): int
    {
        foreach ($rows as $index => $row) {
            if (!is_array($row)) {
                continue;
            }

            $headerMap = $this->buildHeaderMap($row);
            $hasDate = array_key_exists('date', $headerMap);
            $hasCategory = array_key_exists('category', $headerMap) || array_key_exists('expense category', $headerMap);
            $hasAmount = array_key_exists('cost', $headerMap) || array_key_exists('amount', $headerMap);
            $hasDetails = array_key_exists('details', $headerMap) || array_key_exists('title', $headerMap);

            if ($hasDate && $hasCategory && $hasAmount && $hasDetails) {
                return $index;
            }
        }

        return 0;
    }

    /**
     * @param array<int, mixed> $headers
     * @return array<string, int>
     */
    protected function buildHeaderMap(array $headers): array
    {
        $map = [];
        foreach ($headers as $index => $header) {
            $map[$this->normalizeHeader($header)] = $index;
        }

        return $map;
    }

    protected function normalizeHeader($header): string
    {
        $header = is_string($header) ? $header : (string) $header;
        $header = strtolower(trim($header));
        $header = str_replace(["\r\n", "\n", "\r"], ' ', $header);
        $header = preg_replace('/[^a-z0-9]+/i', ' ', $header) ?? $header;
        $header = preg_replace('/\s+/', ' ', $header) ?? $header;

        return $header;
    }

    /**
     * @param array<string, int> $headerMap
     */
    protected function hasRequiredImportHeaders(array $headerMap): bool
    {
        $hasDate = array_key_exists('date', $headerMap);
        $hasCategory = array_key_exists('category', $headerMap) || array_key_exists('expense category', $headerMap);
        $hasAmount = array_key_exists('cost', $headerMap) || array_key_exists('amount', $headerMap);
        $hasDetails = array_key_exists('details', $headerMap) || array_key_exists('title', $headerMap);

        return $hasDate && $hasCategory && $hasAmount && $hasDetails;
    }

    /**
     * @param array<int, mixed> $row
     * @param array<string, int> $headerMap
     * @param array<int, string> $candidates
     */
    protected function valueByHeader(array $row, array $headerMap, array $candidates): string
    {
        foreach ($candidates as $candidate) {
            $key = $this->normalizeHeader($candidate);
            if (!array_key_exists($key, $headerMap)) {
                continue;
            }

            $value = $row[$headerMap[$key]] ?? null;
            $value = is_string($value) ? trim($value) : (string) $value;

            return trim($value);
        }

        return '';
    }

    /**
     * @param array<int, mixed> $row
     */
    protected function hasAnyData(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int, mixed> $row
     * @param array<string, int> $headerMap
     * @return array<string, mixed>
     */
    protected function normalizeExpenseImportRow(array $row, array $headerMap, int $defaultYear): array
    {
        $dateRaw = $this->valueByHeader($row, $headerMap, ['date', 'expense date']);
        $details = $this->valueByHeader($row, $headerMap, ['details', 'title', 'description']);
        $category = $this->valueByHeader($row, $headerMap, ['category', 'expense category']);
        $amountRaw = $this->valueByHeader($row, $headerMap, ['cost', 'amount']);
        $employeeCode = $this->valueByHeader($row, $headerMap, ['employee code', 'employee', 'teacher code']);

        $expenseDate = $this->normalizeDateValue($dateRaw, $defaultYear);
        $amount = $this->normalizeAmount($amountRaw);

        $title = $details !== '' ? $details : $category;
        $detailsValue = $details !== '' ? $details : null;

        $expenseMonth = $expenseDate ? (int) date('n', strtotime($expenseDate)) : null;
        $expenseYear = $expenseDate ? (int) date('Y', strtotime($expenseDate)) : null;

        return [
            'category_name' => $category,
            'title' => $title,
            'details' => $detailsValue,
            'amount' => $amount,
            'employee_code' => $employeeCode,
            'expense_date' => $expenseDate,
            'expense_month' => $expenseMonth,
            'expense_year' => $expenseYear,
        ];
    }

    protected function normalizeAmount(string $raw): ?float
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        $normalized = preg_replace('/[^0-9.\-]+/', '', $raw) ?? '';
        if ($normalized === '' || !is_numeric($normalized)) {
            return null;
        }

        return (float) $normalized;
    }

    protected function normalizeDateValue(string $raw, int $defaultYear): ?string
    {
        $raw = trim($raw);
        if ($raw === '' || strtolower($raw) === 'no date') {
            return null;
        }

        if (is_numeric($raw)) {
            try {
                $base = Carbon::create(1899, 12, 30, 0, 0, 0);
                $date = $base->copy()->addDays((int) floor((float) $raw));
                return $date->format('Y-m-d');
            } catch (\Throwable $e) {
                // Fallback to parsing below.
            }
        }

        $value = $raw;
        if (!preg_match('/\b\d{4}\b/', $value)) {
            $value = $raw . ' ' . $defaultYear;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function resolveExpenseCategory(string $name): ExpenseCategory
    {
        $trimmed = trim($name);
        $cacheKey = strtolower($trimmed);
        if (array_key_exists($cacheKey, $this->expenseCategoryCache)) {
            return $this->expenseCategoryCache[$cacheKey];
        }

        $record = ExpenseCategory::whereRaw('LOWER(name) = ?', [$cacheKey])->first();
        if (!$record) {
            $record = ExpenseCategory::create(['name' => $trimmed]);
        }

        $this->expenseCategoryCache[$cacheKey] = $record;

        return $record;
    }

    protected function resolveTeacherId(?string $employeeCode): ?int
    {
        $employeeCode = trim((string) $employeeCode);
        if ($employeeCode === '') {
            return null;
        }

        if (array_key_exists($employeeCode, $this->teacherIdCache)) {
            return $this->teacherIdCache[$employeeCode];
        }

        $teacher = Teacher::where('emloyee_code', $employeeCode)->first();
        if (!$teacher && is_numeric($employeeCode)) {
            $teacher = Teacher::find((int) $employeeCode);
        }

        $this->teacherIdCache[$employeeCode] = $teacher ? $teacher->id : null;

        return $this->teacherIdCache[$employeeCode];
    }

    /**
     * @return array<int, array<int, string>>
     */
    protected function readImportRows(string $path): array
    {
        $extension = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));

        if ($extension === 'xlsx' && class_exists(\ZipArchive::class)) {
            return $this->readXlsxRows($path);
        }

        return $this->readRowsWithSpreadsheetReader($path);
    }

    /**
     * @return array<int, array<int, string>>
     */
    protected function readRowsWithSpreadsheetReader(string $path): array
    {
        $previousHandler = set_error_handler(function ($severity, $message, $file) {
            $isKnownContinueWarning = str_contains((string) $message, '"continue" targeting switch is equivalent to "break"')
                && str_contains((string) $file, 'SpreadsheetReader_XLSX.php');

            if ($isKnownContinueWarning) {
                return true;
            }

            return false;
        });

        try {
            $reader = new \SpreadsheetReader($path);
        } finally {
            restore_error_handler();
        }

        $rows = [];
        foreach ($reader as $row) {
            $rows[] = array_map(fn($value) => trim((string) $value), is_array($row) ? $row : []);
        }

        return $rows;
    }

    /**
     * Lightweight XLSX reader to avoid SpreadsheetReader_XLSX PHP warning.
     *
     * @return array<int, array<int, string>>
     */
    protected function readXlsxRows(string $path): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            return [];
        }

        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml !== false) {
            $sharedSxe = @simplexml_load_string($sharedXml);
            if ($sharedSxe) {
                foreach ($sharedSxe->xpath('//*[local-name()="si"]') ?: [] as $si) {
                    $text = '';
                    foreach ($si->xpath('.//*[local-name()="t"]') ?: [] as $t) {
                        $text .= (string) $t;
                    }
                    $sharedStrings[] = $text;
                }
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($sheetXml === false) {
            return [];
        }

        $sheet = @simplexml_load_string($sheetXml);
        if (!$sheet) {
            return [];
        }

        $rows = [];
        foreach ($sheet->xpath('//*[local-name()="sheetData"]/*[local-name()="row"]') ?: [] as $rowNode) {
            $row = [];
            foreach ($rowNode->xpath('./*[local-name()="c"]') ?: [] as $cell) {
                $ref = (string) ($cell['r'] ?? '');
                $cellType = (string) ($cell['t'] ?? '');
                $value = '';
                $valueNode = $cell->xpath('./*[local-name()="v"]');
                $raw = isset($valueNode[0]) ? (string) $valueNode[0] : '';

                if ($cellType === 's') {
                    $index = (int) $raw;
                    $value = $sharedStrings[$index] ?? '';
                } elseif ($cellType === 'inlineStr') {
                    $inlineTextNodes = $cell->xpath('./*[local-name()="is"]//*[local-name()="t"]');
                    if (!empty($inlineTextNodes)) {
                        foreach ($inlineTextNodes as $inlineNode) {
                            $value .= (string) $inlineNode;
                        }
                    }
                } else {
                    $value = $raw;
                }

                $col = preg_replace('/\\d+/', '', $ref) ?: '';
                $colIndex = $col !== '' ? $this->columnLettersToIndex($col) : count($row);
                $row[$colIndex] = trim($value);
            }

            if (!empty($row)) {
                ksort($row);
                $max = max(array_keys($row));
                $normalized = [];
                for ($i = 0; $i <= $max; $i++) {
                    $normalized[] = $row[$i] ?? '';
                }
                $rows[] = $normalized;
            } else {
                $rows[] = [];
            }
        }

        return $rows;
    }

    protected function columnLettersToIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $index = 0;
        for ($i = 0; $i < strlen($letters); $i++) {
            $index = $index * 26 + (ord($letters[$i]) - ord('A') + 1);
        }

        return max(0, $index - 1);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('expense_create') && Gate::denies('expense_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new Expense();
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
