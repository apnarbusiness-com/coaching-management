<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyStudentDetailsInformationRequest;
use App\Http\Requests\StoreStudentDetailsInformationRequest;
use App\Http\Requests\UpdateStudentDetailsInformationRequest;
use App\Models\StudentBasicInfo;
use App\Models\StudentDetailsInformation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class StudentDetailsInformationController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('student_details_information_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = StudentDetailsInformation::with(['student'])->select(sprintf('%s.*', (new StudentDetailsInformation)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'student_details_information_show';
                $editGate      = 'student_details_information_edit';
                $deleteGate    = 'student_details_information_delete';
                $crudRoutePart = 'student-details-informations';

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
            $table->editColumn('fathers_name', function ($row) {
                return $row->fathers_name ? $row->fathers_name : '';
            });
            $table->editColumn('mothers_name', function ($row) {
                return $row->mothers_name ? $row->mothers_name : '';
            });
            $table->editColumn('fathers_nid', function ($row) {
                return $row->fathers_nid ? $row->fathers_nid : '';
            });
            $table->editColumn('mothers_nid', function ($row) {
                return $row->mothers_nid ? $row->mothers_nid : '';
            });
            $table->editColumn('guardian_name', function ($row) {
                return $row->guardian_name ? $row->guardian_name : '';
            });
            $table->editColumn('guardian_relation', function ($row) {
                return $row->guardian_relation ? $row->guardian_relation : '';
            });
            $table->editColumn('guardian_contact_number', function ($row) {
                return $row->guardian_contact_number ? $row->guardian_contact_number : '';
            });
            $table->editColumn('student_birth_no', function ($row) {
                return $row->student_birth_no ? $row->student_birth_no : '';
            });
            $table->addColumn('student_id_no', function ($row) {
                return $row->student ? $row->student->id_no : '';
            });

            $table->editColumn('student.first_name', function ($row) {
                return $row->student ? (is_string($row->student) ? $row->student : $row->student->first_name) : '';
            });
            $table->editColumn('student.last_name', function ($row) {
                return $row->student ? (is_string($row->student) ? $row->student : $row->student->last_name) : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'student']);

            return $table->make(true);
        }

        return view('admin.studentDetailsInformations.index');
    }

    public function create()
    {
        abort_if(Gate::denies('student_details_information_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $students = StudentBasicInfo::pluck('id_no', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.studentDetailsInformations.create', compact('students'));
    }

    public function store(StoreStudentDetailsInformationRequest $request)
    {
        $studentDetailsInformation = StudentDetailsInformation::create($request->all());

        return redirect()->route('admin.student-details-informations.index');
    }

    public function edit(StudentDetailsInformation $studentDetailsInformation)
    {
        abort_if(Gate::denies('student_details_information_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $students = StudentBasicInfo::pluck('id_no', 'id')->prepend(trans('global.pleaseSelect'), '');

        $studentDetailsInformation->load('student');

        return view('admin.studentDetailsInformations.edit', compact('studentDetailsInformation', 'students'));
    }

    public function update(UpdateStudentDetailsInformationRequest $request, StudentDetailsInformation $studentDetailsInformation)
    {
        $studentDetailsInformation->update($request->all());

        return redirect()->route('admin.student-details-informations.index');
    }

    public function show(StudentDetailsInformation $studentDetailsInformation)
    {
        abort_if(Gate::denies('student_details_information_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $studentDetailsInformation->load('student');

        return view('admin.studentDetailsInformations.show', compact('studentDetailsInformation'));
    }

    public function destroy(StudentDetailsInformation $studentDetailsInformation)
    {
        abort_if(Gate::denies('student_details_information_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $studentDetailsInformation->delete();

        return back();
    }

    public function massDestroy(MassDestroyStudentDetailsInformationRequest $request)
    {
        $studentDetailsInformations = StudentDetailsInformation::find(request('ids'));

        foreach ($studentDetailsInformations as $studentDetailsInformation) {
            $studentDetailsInformation->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
