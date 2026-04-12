<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyBatchRequest;
use App\Http\Requests\StoreBatchRequest;
use App\Http\Requests\UpdateBatchRequest;
use App\Models\AcademicClass;
use App\Models\Batch;
use App\Models\ClassRoom;
use App\Models\Earning;
use App\Models\StudentBasicInfo;
use App\Models\StudentMonthlyDue;
use App\Services\DueCalculationService;
use Carbon\Carbon;
use App\Models\Subject;
use App\Models\Teacher;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BatchController extends Controller
{
    protected $dueService;

    public function __construct(DueCalculationService $dueService)
    {
        $this->dueService = $dueService;
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('batch_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $summary = $this->buildBatchIndexSummary($month, $year);

        if ($request->ajax()) {
            $enrolledCountSub = DB::table('batch_student_basic_info')
                ->selectRaw('count(distinct student_basic_info_id)')
                ->whereColumn('batch_student_basic_info.batch_id', 'batches.id')
                ->whereMonth('enrolled_at', $month)
                ->whereYear('enrolled_at', $year);

            $dueRemainingSub = DB::table('student_monthly_dues')
                ->selectRaw('coalesce(sum(due_remaining), 0)')
                ->whereColumn('student_monthly_dues.batch_id', 'batches.id')
                ->where('month', $month)
                ->where('year', $year);

            $query = Batch::with(['subject', 'subjects', 'class'])
                ->select(sprintf('%s.*', (new Batch)->table))
                ->selectSub($enrolledCountSub, 'students_count')
                ->selectSub($dueRemainingSub, 'total_due_remaining');
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'batch_show';
                $editGate      = 'batch_edit';
                $deleteGate    = 'batch_delete';
                $crudRoutePart = 'batches';

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
            $table->editColumn('batch_name', function ($row) {
                return $row->batch_name ? $row->batch_name : '';
            });
            $table->addColumn('subject_names', function ($row) {
                $subjects = $row->subjects->pluck('name')->filter()->unique()->values();

                if ($subjects->isEmpty() && $row->subject) {
                    $subjects = collect([$row->subject->name]);
                }

                return $subjects->implode(', ');
            });
            $table->addColumn('class_class_name', function ($row) {
                return $row->class ? $row->class->class_name : '';
            });
            $table->editColumn('fee_type', function ($row) {
                return Batch::FEE_TYPE_SELECT[$row->fee_type] ?? '';
            });
            $table->editColumn('fee_amount', function ($row) {
                return $row->fee_amount !== null ? number_format((float) $row->fee_amount, 2) : '';
            });
            $table->editColumn('duration_in_months', function ($row) {
                return $row->duration_in_months ? $row->duration_in_months : '';
            });
            $table->addColumn('expected_income', function ($row) {
                $studentCount = (int) ($row->students_count ?? 0);
                $feeAmount = (float) ($row->fee_amount ?? 0);
                $duration = (int) ($row->duration_in_months ?? 0);
                $monthlyFee = $feeAmount;

                if ($row->fee_type === 'course' && $duration > 0) {
                    $monthlyFee = $feeAmount / $duration;
                }

                $expected = $monthlyFee * $studentCount;

                return number_format($expected, 2);
            });
            $table->addColumn('total_due_remaining', function ($row) {
                return number_format((float) ($row->total_due_remaining ?? 0), 2);
            });
            $table->addColumn('class_days_display', function ($row) {
                $schedule = $row->class_schedule ?? [];
                if (empty($schedule)) {
                    return '';
                }

                $labels = [];
                static $roomNames = null;
                if ($roomNames === null) {
                    $roomNames = ClassRoom::pluck('name', 'id');
                }
                foreach (Batch::DAY_ORDER as $day) {
                    if (isset($schedule[$day])) {
                        $entry = $schedule[$day];
                        $timeValue = is_array($entry) ? ($entry['time'] ?? null) : $entry;
                        $roomId = is_array($entry) ? ($entry['class_room_id'] ?? null) : null;
                        if (!$timeValue) {
                            continue;
                        }
                        $time = \Carbon\Carbon::parse($timeValue)->format('h:i A');
                        $roomLabel = $roomId ? ($roomNames[$roomId] ?? null) : null;
                        $suffix = $roomLabel ? ' (' . $roomLabel . ')' : '';
                        $labels[] = sprintf(
                            '<span class="label label-info label-many">%s: %s%s</span>',
                            Batch::CLASS_DAY_SELECT[$day] ?? $day,
                            $time,
                            $suffix
                        );
                    }
                }

                return implode(' ', $labels);
            });
            $table->addColumn('students_count', function ($row) {
                return (int) ($row->students_count ?? 0);
            });

            $table->rawColumns(['actions', 'placeholder', 'class_days_display']);

            return $table->with('summary', $summary)->make(true);
        }

        return view('admin.batches.index', compact('month', 'year', 'summary'));
    }

    protected function buildBatchIndexSummary(int $month, int $year): array
    {
        $enrolledCountSub = DB::table('batch_student_basic_info')
            ->selectRaw('count(distinct student_basic_info_id)')
            ->whereColumn('batch_student_basic_info.batch_id', 'batches.id')
            ->whereMonth('enrolled_at', $month)
            ->whereYear('enrolled_at', $year);

        $batches = Batch::query()
            ->select('id', 'fee_type', 'fee_amount', 'duration_in_months')
            ->selectSub($enrolledCountSub, 'students_count')
            ->get();

        $totalExpected = 0;
        foreach ($batches as $batch) {
            $studentCount = (int) ($batch->students_count ?? 0);
            $feeAmount = (float) ($batch->fee_amount ?? 0);
            $duration = (int) ($batch->duration_in_months ?? 0);
            $monthlyFee = $feeAmount;

            if ($batch->fee_type === 'course' && $duration > 0) {
                $monthlyFee = $feeAmount / $duration;
            }

            $totalExpected += ($monthlyFee * $studentCount);
        }

        $totalEarnings = (float) Earning::where('earning_month', $month)
            ->where('earning_year', $year)
            ->sum('amount');

        $totalRemaining = (float) StudentMonthlyDue::where('month', $month)
            ->where('year', $year)
            ->sum('due_remaining');

        return [
            'total_expected' => $totalExpected,
            'total_earned' => $totalEarnings,
            'total_remaining' => $totalRemaining,
        ];
    }

    public function create()
    {
        abort_if(Gate::denies('batch_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $subjects = Subject::pluck('name', 'id');
        $classes  = AcademicClass::pluck('class_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $classRooms = ClassRoom::pluck('name', 'id');
        $students = StudentBasicInfo::orderBy('first_name')
            ->get()
            ->mapWithKeys(function ($student) {
                $name = trim($student->first_name . ' ' . $student->last_name);
                $idNo = $student->id_no ? ' - ' . $student->id_no : '';

                return [$student->id => $name . $idNo];
            });

        return view('admin.batches.create', compact('subjects', 'classes', 'classRooms', 'students'));
    }

    public function store(StoreBatchRequest $request)
    {
        $data       = $request->validated();
        $subjectIds = $request->input('subjects', []);
        $data['subject_id'] = $subjectIds[0];

        unset($data['subjects']);

        $batch = Batch::create($data);
        $batch->subjects()->sync($subjectIds);
        $batch->students()->sync($request->input('students', []));

        return redirect()->route('admin.batches.index');
    }

    public function edit(Batch $batch)
    {
        abort_if(Gate::denies('batch_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $subjects = Subject::pluck('name', 'id');
        $classes  = AcademicClass::pluck('class_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $classRooms = ClassRoom::pluck('name', 'id');
        $students = StudentBasicInfo::orderBy('first_name')
            ->get()
            ->mapWithKeys(function ($student) {
                $name = trim($student->first_name . ' ' . $student->last_name);
                $idNo = $student->id_no ? ' - ' . $student->id_no : '';

                return [$student->id => $name . $idNo];
            });

        $batch->load('subject', 'subjects', 'class', 'students');

        return view('admin.batches.edit', compact('batch', 'subjects', 'classes', 'classRooms', 'students'));
    }

    public function update(UpdateBatchRequest $request, Batch $batch)
    {
        $data       = $request->validated();
        $subjectIds = $request->input('subjects', []);
        $data['subject_id'] = $subjectIds[0];

        unset($data['subjects']);

        $batch->update($data);
        $batch->subjects()->sync($subjectIds);
        $batch->students()->sync($request->input('students', []));

        return redirect()->route('admin.batches.index');
    }

    public function show(Batch $batch)
    {
        abort_if(Gate::denies('batch_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $batch->load('subject', 'subjects', 'class', 'students');
        $classRooms = ClassRoom::pluck('name', 'id');

        return view('admin.batches.show', compact('batch', 'classRooms'));
    }

    public function destroy(Batch $batch)
    {
        abort_if(Gate::denies('batch_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $batch->delete();

        return back();
    }

    public function massDestroy(MassDestroyBatchRequest $request)
    {
        $batches = Batch::find(request('ids'));

        foreach ($batches as $batch) {
            $batch->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }




    // Custom method for batch management
    public function manage(Batch $batch, Request $request)
    {
        abort_if(Gate::denies('batch_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $batch->load(['subject', 'subjects', 'class', 'students', 'teachers']);

        $teacherCount = $batch->teachers->count();
        $enrolledStudentIds = DB::table('batch_student_basic_info')
            ->where('batch_id', $batch->id)
            ->whereMonth('enrolled_at', $month)
            ->whereYear('enrolled_at', $year)
            ->pluck('student_basic_info_id')
            ->unique()
            ->values();

        $enrolledStudents = StudentBasicInfo::with('class')
            ->whereIn('id', $enrolledStudentIds)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $studentCount = $enrolledStudents->count();

        $expectedIncome = $studentCount * (float) $batch->fee_amount;

        $incomeUntilNow = \App\Models\Earning::where('batch_id', $batch->id)
            ->where('earning_month', $month)
            ->where('earning_year', $year)
            ->whereNotNull('student_monthly_due_id')
            ->sum('amount');

        $totalIncome = \App\Models\Earning::where('batch_id', $batch->id)
            ->whereNotNull('student_monthly_due_id')
            ->sum('amount');

        return view('admin.batches.manage', compact(
            'batch',
            'teacherCount',
            'studentCount',
            'expectedIncome',
            'incomeUntilNow',
            'totalIncome',
            'month',
            'year',
            'enrolledStudents'
        ));
    }

    public function assignStudents(Batch $batch)
    {
        abort_if(Gate::denies('batch_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $month = request()->input('month', now()->month);
        $year = request()->input('year', now()->year);

        $batch->load(['class', 'students']);

        $students = StudentBasicInfo::with(['class', 'section', 'shift', 'studentEarnings'])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $assignedStudentIds = DB::table('batch_student_basic_info')
            ->where('batch_id', $batch->id)
            ->whereMonth('enrolled_at', $month)
            ->whereYear('enrolled_at', $year)
            ->pluck('student_basic_info_id')
            ->unique()
            ->values()
            ->all();

        return view('admin.batches.assign_students', compact(
            'batch',
            'students',
            'assignedStudentIds',
            'month',
            'year'
        ));
    }

    public function storeAssignedStudents(Request $request, Batch $batch)
    {
        abort_if(Gate::denies('batch_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'students'   => ['array'],
            'students.*' => ['integer', 'exists:student_basic_infos,id'],
            'month'      => ['required', 'integer', 'min:1', 'max:12'],
            'year'       => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $month = (int) $data['month'];
        $year = (int) $data['year'];
        $enrolledAt = Carbon::createFromDate($year, $month, 1)->toDateString();
        $selectedStudentIds = $data['students'] ?? [];

        $existingStudentIds = DB::table('batch_student_basic_info')
            ->where('batch_id', $batch->id)
            ->whereMonth('enrolled_at', $month)
            ->whereYear('enrolled_at', $year)
            ->pluck('student_basic_info_id')
            ->unique()
            ->values()
            ->all();

        $studentsToEnroll = array_values(array_diff($selectedStudentIds, $existingStudentIds));
        $studentsToRemove = array_values(array_diff($existingStudentIds, $selectedStudentIds));

        if (!empty($studentsToRemove)) {
            foreach ($studentsToRemove as $studentId) {
                $this->dueService->deleteDuesOnUnenroll($studentId, $batch->id, $month, $year);
            }
            DB::table('batch_student_basic_info')
                ->where('batch_id', $batch->id)
                ->whereMonth('enrolled_at', $month)
                ->whereYear('enrolled_at', $year)
                ->whereIn('student_basic_info_id', $studentsToRemove)
                ->delete();
        }

        if (!empty($studentsToEnroll)) {
            $rows = [];
            foreach ($studentsToEnroll as $studentId) {
                $student = StudentBasicInfo::find($studentId);
                $discount = $student ? $student->monthly_discount : 0;

                $rows[] = [
                    'batch_id' => $batch->id,
                    'student_basic_info_id' => $studentId,
                    'enrolled_at' => $enrolledAt,
                    'per_student_discount' => $discount,
                    'custom_monthly_fee' => null,
                ];

                $this->dueService->generateDueForEnrollment($studentId, $batch->id, $month, $year, $discount);
            }

            DB::table('batch_student_basic_info')->insert($rows);
        }

        return redirect()
            ->route('admin.batches.assignStudents', [$batch->id, 'month' => $month, 'year' => $year])
            ->with('status', 'Students updated successfully.');
    }

    public function quickEnrollStudents(Request $request, Batch $batch)
    {
        abort_if(Gate::denies('batch_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'student_ids' => ['required', 'string'],
            'month'       => ['required', 'integer', 'min:1', 'max:12'],
            'year'        => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $month = (int) $data['month'];
        $year = (int) $data['year'];
        $enrolledAt = Carbon::createFromDate($year, $month, 1)->toDateString();

        $idNos = array_filter(
            array_map('intval', preg_split('/[\s,]+/', $data['student_ids'])),
            fn($id) => $id > 0
        );

        if (empty($idNos)) {
            return redirect()
                ->route('admin.batches.manage', [$batch->id, 'month' => $month, 'year' => $year])
                ->with('error', 'No valid student ID numbers provided.');
        }

        $studentIds = DB::table('student_basic_infos')
            ->whereIn('id_no', $idNos)
            ->pluck('id')
            ->all();

        if (empty($studentIds)) {
            return redirect()
                ->route('admin.batches.manage', [$batch->id, 'month' => $month, 'year' => $year])
                ->with('error', 'No students found with the provided ID numbers.');
        }

        $existingStudentIds = DB::table('batch_student_basic_info')
            ->where('batch_id', $batch->id)
            ->whereMonth('enrolled_at', $month)
            ->whereYear('enrolled_at', $year)
            ->pluck('student_basic_info_id')
            ->unique()
            ->values()
            ->all();

        $studentsToEnroll = array_values(array_diff($studentIds, $existingStudentIds));

        if (!empty($studentsToEnroll)) {
            $validStudentIds = DB::table('student_basic_infos')
                ->whereIn('id', $studentsToEnroll)
                ->pluck('id')
                ->all();

            if (!empty($validStudentIds)) {
                $rows = [];
                foreach ($validStudentIds as $studentId) {
                    $student = StudentBasicInfo::find($studentId);
                    $discount = $student ? $student->monthly_discount : 0;

                    $rows[] = [
                        'batch_id' => $batch->id,
                        'student_basic_info_id' => $studentId,
                        'enrolled_at' => $enrolledAt,
                        'per_student_discount' => $discount,
                        'custom_monthly_fee' => null,
                    ];

                    $this->dueService->generateDueForEnrollment($studentId, $batch->id, $month, $year, $discount);
                }

                DB::table('batch_student_basic_info')->insert($rows);
            }
        }

        return redirect()
            ->route('admin.batches.manage', [$batch->id, 'month' => $month, 'year' => $year])
            ->with('status', count($studentsToEnroll) . ' student(s) enrolled successfully.');
    }

    public function unEnrollStudent(Request $request, Batch $batch, StudentBasicInfo $student)
    {
        abort_if(Gate::denies('batch_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $this->dueService->deleteDuesOnUnenroll($student->id, $batch->id, $month, $year);

        DB::table('batch_student_basic_info')
            ->where('batch_id', $batch->id)
            ->where('student_basic_info_id', $student->id)
            ->whereMonth('enrolled_at', $month)
            ->whereYear('enrolled_at', $year)
            ->delete();

        return redirect()
            ->route('admin.batches.manage', [$batch->id, 'month' => $month, 'year' => $year])
            ->with('status', 'Student un-enrolled successfully.');
    }

    public function quickEnrollStudentsAjax(Request $request, Batch $batch)
    {
        abort_if(Gate::denies('batch_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'student_ids' => ['required', 'string'],
            'month'       => ['required', 'integer', 'min:1', 'max:12'],
            'year'        => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $month = (int) $data['month'];
        $year = (int) $data['year'];
        $enrolledAt = Carbon::createFromDate($year, $month, 1)->toDateString();

        $idNos = array_filter(
            array_map('intval', preg_split('/[\s,]+/', $data['student_ids'])),
            fn($id) => $id > 0
        );

        if (empty($idNos)) {
            return response()->json(['success' => false, 'message' => 'No valid student ID numbers provided.']);
        }



        $studentIds = DB::table('student_basic_infos')
            ->whereIn('id_no', $idNos)
            ->pluck('id')
            ->all();

        if (empty($studentIds)) {
            return response()->json(['success' => false, 'message' => 'No students found with the provided ID numbers.']);
        }

        $existingStudentIds = DB::table('batch_student_basic_info')
            ->where('batch_id', $batch->id)
            ->whereMonth('enrolled_at', $month)
            ->whereYear('enrolled_at', $year)
            ->pluck('student_basic_info_id')
            ->unique()
            ->values()
            ->all();

        $studentsToEnroll = array_values(array_diff($studentIds, $existingStudentIds));



        if (!empty($studentsToEnroll)) {
            $validStudentIds = DB::table('student_basic_infos')
                ->whereIn('id', $studentsToEnroll)
                ->pluck('id')
                ->all();



            if (!empty($validStudentIds)) {
                $rows = [];
                foreach ($validStudentIds as $studentId) {
                    $student = StudentBasicInfo::find($studentId);
                    $discount = $student ? $student->monthly_discount : 0;

                    $rows[] = [
                        'batch_id' => $batch->id,
                        'student_basic_info_id' => $studentId,
                        'enrolled_at' => $enrolledAt,
                        'per_student_discount' => $discount ?? 0,
                        'custom_monthly_fee' => null,
                    ];

                    // return response()->json([
                    //     'success' => true,
                    //     // 'message' => 'Students enrolled successfully.',
                    //     // 'data' => $data,
                    //     'enrolledAt' => $enrolledAt,
                    //     'idNos' => $idNos,
                    //     'studentIds' => $studentIds,
                    //     'existingStudentIds' => $existingStudentIds,
                    //     'studentsToEnroll' => $studentsToEnroll,
                    //     'validStudentIds' => $validStudentIds,
                    //     'rows' => $rows
                    // ]);

                    $studentMonthlyDue = $this->dueService->generateDueForEnrollment($studentId, $batch->id, $month, $year, $discount);
                }


                DB::table('batch_student_basic_info')->insert($rows);
                // dd($rows, $studentMonthlyDue);
            }
        }

        return response()->json(['success' => true, 'message' => count($studentsToEnroll) . ' student(s) enrolled successfully.']);
    }

    public function unEnrollStudentAjax(Request $request, Batch $batch, StudentBasicInfo $student)
    {
        abort_if(Gate::denies('batch_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        // dd($month, $year,$student);

        // $this->dueService->deleteDuesOnUnenroll($student->id, $batch->id, $month, $year);
        try {
            $this->dueService->deleteDuesOnUnenroll(
                $student->id,
                $batch->id,
                $month,
                $year
            );
        } catch (\Exception $e) {
            Log::error('Failed to delete dues on unenroll', [
                'student_id' => $student->id,
                'batch_id' => $batch->id,
                'month' => $month,
                'year' => $year,
                'error' => $e->getMessage(),
            ]);
        }

        DB::table('batch_student_basic_info')
            ->where('batch_id', $batch->id)
            ->where('student_basic_info_id', $student->id)
            ->whereMonth('enrolled_at', $month)
            ->whereYear('enrolled_at', $year)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Student un-enrolled successfully.']);
    }

    public function getEnrolledStudentsAjax(Request $request, Batch $batch)
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $enrolledStudents = $batch->students()
            ->whereMonth('enrolled_at', $month)
            ->whereYear('enrolled_at', $year)
            ->get();

        $studentCount = $enrolledStudents->count();
        $capacity = $batch->capacity;
        $capacityText = $capacity ? $studentCount . '/' . $capacity : $studentCount . '/∞';
        $capacityPercent = $capacity ? min(100, round(($studentCount / max($capacity, 1)) * 100)) : null;

        return response()->json([
            'success' => true,
            'students' => $enrolledStudents->map(fn($s) => [
                'id' => $s->id,
                'first_name' => $s->first_name,
                'last_name' => $s->last_name,
                'id_no' => $s->id_no,
                'class_name' => $s->class->class_name ?? 'N/A',
            ]),
            'studentCount' => $studentCount,
            'capacityText' => $capacityText,
            'capacityPercent' => $capacityPercent,
        ]);
    }

    public function copyPreviousMonthEnrollments(Request $request, Batch $batch)
    {
        abort_if(Gate::denies('batch_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year'  => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $month = (int) $data['month'];
        $year = (int) $data['year'];

        $currentMonthStart = Carbon::createFromDate($year, $month, 1);
        $previousMonthStart = $currentMonthStart->copy()->subMonth();

        $prevMonth = (int) $previousMonthStart->month;
        $prevYear = (int) $previousMonthStart->year;

        $previousEnrollments = DB::table('batch_student_basic_info')
            ->where('batch_id', $batch->id)
            ->whereMonth('enrolled_at', $prevMonth)
            ->whereYear('enrolled_at', $prevYear)
            ->get();

        if ($previousEnrollments->isEmpty()) {
            return redirect()
                ->route('admin.batches.assignStudents', [$batch->id, 'month' => $month, 'year' => $year])
                ->with('status', 'Previous month has no enrolled students to copy.');
        }

        DB::table('batch_student_basic_info')
            ->where('batch_id', $batch->id)
            ->whereMonth('enrolled_at', $month)
            ->whereYear('enrolled_at', $year)
            ->delete();

        $enrolledAt = $currentMonthStart->toDateString();
        $rows = [];
        foreach ($previousEnrollments as $prev) {
            $rows[] = [
                'batch_id' => $batch->id,
                'student_basic_info_id' => $prev->student_basic_info_id,
                'enrolled_at' => $enrolledAt,
                'per_student_discount' => $prev->per_student_discount ?? 0,
                'custom_monthly_fee' => $prev->custom_monthly_fee ?? null,
            ];
        }

        DB::table('batch_student_basic_info')->insert($rows);

        return redirect()
            ->route('admin.batches.assignStudents', [$batch->id, 'month' => $month, 'year' => $year])
            ->with('status', 'Copied previous month enrollments successfully.');
    }

    public function copyPreviousMonthEnrollmentsAll(Request $request)
    {
        abort_if(Gate::denies('batch_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year'  => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $month = (int) $data['month'];
        $year = (int) $data['year'];

        $currentMonthStart = Carbon::createFromDate($year, $month, 1);
        $previousMonthStart = $currentMonthStart->copy()->subMonth();

        $prevMonth = (int) $previousMonthStart->month;
        $prevYear = (int) $previousMonthStart->year;

        $previousEnrollments = DB::table('batch_student_basic_info')
            ->whereMonth('enrolled_at', $prevMonth)
            ->whereYear('enrolled_at', $prevYear)
            ->get();

        if ($previousEnrollments->isEmpty()) {
            return redirect()
                ->route('admin.batches.index', ['month' => $month, 'year' => $year])
                ->with('status', 'Previous month has no enrolled students to copy.');
        }

        $grouped = $previousEnrollments->groupBy('batch_id');
        $enrolledAt = $currentMonthStart->toDateString();
        $totalInserted = 0;

        foreach ($grouped as $batchId => $rows) {
            DB::table('batch_student_basic_info')
                ->where('batch_id', $batchId)
                ->whereMonth('enrolled_at', $month)
                ->whereYear('enrolled_at', $year)
                ->delete();

            $insertRows = [];
            foreach ($rows as $prev) {
                $insertRows[] = [
                    'batch_id' => $prev->batch_id,
                    'student_basic_info_id' => $prev->student_basic_info_id,
                    'enrolled_at' => $enrolledAt,
                    'per_student_discount' => $prev->per_student_discount ?? 0,
                    'custom_monthly_fee' => $prev->custom_monthly_fee ?? null,
                ];
            }

            if (!empty($insertRows)) {
                DB::table('batch_student_basic_info')->insert($insertRows);
                $totalInserted += count($insertRows);
            }
        }

        return redirect()
            ->route('admin.batches.index', ['month' => $month, 'year' => $year])
            ->with('status', "Copied previous month enrollments for all batches. Total students enrolled: {$totalInserted}.");
    }

    public function assignTeachers(Batch $batch, Request $request)
    {
        abort_if(Gate::denies('batch_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $batch->load(['subject', 'subjects', 'class']);

        // Get teachers assigned for this specific month/year
        $assignedTeachers = DB::table('batch_teacher')
            ->where('batch_id', $batch->id)
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        $assignedTeacherIds = $assignedTeachers->pluck('teacher_id')->toArray();

        $teachers = Teacher::orderBy('name')->get();

        return view('admin.batches.assign_teachers', compact('batch', 'teachers', 'month', 'year', 'assignedTeachers', 'assignedTeacherIds'));
    }

    public function storeAssignedTeacher(Request $request, Batch $batch)
    {
        abort_if(Gate::denies('batch_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'teacher_id'    => ['required', 'integer', 'exists:teachers,id'],
            'salary_amount' => ['required', 'numeric', 'min:0'],
            'salary_amount_type' => ['required', 'string', 'in:fixed,percentage'],
            'role'          => ['nullable', 'string', 'in:primary,assistant'],
            'month'         => ['required', 'integer', 'min:1', 'max:12'],
            'year'          => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        // Check if assignment exists for this month/year
        $exists = DB::table('batch_teacher')
            ->where('batch_id', $batch->id)
            ->where('teacher_id', $data['teacher_id'])
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->exists();

        if ($exists) {
            // Update existing
            DB::table('batch_teacher')
                ->where('batch_id', $batch->id)
                ->where('teacher_id', $data['teacher_id'])
                ->where('month', $data['month'])
                ->where('year', $data['year'])
                ->update([
                    'salary_amount' => $data['salary_amount'],
                    'salary_amount_type' => $data['salary_amount_type'],
                    'role' => $data['role'] ?? null,
                    'updated_at' => now(),
                ]);
        } else {
            // Insert new
            DB::table('batch_teacher')->insert([
                'batch_id' => $batch->id,
                'teacher_id' => $data['teacher_id'],
                'salary_amount' => $data['salary_amount'],
                'salary_amount_type' => $data['salary_amount_type'],
                'role' => $data['role'] ?? null,
                'month' => $data['month'],
                'year' => $data['year'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()
            ->route('admin.batches.assignTeachers', [$batch->id, 'month' => $data['month'], 'year' => $data['year']])
            ->with('status', 'Teacher assignment saved.');
    }

    public function removeAssignedTeacher(Request $request, Batch $batch, Teacher $teacher)
    {
        abort_if(Gate::denies('batch_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        DB::table('batch_teacher')
            ->where('batch_id', $batch->id)
            ->where('teacher_id', $teacher->id)
            ->where('month', $month)
            ->where('year', $year)
            ->delete();

        return back()->with('status', 'Teacher removed from batch.');
    }
}
