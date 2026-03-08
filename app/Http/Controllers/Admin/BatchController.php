<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyBatchRequest;
use App\Http\Requests\StoreBatchRequest;
use App\Http\Requests\UpdateBatchRequest;
use App\Models\AcademicClass;
use App\Models\Batch;
use App\Models\StudentBasicInfo;
use App\Models\Subject;
// use Gate;
use Illuminate\Http\Request;
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
    public function manage(Batch $batch)
    {
        // return $batch;
        // abort_if(Gate::denies('batch_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $batches = Batch::with('students')->get();

        return view('admin.batches.manage', compact('batch'));
    }
}
