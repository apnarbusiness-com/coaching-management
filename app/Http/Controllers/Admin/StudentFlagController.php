<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentBasicInfo;
use App\Models\StudentFlag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class StudentFlagController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('student_flag_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = StudentFlag::query()->select('student_flags.*');
            $table = DataTables::of($query);
            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                return '<button type="button" class="btn btn-xs btn-info edit-btn" data-id="' . $row->id . '">Edit</button>
                        <button type="button" class="btn btn-xs btn-danger delete-btn" data-id="' . $row->id . '">Delete</button>';
            });

            $table->editColumn('id', fn($row) => $row->id);
            $table->editColumn('name', fn($row) => $row->name);
            $table->editColumn('color', fn($row) => '<span style="display: inline-block; width: 24px; height: 24px; background-color: ' . $row->color . '; border-radius: 4px; border: 1px solid #ddd;"></span> <span>' . $row->color . '</span>');
            $table->editColumn('is_active', fn($row) => $row->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>');
            $table->editColumn('description', fn($row) => $row->description);

            $table->rawColumns(['actions', 'color', 'is_active', 'placeholder']);
            $table->setRowAttr(['data-entry-id' => fn($row) => $row->id]);

            return $table->make(true);
        }

        return view('admin.studentFlags.index');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('student_flag_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'name' => 'required|unique:student_flags,name',
            'color' => 'required',
        ]);

        StudentFlag::create($request->only(['name', 'color', 'description', 'is_active']));

        return response()->json(['success' => true, 'message' => 'Flag created successfully']);
    }

    public function edit($id)
    {
        abort_if(Gate::denies('student_flag_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $flag = StudentFlag::findOrFail($id);

        return response()->json($flag);
    }

    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('student_flag_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $flag = StudentFlag::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:student_flags,name,' . $id,
            'color' => 'required',
        ]);

        $flag->update($request->only(['name', 'color', 'description', 'is_active']));

        return response()->json(['success' => true, 'message' => 'Flag updated successfully']);
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('student_flag_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $flag = StudentFlag::findOrFail($id);
        $flag->delete();

        return response()->json(['success' => true, 'message' => 'Flag deleted successfully']);
    }

    public function getFlags()
    {
        $flags = StudentFlag::where('is_active', 1)->get(['id', 'name', 'color']);
        return response()->json($flags);
    }

    public function assignFlag(Request $request)
    {
        abort_if(Gate::denies('student_flag_assign'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'student_id' => 'required|exists:student_basic_infos,id',
            'flag_id' => 'required|exists:student_flags,id',
            'comment' => 'nullable|string',
        ]);

        $student = StudentBasicInfo::findOrFail($request->student_id);
        $flag = StudentFlag::findOrFail($request->flag_id);

        $student->flags()->syncWithoutDetaching([
            $flag->id => [
                'comment' => $request->comment,
                'created_by_id' => auth()->id(),
            ]
        ]);

        return response()->json(['success' => true, 'message' => 'Flag assigned successfully']);
    }

    public function removeFlag(Request $request)
    {
        abort_if(Gate::denies('student_flag_assign'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'student_id' => 'required|exists:student_basic_infos,id',
            'flag_id' => 'required|exists:student_flags,id',
        ]);

        $student = StudentBasicInfo::findOrFail($request->student_id);
        $student->flags()->detach($request->flag_id);

        return response()->json(['success' => true, 'message' => 'Flag removed successfully']);
    }

    public function getStudentFlags($studentId)
    {
        $student = StudentBasicInfo::findOrFail($studentId);
        $flags = $student->flags()->withPivot('comment')->get();

        return response()->json($flags);
    }
}