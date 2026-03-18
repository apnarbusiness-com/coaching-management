<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyBatchRequest;
use App\Http\Requests\StoreBatchRequest;
use App\Http\Requests\UpdateBatchRequest;
use App\Models\AcademicClass;
use App\Models\Batch;
use App\Models\StudentBasicInfo;
use Carbon\Carbon;
use App\Models\Subject;
use App\Models\Teacher;
// use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('batch_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Batch::with(['subject', 'subjects', 'class', 'students'])->select(sprintf('%s.*', (new Batch)->table));
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
            $table->addColumn('class_days_display', function ($row) {
                if (! is_array($row->class_days)) {
                    return '';
                }

                $labels = [];
                foreach ($row->class_days as $day) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', Batch::CLASS_DAY_SELECT[$day] ?? $day);
                }

                return implode(' ', $labels);
            });
            $table->editColumn('class_time', function ($row) {
                return $row->class_time ? date('h:i A', strtotime($row->class_time)) : '';
            });
            $table->addColumn('students_count', function ($row) {
                return $row->students->count();
            });

            $table->rawColumns(['actions', 'placeholder', 'class_days_display']);

            return $table->make(true);
        }

        return view('admin.batches.index');
    }

    public function create()
    {
        abort_if(Gate::denies('batch_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $subjects = Subject::pluck('name', 'id');
        $classes  = AcademicClass::pluck('class_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $students = StudentBasicInfo::orderBy('first_name')
            ->get()
            ->mapWithKeys(function ($student) {
                $name = trim($student->first_name . ' ' . $student->last_name);
                $idNo = $student->id_no ? ' - ' . $student->id_no : '';

                return [$student->id => $name . $idNo];
            });

        return view('admin.batches.create', compact('subjects', 'classes', 'students'));
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
        $students = StudentBasicInfo::orderBy('first_name')
            ->get()
            ->mapWithKeys(function ($student) {
                $name = trim($student->first_name . ' ' . $student->last_name);
                $idNo = $student->id_no ? ' - ' . $student->id_no : '';

                return [$student->id => $name . $idNo];
            });

        $batch->load('subject', 'subjects', 'class', 'students');

        return view('admin.batches.edit', compact('batch', 'subjects', 'classes', 'students'));
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

        return view('admin.batches.show', compact('batch'));
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
                $rows[] = [
                    'batch_id' => $batch->id,
                    'student_basic_info_id' => $studentId,
                    'enrolled_at' => $enrolledAt,
                    'per_student_discount' => 0,
                    'custom_monthly_fee' => null,
                ];
            }

            DB::table('batch_student_basic_info')->insert($rows);
        }

        return redirect()
            ->route('admin.batches.assignStudents', [$batch->id, 'month' => $month, 'year' => $year])
            ->with('status', 'Students updated successfully.');
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

    public function assignTeachers(Batch $batch)
    {
        abort_if(Gate::denies('batch_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $batch->load(['subject', 'subjects', 'class', 'teachers']);

        $teachers = Teacher::orderBy('name')->get();

        return view('admin.batches.assign_teachers', compact('batch', 'teachers'));
    }

    public function storeAssignedTeacher(Request $request, Batch $batch)
    {
        abort_if(Gate::denies('batch_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'teacher_id'    => ['required', 'integer', 'exists:teachers,id'],
            'salary_amount' => ['required', 'numeric', 'min:0'],
            'role'          => ['nullable', 'string', 'in:primary,assistant'],
        ]);

        $batch->teachers()->syncWithoutDetaching([
            $data['teacher_id'] => [
                'salary_amount' => $data['salary_amount'],
                'role'          => $data['role'] ?? null,
            ],
        ]);

        return redirect()
            ->route('admin.batches.assignTeachers', $batch->id)
            ->with('status', 'Teacher assignment saved.');
    }

    public function removeAssignedTeacher(Batch $batch, Teacher $teacher)
    {
        abort_if(Gate::denies('batch_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $batch->teachers()->detach($teacher->id);

        return back()->with('status', 'Teacher removed from batch.');
    }
}
