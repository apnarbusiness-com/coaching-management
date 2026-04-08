<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyStudentBasicInfoRequest;
use App\Http\Requests\StoreStudentBasicInfoRequest;
use App\Http\Requests\UpdateStudentBasicInfoRequest;
use App\Models\AcademicClass;
use App\Models\AcademicBackground;
use App\Models\Batch;
use App\Models\ClassRoom;
use App\Models\Section;
use App\Models\Shift;
use App\Models\StudentBasicInfo;
use App\Models\StudentDetailsInformation;
use App\Models\StudentImportRaw;
use App\Models\Subject;
use App\Models\User;
use App\Services\StudentImportService;
use Carbon\Carbon;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use SpreadsheetReader;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Polyfill\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;

class StudentBasicInfoController extends Controller
{
    use MediaUploadingTrait;

    /**
     * @var array<string, int|null>
     */
    protected array $academicBackgroundCache = [];

    /**
     * @var array<string, int|null>
     */
    protected array $academicClassCache = [];

    public function index(Request $request)
    {
        abort_if(Gate::denies('student_basic_info_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = StudentBasicInfo::with(['class', 'section', 'shift', 'academicBackground', 'user', 'subjects'])->select(sprintf('%s.*', (new StudentBasicInfo)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'student_basic_info_show';
                $editGate = 'student_basic_info_edit';
                $deleteGate = 'student_basic_info_delete';
                $crudRoutePart = 'student-basic-infos';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('roll', function ($row) {
                return $row->roll ? $row->roll : '';
            });
            $table->editColumn('first_name', function ($row) {
                return $row->first_name ? $row->first_name : '';
            });
            $table->editColumn('gender', function ($row) {
                return $row->gender ? StudentBasicInfo::GENDER_RADIO[$row->gender] : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? StudentBasicInfo::STATUS_SELECT[$row->status] : '';
            });

            $table->addColumn('user_name', function ($row) {
                return $row->user ? $row->user->name : '';
            });

            $table->editColumn('subject', function ($row) {
                $labels = [];
                foreach ($row->subjects as $subject) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $subject->name);
                }

                return implode(' ', $labels);
            });

            $table->rawColumns(['actions', 'placeholder', 'user', 'subject']);

            return $table->make(true);
        }

        $rawSourceFiles = StudentImportRaw::query()
            ->select('source_file')
            ->distinct()
            ->orderByDesc('source_file')
            ->pluck('source_file');

        return view('admin.studentBasicInfos.index', compact('rawSourceFiles'));
    }

    public function rawImports(Request $request)
    {
        abort_if(Gate::denies('student_basic_info_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sourceFile = $request->input('source_file');

        $rawSourceFiles = StudentImportRaw::query()
            ->select('source_file')
            ->distinct()
            ->orderByDesc('source_file')
            ->pluck('source_file');

        $query = StudentImportRaw::query()
            ->orderBy('id');
        // ->orderByDesc('id');
        if (!empty($sourceFile)) {
            $query->where('source_file', $sourceFile);
        }

        $rawRows = $query->paginate(50)->withQueryString();
        $summaryQuery = StudentImportRaw::query();
        if (!empty($sourceFile)) {
            $summaryQuery->where('source_file', $sourceFile);
        }
        $totalRows = (clone $summaryQuery)->count();
        $processedRows = (clone $summaryQuery)->where('is_processed', true)->count();
        $pendingRows = max(0, $totalRows - $processedRows);

        return view('admin.studentBasicInfos.rawImports', compact(
            'rawRows',
            'rawSourceFiles',
            'sourceFile',
            'totalRows',
            'processedRows',
            'pendingRows'
        ));
    }

    public function deleteRawImportRow(StudentImportRaw $studentImportRaw)
    {
        abort_if(Gate::denies('student_basic_info_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sourceFile = $studentImportRaw->source_file;
        $studentImportRaw->delete();

        return redirect()->route('admin.student-basic-infos.rawImports', ['source_file' => $sourceFile])
            ->with('message', 'Raw row deleted successfully.');
    }

    public function resetRawImports(Request $request)
    {
        abort_if(Gate::denies('student_basic_info_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'scope' => 'required|in:all,source',
            'source_file' => 'nullable|string',
        ]);

        $query = StudentImportRaw::query();
        $scope = $request->input('scope');
        $sourceFile = $request->input('source_file');

        if ($scope === 'source') {
            if (empty($sourceFile)) {
                return redirect()->route('admin.student-basic-infos.rawImports')
                    ->with('error', 'Please select a source file for reset.');
            }
            $query->where('source_file', $sourceFile);
        }

        $deleted = $query->delete();

        return redirect()->route('admin.student-basic-infos.rawImports')
            ->with('message', "Raw reset complete. Deleted rows: {$deleted}.");
    }

    public function create()
    {
        abort_if(Gate::denies('student_basic_info_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $classes = AcademicClass::pluck('class_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $sections = Section::pluck('section_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $shifts = Shift::pluck('shift_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $academicBackgrounds = AcademicBackground::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $subjects = Subject::pluck('name', 'id');
        $batches = Batch::pluck('batch_name', 'id');

        $latest_id_no = generateAdmissionID(); // Generate the latest admission ID :: from helpers

        return view('admin.studentBasicInfos.create', compact('classes', 'sections', 'shifts', 'academicBackgrounds', 'subjects', 'users', 'batches', 'latest_id_no'));
    }

    public function store(StoreStudentBasicInfoRequest $request)
    {
        // if ($request->need_login) {
        //     return "need login";
        // }
        // return $request->all();
        // $studentBasicInfo = StudentBasicInfo::create($request->all());

        $student = StudentBasicInfo::where('contact_number', $request->contact_number)->first();
        if (isset($student)) {
            return redirect()->back()->with('error', 'Contact Number already exists.');
        }


        $studentBasicInfo = new StudentBasicInfo();
        $studentBasicInfo->roll = $request->roll ? $request->roll : (generateAdmissionID() ?? 000);
        $studentBasicInfo->id_no = generateAdmissionID() ?? 000;
        $studentBasicInfo->first_name = $request->first_name;
        $studentBasicInfo->last_name = $request->filled('last_name') ? $request->last_name : null;
        $studentBasicInfo->gender = $request->gender;
        $studentBasicInfo->dob = $request->dob;
        $studentBasicInfo->contact_number = $request->contact_number;
        $studentBasicInfo->email = $request->email;

        $studentBasicInfo->class_id = $request->class_id;
        $studentBasicInfo->section_id = $request->section_id;
        $studentBasicInfo->shift_id = $request->shift_id;
        $studentBasicInfo->academic_background_id = $request->academic_background_id;

        $studentBasicInfo->joining_date = $request->joining_date;
        $studentBasicInfo->status = $request->status ? $request->status : 1;

        $studentBasicInfo->save();



        if (!isset($studentBasicInfo)) {
            return redirect()->back()->with('error', 'Student Basic Info not created. Please try again.');
        }

        if ($request->need_login) {

            // $user = $studentBasicInfo->user ?? null;
            // $user = null;
            // if (!isset($user)) {
            $user = User::create([
                'name' => trim($request->first_name . ' ' . ($request->last_name ?? '')),
                'email' => $request->email,
                'user_name' => $request->user_name ?? null,
                'admission_id' => $studentBasicInfo->id_no ?? null,
                'password' => isset($request->password) && !empty($request->password) ? bcrypt($request->password) : bcrypt($studentBasicInfo->id_no),
            ]);

            $user->roles()->sync(\App\Models\Role::whereIn('title', ['Student', 'student'])->first()->id ?? []);


            // }
            $studentBasicInfo->user_id = $user->id;
            $studentBasicInfo->save();
        }

        $studentDetails = new StudentDetailsInformation();
        $studentDetails->student_id = $studentBasicInfo->id;

        $studentDetails->fathers_name = $request->fathers_name;
        $studentDetails->mothers_name = $request->mothers_name;

        $studentDetails->guardian_name = $request->guardian_name;
        $studentDetails->guardian_relation = $request->guardian_relation_type == 'Other' ? $request->guardian_relation_other : $request->guardian_relation_type;
        $studentDetails->guardian_contact_number = $request->guardian_contact_number;
        $studentDetails->guardian_email = $request->guardian_email;
        $studentDetails->address = $request->address;
        $studentDetails->student_blood_group = $request->student_blood_group;
        $studentDetails->save();

        $studentBasicInfo->subjects()->sync($request->input('subjects', []));
        $studentBasicInfo->batches()->sync($request->input('batches', []));
        if ($request->input('file-upload', false)) {
            $studentBasicInfo
                ->addMedia(storage_path('tmp/uploads/' . basename($request->input('file-upload'))))
                // ->toMediaCollection('file-upload');
                ->toMediaCollection('image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $studentBasicInfo->id]);
        }

        return redirect()->route('admin.student-basic-infos.index');
    }

    public function edit(StudentBasicInfo $studentBasicInfo)
    {
        abort_if(Gate::denies('student_basic_info_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $classes = AcademicClass::pluck('class_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $sections = Section::pluck('section_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $shifts = Shift::pluck('shift_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $academicBackgrounds = AcademicBackground::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $subjects = Subject::pluck('name', 'id');
        $batches = Batch::pluck('batch_name', 'id');

        $studentBasicInfo->load('class', 'section', 'shift', 'academicBackground', 'user', 'subjects', 'batches', 'studentDetails');

        return view('admin.studentBasicInfos.edit', compact('classes', 'sections', 'shifts', 'academicBackgrounds', 'studentBasicInfo', 'subjects', 'users', 'batches'));
    }

    public function update(UpdateStudentBasicInfoRequest $request, StudentBasicInfo $studentBasicInfo)
    {
        // Update Student Basic Info
        $studentBasicInfo->update([
            'roll' => $request->roll,
            'first_name' => $request->first_name,
            'last_name' => $request->filled('last_name') ? $request->last_name : null,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'contact_number' => $request->contact_number,
            'email' => $request->email,
            'class_id' => $request->class_id,
            'section_id' => $request->section_id,
            'shift_id' => $request->shift_id,
            'academic_background_id' => $request->academic_background_id,
            'joining_date' => $request->joining_date,
            'status' => $request->status,
        ]);

        $studentBasicInfo->subjects()->sync($request->input('subjects', []));
        $studentBasicInfo->batches()->sync($request->input('batches', []));

        // Handle User Login
        if ($request->need_login) {
            if (!$studentBasicInfo->user_id) {
                $user = User::where('email', $request->email)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => trim($request->first_name . ' ' . ($request->last_name ?? '')),
                        'email' => $request->email,
                        'password' => isset($request->password) && !empty($request->password) ? bcrypt($request->password) : bcrypt($request->email),
                    ]);
                }
                $studentBasicInfo->user_id = $user->id;
                $studentBasicInfo->save();
            } else {
                $user = $studentBasicInfo->user;
                $userData = [
                    'name' => trim($request->first_name . ' ' . ($request->last_name ?? '')),
                    'email' => $request->email,
                ];
                if ($request->filled('password')) {
                    $userData['password'] = bcrypt($request->password);
                }
                $user->update($userData);
            }
        }

        $guardian_name = '';
        if ($request->guardian_relation_type == 'Father') {
            $guardian_name = $request->fathers_name;
        } elseif ($request->guardian_relation_type == 'Mother') {
            $guardian_name = $request->mothers_name;
        } else {
            $guardian_name = $request->guardian_name;
        }

        // Update Student Details
        $studentDetails = $studentBasicInfo->studentDetails()->firstOrCreate(['student_id' => $studentBasicInfo->id]);

        $studentDetails->update([
            'fathers_name' => $request->fathers_name,
            'mothers_name' => $request->mothers_name,
            'guardian_name' => $guardian_name,
            'guardian_relation' => $request->guardian_relation_type == 'Other' ? $request->guardian_relation_other : $request->guardian_relation_type,
            'guardian_contact_number' => $request->guardian_contact_number,
            'guardian_email' => $request->guardian_email,
            'address' => $request->address,
            'student_blood_group' => $request->student_blood_group,
        ]);

        // Image Handling
        if ($request->input('image', false)) {
            if (!$studentBasicInfo->image || $request->input('image') !== $studentBasicInfo->image->file_name) {
                // If it's a new file (from tmp)
                if (file_exists(storage_path('tmp/uploads/' . basename($request->input('image'))))) {
                    if ($studentBasicInfo->image) {
                        $studentBasicInfo->image->delete();
                    }
                    $studentBasicInfo->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
                }
            }
        }

        return redirect()->route('admin.student-basic-infos.index');
    }

    public function show(StudentBasicInfo $studentBasicInfo)
    {
        // return $studentBasicInfo;
        // if ($studentBasicInfo->media->count() > 0) {
        //     return response()->json(['preview_url' => $studentBasicInfo->media[0]->preview_url]);
        // }

        // return $studentBasicInfo->image->getUrl('preview');

        abort_if(Gate::denies('student_basic_info_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $studentBasicInfo->load('class', 'section', 'shift', 'academicBackground', 'user', 'subjects', 'batches.subject', 'batches.class', 'studentEarnings', 'studentDetails');

        $attendancePercent = 0;
        $score = 0;

        $subjects = Subject::pluck('name', 'id');
        $classRooms = ClassRoom::pluck('name', 'id');

        return view('admin.studentBasicInfos.show', compact('studentBasicInfo', 'attendancePercent', 'score', 'subjects', 'classRooms'));
    }

    public function syncSubjects(Request $request, StudentBasicInfo $studentBasicInfo)
    {
        $studentBasicInfo->subjects()->sync($request->input('subjects', []));

        return response()->json(['message' => 'Subjects updated successfully']);
    }

    public function destroy(StudentBasicInfo $studentBasicInfo)
    {
        abort_if(Gate::denies('student_basic_info_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $studentBasicInfo->delete();

        return back();
    }

    public function massDestroy(MassDestroyStudentBasicInfoRequest $request)
    {
        $studentBasicInfos = StudentBasicInfo::find(request('ids'));

        foreach ($studentBasicInfos as $studentBasicInfo) {
            $studentBasicInfo->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('student_basic_info_create') && Gate::denies('student_basic_info_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new StudentBasicInfo();
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function printIdCard($id)
    {
        abort_if(Gate::denies('student_basic_info_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $studentId = explode(',', $id);

        $student = StudentBasicInfo::where('id', $studentId)->first();

        return view('admin.studentBasicInfos.id_card', compact('student'));
    }

    public function downloadDemoCsv()
    {
        abort_if(Gate::denies('student_basic_info_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $headers = [
            'roll',
            'id_no',
            'first_name',
            'last_name',
            'gender',
            'contact_number',
            'email',
            'dob',
            'status',
            'joining_date',
            'class_id',
            'section_id',
            'shift_id',
            'academic_background_id',
            'user_id',
        ];

        $sampleRow = [
            '101',
            'ST-2026-0001',
            'Rahim',
            'Khan',
            'male',
            '01700000000',
            'rahim@example.com',
            '2009-01-15',
            '1',
            now()->format('Y-m-d H:i:s'),
            '1',
            '1',
            '1',
            '1',
            '',
        ];

        return response()->streamDownload(function () use ($headers, $sampleRow) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM helps Excel open Bengali/UTF-8 text correctly.
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);
            fputcsv($handle, $sampleRow);
            fclose($handle);
        }, 'student_basic_info_demo.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function importRawToTable(Request $request)
    {
        abort_if(Gate::denies('student_basic_info_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'excel_file' => 'required|mimes:csv,txt,xls,xlsx',
        ]);

        $file = $request->file('excel_file');
        $sourceFile = $file->getClientOriginalName() ?: ('import_' . now()->format('Ymd_His'));
        $spreadsheet = IOFactory::load($file->getRealPath());

        $rowsToInsert = [];
        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            $sheetName = $sheet->getTitle();
            foreach ($sheet->getRowIterator() as $row) {
                $rowIndex = $row->getRowIndex();
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $values = [];
                foreach ($cellIterator as $cell) {
                    $values[] = trim((string) $cell->getFormattedValue());
                }

                $hasData = false;
                foreach ($values as $value) {
                    if ($value !== '') {
                        $hasData = true;
                        break;
                    }
                }

                if (!$hasData) {
                    continue;
                }

                if (!$this->isMeaningfulRawRow($values)) {
                    continue;
                }

                $rowsToInsert[] = [
                    'source_file' => $sourceFile,
                    'sheet_name' => $sheetName,
                    'row_index' => $rowIndex,
                    'row_data' => json_encode($values, JSON_UNESCAPED_UNICODE),
                    'is_processed' => false,
                    'processed_at' => null,
                    'processed_status' => null,
                    'processed_note' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($rowsToInsert)) {
            foreach (array_chunk($rowsToInsert, 500) as $chunk) {
                DB::table('student_import_raws')->insert($chunk);
            }
        }

        return redirect()->route('admin.student-basic-infos.rawImports', ['source_file' => $sourceFile])
            ->with('message', 'Raw import complete. Rows inserted: ' . count($rowsToInsert));
    }

    public function processRawToStudents(Request $request, StudentImportService $studentImportService)
    {
        abort_if(Gate::denies('student_basic_info_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'source_file' => 'required|string',
        ]);

        $allRows = StudentImportRaw::query()
            ->where('source_file', $request->input('source_file'))
            ->orderBy('sheet_name')
            ->orderBy('row_index')
            ->get();

        if ($allRows->isEmpty()) {
            return redirect()->route('admin.student-basic-infos.index')->with('error', 'No raw rows found for selected source file.');
        }

        $rows = $allRows->map(fn(StudentImportRaw $raw) => (array) ($raw->row_data ?? []))->values()->all();
        $headerIndex = $this->detectHeaderIndex($rows);
        $headers = $rows[$headerIndex] ?? [];
        $headerMap = $this->buildHeaderMap($headers);
        if (!$this->hasRequiredImportHeaders($headerMap)) {
            return redirect()->route('admin.student-basic-infos.index')
                ->with('error', 'Could not detect required headers (ID, Student Name, Contact Number) in raw table data.');
        }

        $headerRowIndex = $allRows[$headerIndex]->row_index ?? null;
        if ($headerRowIndex === null) {
            return redirect()->route('admin.student-basic-infos.index')
                ->with('error', 'Header row not found in raw rows.');
        }

        $rawRows = StudentImportRaw::query()
            ->where('source_file', $request->input('source_file'))
            ->where('row_index', '>', $headerRowIndex)
            ->where('is_processed', false)
            ->orderBy('sheet_name')
            ->orderBy('row_index')
            ->get();

        if ($rawRows->isEmpty()) {
            return redirect()->route('admin.student-basic-infos.rawImports', ['source_file' => $request->input('source_file')])
                ->with('message', 'No unprocessed rows found for this source file.');
        }

        $summary = [
            'created' => 0,
            'updated' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($rawRows as $rawRow) {
            $row = (array) ($rawRow->row_data ?? []);
            if (!$this->hasAnyData($row)) {
                continue;
            }

            try {
                $normalized = $this->normalizeImportRow($row, $headerMap);
                $normalized['academic_background_id'] = $this->resolveAcademicBackgroundId(
                    (string) ($normalized['academic_background_name'] ?? '')
                );
                $result = $studentImportService->importRow($normalized);

                if ($result['status'] === 'created') {
                    $summary['created']++;
                } elseif ($result['status'] === 'updated') {
                    $summary['updated']++;
                }

                $rawRow->is_processed = true;
                $rawRow->processed_at = now();
                $rawRow->processed_status = $result['status'];
                $rawRow->processed_note = $result['message'];
                $rawRow->save();
            } catch (\Throwable $exception) {
                $summary['failed']++;
                $summary['errors'][] = "Row {$rawRow->row_index}: {$exception->getMessage()}";

                $rawRow->is_processed = true;
                $rawRow->processed_at = now();
                $rawRow->processed_status = 'failed';
                $rawRow->processed_note = $exception->getMessage();
                $rawRow->save();
            }
        }

        $message = "Step-2 completed. Created: {$summary['created']}, Updated: {$summary['updated']}, Failed: {$summary['failed']}.";
        session()->flash('message', $message);
        if (!empty($summary['errors'])) {
            session()->flash('import_errors', array_slice($summary['errors'], 0, 25));
        }

        return redirect()->route('admin.student-basic-infos.rawImports', ['source_file' => $request->input('source_file')]);
    }

    public function parseStudentImport(Request $request)
    {
        abort_if(Gate::denies('student_basic_info_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'csv_file' => 'required|mimes:csv,txt,xls,xlsx',
        ]);

        $file = $request->file('csv_file');
        $ext = strtolower($file->getClientOriginalExtension() ?: '');
        if (in_array($ext, ['xlsx', 'xls'], true) && !class_exists(\ZipArchive::class)) {
            return redirect()->back()->with('error', 'Excel import requires PHP zip extension (ZipArchive). Please upload CSV file or enable php_zip.');
        }

        $path = $file->path();
        $rows = $this->readImportRows($path, 30);

        $headerIndex = $this->detectHeaderIndex($rows);
        $headers = $rows[$headerIndex] ?? [];
        $headerMap = $this->buildHeaderMap($headers);
        if (!$this->hasRequiredImportHeaders($headerMap)) {
            return redirect()->back()->with('error', 'Could not detect required headers (ID, Student Name, Contact Number). Please check your file format.');
        }

        $lines = [];
        for ($i = $headerIndex + 1; $i < min(count($rows), $headerIndex + 6); $i++) {
            $lines[] = $rows[$i];
        }

        $extension = $file->getClientOriginalExtension() ?: 'csv';
        $filename = Str::random(18) . '.' . strtolower($extension);
        $file->storeAs('csv_import', $filename);

        $redirect = url()->previous();

        return view('admin.studentBasicInfos.parseImport', compact(
            'headers',
            'lines',
            'filename',
            'redirect',
            'headerIndex'
        ));
    }

    public function processStudentImport(Request $request, StudentImportService $studentImportService)
    {
        abort_if(Gate::denies('student_basic_info_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'filename' => 'required|string',
            'redirect' => 'required|string',
            'headerIndex' => 'required|integer|min:0',
        ]);

        $path = storage_path('app/csv_import/' . $request->input('filename'));
        abort_unless(File::exists($path), Response::HTTP_NOT_FOUND, 'Import file not found.');
        $ext = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, ['xlsx', 'xls'], true) && !class_exists(\ZipArchive::class)) {
            return redirect($request->input('redirect'))->with('error', 'Excel processing requires PHP zip extension (ZipArchive). Please use CSV.');
        }

        $rows = $this->readImportRows($path);

        $headerIndex = (int) $request->input('headerIndex', 0);
        $headers = $rows[$headerIndex] ?? [];
        $headerMap = $this->buildHeaderMap($headers);
        if (!$this->hasRequiredImportHeaders($headerMap)) {
            File::delete($path);
            return redirect($request->input('redirect'))->with('error', 'Could not detect required headers (ID, Student Name, Contact Number). Import aborted.');
        }

        $summary = [
            'created' => 0,
            'updated' => 0,
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
                $normalized = $this->normalizeImportRow($row, $headerMap);
                $normalized['academic_background_id'] = $this->resolveAcademicBackgroundId(
                    (string) ($normalized['academic_background_name'] ?? '')
                );
                $result = $studentImportService->importRow($normalized);

                if ($result['status'] === 'created') {
                    $summary['created']++;
                } elseif ($result['status'] === 'updated') {
                    $summary['updated']++;
                }
            } catch (\Throwable $exception) {
                $summary['failed']++;
                $summary['errors'][] = "Row {$sourceRowNumber}: {$exception->getMessage()}";
            }
        }

        File::delete($path);

        $message = "Import completed. Created: {$summary['created']}, Updated: {$summary['updated']}, Failed: {$summary['failed']}.";
        session()->flash('message', $message);

        if (!empty($summary['errors'])) {
            session()->flash('import_errors', array_slice($summary['errors'], 0, 25));
        }

        return redirect($request->input('redirect'));
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
            $hasId = array_key_exists('id', $headerMap);
            $hasName = array_key_exists('student name', $headerMap) || array_key_exists('name', $headerMap);
            $hasMobile = array_key_exists('contact number', $headerMap) || array_key_exists('mobile', $headerMap);

            if ($hasId && $hasName && $hasMobile) {
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
     * @param array<int, mixed> $row
     * @param array<string, int> $headerMap
     * @return array<string, mixed>
     */
    protected function normalizeImportRow(array $row, array $headerMap): array
    {
        $idNo = $this->valueByHeader($row, $headerMap, ['id']);
        $classRollRaw = $this->valueByHeader($row, $headerMap, ['class roll', 'roll']);
        $name = $this->valueByHeader($row, $headerMap, ['student name', 'name']);
        $mobile = $this->valueByHeader($row, $headerMap, ['contact number', 'mobile']);
        $guardianContactRaw = $this->valueByHeader($row, $headerMap, ['guardian contact', 'guardian']);
        $fathersName = $this->valueByHeader($row, $headerMap, ["father s name", "father name"]);
        $mothersName = $this->valueByHeader($row, $headerMap, ["mother s name", "mother name"]);
        $dobRaw = $this->valueByHeader($row, $headerMap, ['dob', 'date of birth']);
        $genderRaw = $this->valueByHeader($row, $headerMap, ['gender']);
        $address = $this->valueByHeader($row, $headerMap, [
            'address',
            'student address',
            'present address',
            'current address',
            'permanent address',
        ]);
        $bloodGroup = $this->valueByHeader($row, $headerMap, ['blood group']);
        $className = $this->valueByHeader($row, $headerMap, ['class']);
        $groupName = $this->valueByHeader($row, $headerMap, ['academic background', 'group']);
        $joiningDateRaw = $this->valueByHeader($row, $headerMap, ['admission date', 'joining date']);
        $activeStatusRaw = $this->valueByHeader($row, $headerMap, ['active status']);
        $email = $this->valueByHeader($row, $headerMap, ['email']);
        $password = $this->valueByHeader($row, $headerMap, ['password']);

        $roll = null;
        if ($classRollRaw !== '' && is_numeric($classRollRaw)) {
            $roll = (int) $classRollRaw;
        } elseif (is_numeric($idNo)) {
            $roll = (int) $idNo;
        }
        $idNo = $idNo !== '' ? $idNo : null;
        $firstName = $name !== '' ? $name : 'Unknown';
        $mobile = $this->normalizePhone($mobile);
        [$guardianContactParsed, $guardianRelationParsed] = $this->parseGuardianContactAndRelation($guardianContactRaw);
        $guardianContact = $this->normalizePhone($guardianContactParsed);

        $activeStatus = strtolower($activeStatusRaw);
        $status = in_array($activeStatus, ['yes', 'active', '1', 'true'], true) ? '1' : '0';

        $dob = $this->normalizeDateValue($dobRaw, 'Y-m-d') ?? '2000-01-01';
        $gender = $this->normalizeGender($genderRaw);

        $joiningDate = $this->normalizeDateValue($joiningDateRaw, 'Y-m-d H:i:s');

        return [
            'roll' => $roll,
            'id_no' => $idNo,
            'first_name' => $firstName,
            'last_name' => null,
            'gender' => $gender,
            'dob' => $dob,
            'contact_number' => $mobile !== '' ? $mobile : ('MISSING-' . Str::random(8)),
            'email' => $email !== '' ? $email : null,
            'class_id' => $this->resolveClassId($className),
            'section_id' => null,
            'shift_id' => null,
            'academic_background_id' => null,
            'academic_background_name' => $groupName !== '' ? $groupName : null,
            'joining_date' => $joiningDate,
            'status' => $status,
            'fathers_name' => $fathersName !== '' ? $fathersName : null,
            'mothers_name' => $mothersName !== '' ? $mothersName : null,
            'guardian_name' => null,
            'guardian_relation' => $guardianRelationParsed ?? 'Other',
            'guardian_contact_number' => $guardianContact !== '' ? $guardianContact : ($mobile !== '' ? $mobile : 'N/A'),
            'guardian_email' => null,
            'address' => $address !== '' ? $address : null,
            'student_blood_group' => $bloodGroup !== '' ? $bloodGroup : null,
            'user_name' => null,
            'password' => $password !== '' ? $password : null,
        ];
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
     * @param array<int, string> $values
     */
    protected function isMeaningfulRawRow(array $values): bool
    {
        $nonEmptyIndexes = [];
        foreach ($values as $index => $value) {
            if (trim((string) $value) !== '') {
                $nonEmptyIndexes[] = $index;
            }
        }

        if (count($nonEmptyIndexes) === 0) {
            return false;
        }

        // Skip rows like ["477","","",""...] which are trailing/garbage rows.
        if (count($nonEmptyIndexes) === 1 && $nonEmptyIndexes[0] === 0) {
            $first = trim((string) ($values[0] ?? ''));
            if ($first !== '' && is_numeric($first)) {
                return false;
            }
        }

        return true;
    }

    protected function resolveAcademicBackgroundId(string $groupName): ?int
    {
        $name = trim($groupName);
        if ($name === '' || $name === '-' || strcasecmp($name, 'no') === 0) {
            return null;
        }

        $cacheKey = strtolower($name);
        if (array_key_exists($cacheKey, $this->academicBackgroundCache)) {
            return $this->academicBackgroundCache[$cacheKey];
        }

        $record = AcademicBackground::firstOrCreate(['name' => $name]);
        $this->academicBackgroundCache[$cacheKey] = $record->id;

        return $record->id;
    }

    protected function resolveClassId(string $className): ?int
    {
        $name = trim($className);
        if ($name === '' || $name === '-' || strcasecmp($name, 'no') === 0) {
            return null;
        }

        $cacheKey = strtolower($name);
        if (array_key_exists($cacheKey, $this->academicClassCache)) {
            return $this->academicClassCache[$cacheKey];
        }

        $record = AcademicClass::firstOrCreate(['class_name' => $name]);
        $this->academicClassCache[$cacheKey] = $record->id;

        return $record->id;
    }

    /**
     * @param array<string, int> $headerMap
     */
    protected function hasRequiredImportHeaders(array $headerMap): bool
    {
        return array_key_exists('id', $headerMap)
            && (array_key_exists('student name', $headerMap) || array_key_exists('name', $headerMap))
            && (array_key_exists('contact number', $headerMap) || array_key_exists('mobile', $headerMap));
    }

    protected function normalizeGender(string $value): string
    {
        $v = strtolower(trim($value));
        if (in_array($v, ['male', 'm'], true)) {
            return 'male';
        }
        if (in_array($v, ['female', 'f'], true)) {
            return 'female';
        }

        return 'others';
    }

    protected function normalizePhone(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        $digits = preg_replace('/\D+/', '', $value) ?? '';
        if (strlen($digits) === 10) {
            return '0' . $digits;
        }
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            return $digits;
        }

        return $digits !== '' ? $digits : $value;
    }

    protected function normalizeDateValue(string $raw, string $format): ?string
    {
        $raw = trim($raw);
        if ($raw === '' || strtolower($raw) === 'no date') {
            return null;
        }

        // Excel serial date handling.
        if (is_numeric($raw)) {
            try {
                $base = Carbon::create(1899, 12, 30, 0, 0, 0);
                $date = $base->copy()->addDays((int) floor((float) $raw));
                return $date->format($format);
            } catch (\Throwable $e) {
                // Fallback to normal parser below.
            }
        }

        try {
            return Carbon::parse($raw)->format($format);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * @return array{0:string,1:string|null}
     */
    protected function parseGuardianContactAndRelation(string $raw): array
    {
        $value = trim($raw);
        if ($value === '') {
            return ['', null];
        }

        $relation = null;
        if (preg_match('/([A-Za-z])\s*$/', $value, $matches) === 1) {
            $code = strtolower($matches[1]);
            $relationMap = [
                'f' => 'Father',
                'm' => 'Mother',
                'b' => 'Brother',
                's' => 'Sister',
            ];

            if (array_key_exists($code, $relationMap)) {
                $relation = $relationMap[$code];
                $value = trim(substr($value, 0, -strlen($matches[0])));
            }
        }

        return [$value, $relation];
    }

    /**
     * @return array<int, array<int, string>>
     */
    protected function readImportRows(string $path, ?int $limit = null): array
    {
        $extension = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));

        if ($extension === 'xlsx' && class_exists(\ZipArchive::class)) {
            return $this->readXlsxRows($path, $limit);
        }

        return $this->readRowsWithSpreadsheetReader($path, $limit);
    }

    /**
     * @return array<int, array<int, string>>
     */
    protected function readRowsWithSpreadsheetReader(string $path, ?int $limit = null): array
    {
        // On some PHP versions SpreadsheetReader_XLSX emits a warning:
        // "continue targeting switch is equivalent to break".
        // Ignore only that known warning so import can proceed.
        $previousHandler = set_error_handler(function ($severity, $message, $file) {
            $isKnownContinueWarning = str_contains((string) $message, '"continue" targeting switch is equivalent to "break"')
                && str_contains((string) $file, 'SpreadsheetReader_XLSX.php');

            if ($isKnownContinueWarning) {
                return true;
            }

            return false;
        });

        try {
            $reader = new SpreadsheetReader($path);
        } finally {
            restore_error_handler();
        }

        $rows = [];
        foreach ($reader as $row) {
            $rows[] = array_map(fn($value) => trim((string) $value), is_array($row) ? $row : []);
            if ($limit !== null && count($rows) >= $limit) {
                break;
            }
        }

        return $rows;
    }

    /**
     * Lightweight XLSX reader to avoid SpreadsheetReader_XLSX PHP warning.
     *
     * @return array<int, array<int, string>>
     */
    protected function readXlsxRows(string $path, ?int $limit = null): array
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

                $col = preg_replace('/\d+/', '', $ref) ?: '';
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

            if ($limit !== null && count($rows) >= $limit) {
                break;
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
}
