<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyEarningRequest;
use App\Http\Requests\StoreEarningRequest;
use App\Http\Requests\UpdateEarningRequest;
use App\Models\Earning;
use App\Models\EarningCategory;
use App\Models\StudentBasicInfo;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class EarningsController extends Controller
{
    use MediaUploadingTrait;

    /**
     * @var array<string, \App\Models\EarningCategory>
     */
    protected array $earningCategoryCache = [];

    /**
     * @var array<string, int|null>
     */
    protected array $studentIdCache = [];

    public function index(Request $request)
    {
        abort_if(Gate::denies('earning_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Earning::with(['earning_category', 'student', 'subject', 'created_by', 'updated_by'])
                ->select(sprintf('%s.*', (new Earning)->table));

            $categoryId = $request->input('category_id');
            $month = $request->input('month');
            $year = $request->input('year');

            if (!empty($categoryId)) {
                $query->where('earning_category_id', $categoryId);
            }
            if (!empty($month)) {
                $query->whereMonth('earning_date', $month);
            }
            if (!empty($year)) {
                $query->whereYear('earning_date', $year);
            }

            $table = DataTables::of($query);
            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'earning_show';
                $editGate = 'earning_edit';
                $deleteGate = 'earning_delete';
                $crudRoutePart = 'earnings';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', fn($row) => $row->id ?? '');
            $table->addColumn('earning_category_name', fn($row) => $row->earning_category->name ?? '');
            $table->addColumn('student_id_no', fn($row) => $row->student->id_no ?? '');
            $table->addColumn('subject_name', fn($row) => $row->subject->name ?? '');
            $table->editColumn('title', fn($row) => $row->title ?? '');
            $table->editColumn('exam_year', fn($row) => $row->exam_year ?? '');
            $table->editColumn('amount', fn($row) => $row->amount ?? '');
            $table->editColumn('earning_date', fn($row) => $row->earning_date ?? '');
            $table->editColumn('paid_by', fn($row) => $row->paid_by ?? '');
            $table->editColumn('recieved_by', fn($row) => $row->recieved_by ?? '');

            $table->rawColumns(['actions', 'placeholder']);
            $table->setRowAttr([
                'data-entry-id' => fn($row) => $row->id,
            ]);

            return $table->make(true);
        }

        $earning_categories = EarningCategory::pluck('name', 'id');

        return view('admin.earnings.index', compact('earning_categories'));
    }

    public function summary(Request $request)
    {
        abort_if(Gate::denies('earning_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categoryId = $request->input('category_id');
        $month = $request->input('month');
        $year = $request->input('year');
        $summaryYear = !empty($year) ? (int) $year : (int) date('Y');

        $query = Earning::query()
            ->whereNotNull('earning_date')
            ->whereYear('earning_date', $summaryYear);

        if (!empty($categoryId)) {
            $query->where('earning_category_id', $categoryId);
        }
        if (!empty($month)) {
            $query->whereMonth('earning_date', $month);
        }

        $totals = $query
            ->selectRaw('MONTH(earning_date) as month, SUM(amount) as total')
            ->groupBy(DB::raw('MONTH(earning_date)'))
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

    public function downloadDemoCsv()
    {
        abort_if(Gate::denies('earning_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $headers = [
            'Date',
            'Details',
            'Category',
            'Earning',
            'Admission ID',
        ];

        $filename = 'earnings_demo_' . date('Ymd_His') . '.csv';
        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers);
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function create()
    {
        abort_if(Gate::denies('earning_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $earning_categories = EarningCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $earning_category_flags = EarningCategory::pluck('is_student_connected', 'id');

        // We don't load all students here anymore, it's handled via Select2 AJAX
        $students = [];

        $subjects = Subject::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        // Generate receipt number Format: REC-YYYY-001
        $receipt_numbers = 'REC-' . date('Y') . '-' . str_pad(Earning::whereYear('earning_date', date('Y'))->count() + 1, 3, '0', STR_PAD_LEFT);

        return view('admin.earnings.create', compact('earning_categories', 'earning_category_flags', 'students', 'subjects', 'receipt_numbers'));
    }

    public function getStudents(Request $request)
    {
        $search = $request->term;

        $students = StudentBasicInfo::where(function ($query) use ($search) {
            $query->where('first_name', 'LIKE', "%$search%")
                ->orWhere('last_name', 'LIKE', "%$search%")
                ->orWhere('id_no', 'LIKE', "%$search%");
        })
            ->limit(10)
            ->get();

        $formatted_students = [];

        foreach ($students as $student) {
            $formatted_students[] = [
                'id' => $student->id,
                'text' => ($student->first_name ?? '') . ' ' . ($student->last_name ?? '') . ' (' . ($student->id_no ?? '') . ')'
            ];
        }

        return response()->json($formatted_students);
    }

    public function store(StoreEarningRequest $request)
    {
        $data = $request->all();
        $data['created_by_id'] = auth()->id();

        // Auto-calculate month and year from earning_date
        if (!empty($data['earning_date'])) {
            $date = \Carbon\Carbon::parse($data['earning_date']);
            $data['earning_month'] = $date->month;
            $data['earning_year'] = $date->year;
        }

        $earning = Earning::create($data);

        foreach ($request->input('payment_proof', []) as $file) {
            $earning->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('payment_proof');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $earning->id]);
        }

        return redirect()->route('admin.earnings.index');
    }

    public function edit(Earning $earning)
    {
        abort_if(Gate::denies('earning_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $earning_categories = EarningCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $earning_category_flags = EarningCategory::pluck('is_student_connected', 'id');

        $students = [];

        $subjects = Subject::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $earning->load('earning_category', 'student', 'subject', 'created_by', 'updated_by');

        return view('admin.earnings.edit', compact('earning', 'earning_categories', 'earning_category_flags', 'students', 'subjects'));
    }

    public function update(UpdateEarningRequest $request, Earning $earning)
    {
        $data = $request->all();
        $data['updated_by_id'] = auth()->id();

        // Auto-calculate month and year from earning_date
        if (!empty($data['earning_date'])) {
            $date = \Carbon\Carbon::parse($data['earning_date']);
            $data['earning_month'] = $date->month;
            $data['earning_year'] = $date->year;
        }

        $earning->update($data);

        if (count($earning->payment_proof) > 0) {
            foreach ($earning->payment_proof as $media) {
                if (!in_array($media->file_name, $request->input('payment_proof', []))) {
                    $media->delete();
                }
            }
        }
        $media = $earning->payment_proof->pluck('file_name')->toArray();
        foreach ($request->input('payment_proof', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $earning->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('payment_proof');
            }
        }

        return redirect()->route('admin.earnings.index');
    }

    public function show(Earning $earning)
    {
        abort_if(Gate::denies('earning_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $earning->load('earning_category', 'student', 'subject', 'created_by', 'updated_by');

        return view('admin.earnings.show', compact('earning'));
    }

    public function destroy(Earning $earning)
    {
        abort_if(Gate::denies('earning_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $earning->delete();

        return back();
    }

    public function massDestroy(MassDestroyEarningRequest $request)
    {
        $earnings = Earning::find(request('ids'));

        foreach ($earnings as $earning) {
            $earning->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function importExcel(Request $request)
    {
        abort_if(Gate::denies('earning_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv',
            'default_year' => 'nullable|integer|min:1900|max:2100',
        ]);

        $file = $request->file('import_file');
        $extension = $file->getClientOriginalExtension() ?: 'xlsx';
        $filename = Str::random(18) . '.' . strtolower($extension);
        $file->storeAs('earnings_import', $filename);

        $path = storage_path('app/earnings_import/' . $filename);
        $ext = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, ['xlsx', 'xls'], true) && !class_exists(\ZipArchive::class)) {
            File::delete($path);
            return redirect()->route('admin.earnings.index')
                ->with('error', 'Excel processing requires PHP zip extension (ZipArchive). Please upload CSV or enable php_zip.');
        }

        $rows = $this->readImportRows($path);
        if (empty($rows)) {
            File::delete($path);
            return redirect()->route('admin.earnings.index')->with('error', 'No rows found in the import file.');
        }

        $headerIndex = $this->detectHeaderIndex($rows);
        $headers = $rows[$headerIndex] ?? [];
        $headerMap = $this->buildHeaderMap($headers);
        if (!$this->hasRequiredImportHeaders($headerMap)) {
            File::delete($path);
            return redirect()->route('admin.earnings.index')
                ->with('error', 'Could not detect required headers (Date, Details, Category, Earning). Import aborted.');
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
                $normalized = $this->normalizeEarningImportRow($row, $headerMap, $defaultYear);
                if ($normalized['category_name'] === '' || $normalized['title'] === '' || $normalized['amount'] === null) {
                    throw new \RuntimeException('Missing required fields (Category, Details, or Earning).');
                }

                $earningCategory = $this->resolveEarningCategory($normalized['category_name']);
                $studentId = $this->resolveStudentId($normalized['admission_id']);

                Earning::create([
                    'earning_category_id' => $earningCategory->id,
                    'student_id' => $studentId,
                    'title' => $normalized['title'],
                    'details' => $normalized['details'],
                    'amount' => $normalized['amount'],
                    'earning_date' => $normalized['earning_date'],
                    'earning_month' => $normalized['earning_month'],
                    'earning_year' => $normalized['earning_year'],
                    'recieved_by' => auth()->user()->name ?? null,
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

        return redirect()->route('admin.earnings.index');
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
            $hasCategory = array_key_exists('category', $headerMap) || array_key_exists('earning category', $headerMap);
            $hasAmount = array_key_exists('earning', $headerMap) || array_key_exists('amount', $headerMap);
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
        $hasCategory = array_key_exists('category', $headerMap) || array_key_exists('earning category', $headerMap);
        $hasAmount = array_key_exists('earning', $headerMap) || array_key_exists('amount', $headerMap);
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
    protected function normalizeEarningImportRow(array $row, array $headerMap, int $defaultYear): array
    {
        $dateRaw = $this->valueByHeader($row, $headerMap, ['date', 'earning date']);
        $details = $this->valueByHeader($row, $headerMap, ['details', 'title', 'description']);
        $category = $this->valueByHeader($row, $headerMap, ['category', 'earning category']);
        $amountRaw = $this->valueByHeader($row, $headerMap, ['earning', 'amount']);
        $admissionId = $this->valueByHeader($row, $headerMap, ['admission id', 'admission', 'student id']);

        $earningDate = $this->normalizeDateValue($dateRaw, $defaultYear);
        $amount = $this->normalizeAmount($amountRaw);

        $title = $details !== '' ? $details : $category;
        $detailsValue = $details !== '' ? $details : null;

        $earningMonth = $earningDate ? (int) date('n', strtotime($earningDate)) : null;
        $earningYear = $earningDate ? (int) date('Y', strtotime($earningDate)) : null;

        return [
            'category_name' => $category,
            'title' => $title,
            'details' => $detailsValue,
            'amount' => $amount,
            'admission_id' => $admissionId,
            'earning_date' => $earningDate,
            'earning_month' => $earningMonth,
            'earning_year' => $earningYear,
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

    protected function resolveEarningCategory(string $name): EarningCategory
    {
        $trimmed = trim($name);
        $cacheKey = strtolower($trimmed);
        if (array_key_exists($cacheKey, $this->earningCategoryCache)) {
            return $this->earningCategoryCache[$cacheKey];
        }

        $record = EarningCategory::whereRaw('LOWER(name) = ?', [$cacheKey])->first();
        if (!$record) {
            $record = EarningCategory::create(['name' => $trimmed]);
        }

        $this->earningCategoryCache[$cacheKey] = $record;

        return $record;
    }

    protected function resolveStudentId(?string $admissionId): ?int
    {
        $admissionId = trim((string) $admissionId);
        if ($admissionId === '') {
            return null;
        }

        if (array_key_exists($admissionId, $this->studentIdCache)) {
            return $this->studentIdCache[$admissionId];
        }

        $student = StudentBasicInfo::where('id_no', $admissionId)->first();
        if (!$student && is_numeric($admissionId)) {
            $student = StudentBasicInfo::find((int) $admissionId);
        }

        $this->studentIdCache[$admissionId] = $student ? $student->id : null;

        return $this->studentIdCache[$admissionId];
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
        abort_if(Gate::denies('earning_create') && Gate::denies('earning_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new Earning();
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
