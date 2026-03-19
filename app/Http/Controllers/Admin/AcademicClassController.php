<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyAcademicClassRequest;
use App\Http\Requests\StoreAcademicClassRequest;
use App\Http\Requests\UpdateAcademicClassRequest;
use App\Models\AcademicClass;
use App\Models\Section;
use App\Models\Shift;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class AcademicClassController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('academic_class_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = AcademicClass::with(['class_sections', 'class_shifts'])->select(sprintf('%s.*', (new AcademicClass)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'academic_class_show';
                $editGate      = 'academic_class_edit';
                $deleteGate    = 'academic_class_delete';
                $crudRoutePart = 'academic-classes';

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
            $table->editColumn('class_name', function ($row) {
                return $row->class_name ? $row->class_name : '';
            });

            $table->editColumn('class_code', function ($row) {
                return $row->class_code ? $row->class_code : '';
            });
            $table->editColumn('class_section', function ($row) {
                $labels = [];
                foreach ($row->class_sections as $class_section) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $class_section->section_name);
                }

                return implode(' ', $labels);
            });
            $table->editColumn('class_shift', function ($row) {
                $labels = [];
                foreach ($row->class_shifts as $class_shift) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $class_shift->shift_name);
                }

                return implode(' ', $labels);
            });

            $table->rawColumns(['actions', 'placeholder', 'class_section', 'class_shift']);

            return $table->make(true);
        }

        return view('admin.academicClasses.index');
    }

    public function create()
    {
        abort_if(Gate::denies('academic_class_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $class_sections = Section::pluck('section_name', 'id');

        $class_shifts = Shift::pluck('shift_name', 'id');

        return view('admin.academicClasses.create', compact('class_sections', 'class_shifts'));
    }

    public function store(StoreAcademicClassRequest $request)
    {
        $academicClass = AcademicClass::create($request->all());
        $academicClass->class_sections()->sync($request->input('class_sections', []));
        $academicClass->class_shifts()->sync($request->input('class_shifts', []));

        return redirect()->route('admin.academic-classes.index');
    }

    public function edit(AcademicClass $academicClass)
    {
        abort_if(Gate::denies('academic_class_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $class_sections = Section::pluck('section_name', 'id');

        $class_shifts = Shift::pluck('shift_name', 'id');

        $academicClass->load('class_sections', 'class_shifts');

        return view('admin.academicClasses.edit', compact('academicClass', 'class_sections', 'class_shifts'));
    }

    public function update(UpdateAcademicClassRequest $request, AcademicClass $academicClass)
    {
        $academicClass->update($request->all());
        $academicClass->class_sections()->sync($request->input('class_sections', []));
        $academicClass->class_shifts()->sync($request->input('class_shifts', []));

        return redirect()->route('admin.academic-classes.index');
    }

    public function show(AcademicClass $academicClass)
    {
        abort_if(Gate::denies('academic_class_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $academicClass->load('class_sections', 'class_shifts', 'classStudentBasicInfos');

        return view('admin.academicClasses.show', compact('academicClass'));
    }

    public function destroy(AcademicClass $academicClass)
    {
        abort_if(Gate::denies('academic_class_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $academicClass->delete();

        return back();
    }

    public function massDestroy(MassDestroyAcademicClassRequest $request)
    {
        $academicClasses = AcademicClass::find(request('ids'));

        foreach ($academicClasses as $academicClass) {
            $academicClass->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
