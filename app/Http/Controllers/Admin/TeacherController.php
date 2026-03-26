<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyTeacherRequest;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Role;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class TeacherController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('teacher_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teachers = Teacher::with(['user', 'subjects', 'media'])->get();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        abort_if(Gate::denies('teacher_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $subjects = Subject::pluck('name', 'id');

        return view('admin.teachers.create', compact('subjects'));
    }

    public function store(StoreTeacherRequest $request)
    {
        // 1. Create User account first
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $password = $request->password ?? $request->email; // Use password if provided, else email
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($password), // Use password if provided, else email
            ]);
            $teacherRole = Role::where('title', 'Teacher')->first();
            // Assign 'User' role (ID 2)
            if (isset($teacherRole)) {
                $user->roles()->sync([$teacherRole->id]);
            }else{
                $user->roles()->sync([2]); // Default 'User' role
            }
            
        }

        // 2. Create Teacher and link to User
        $teacherData = $request->all();
        $teacherData['user_id'] = $user->id;
        $teacherData['status'] = 1; // Always active by default

        $teacher = Teacher::create($teacherData);
        $teacher->subjects()->sync($request->input('subjects', []));

        if ($request->input('profile_img', false)) {
            $teacher->addMedia(storage_path('tmp/uploads/' . basename($request->input('profile_img'))))->toMediaCollection('profile_img');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $teacher->id]);
        }

        return redirect()->route('admin.teachers.index');
    }

    public function edit(Teacher $teacher)
    {
        abort_if(Gate::denies('teacher_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $subjects = Subject::pluck('name', 'id');

        $teacher->load('user', 'subjects');

        return view('admin.teachers.edit', compact('subjects', 'teacher', 'users'));
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $teacher->update($request->all());
        $teacher->subjects()->sync($request->input('subjects', []));
        if ($request->input('profile_img', false)) {
            if (!$teacher->profile_img || $request->input('profile_img') !== $teacher->profile_img->file_name) {
                if ($teacher->profile_img) {
                    $teacher->profile_img->delete();
                }
                $teacher->addMedia(storage_path('tmp/uploads/' . basename($request->input('profile_img'))))->toMediaCollection('profile_img');
            }
        } elseif ($teacher->profile_img) {
            $teacher->profile_img->delete();
        }

        return redirect()->route('admin.teachers.index');
    }

    public function show(Teacher $teacher)
    {
        abort_if(Gate::denies('teacher_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teacher->load('user', 'subjects', 'teacherExpenses', 'teacherTeachersPayments');

        return view('admin.teachers.show', compact('teacher'));
    }

    public function destroy(Teacher $teacher)
    {
        abort_if(Gate::denies('teacher_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teacher->delete();

        return back();
    }

    public function massDestroy(MassDestroyTeacherRequest $request)
    {
        $teachers = Teacher::find(request('ids'));

        foreach ($teachers as $teacher) {
            $teacher->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('teacher_create') && Gate::denies('teacher_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new Teacher();
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function toggleStatus(Request $request, Teacher $teacher)
    {
        abort_if(Gate::denies('teacher_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teacher->status = $request->boolean('status');
        $teacher->save();

        return response()->json(['status' => (int) $teacher->status]);
    }


    public function idCard($id)
    {
        abort_if(Gate::denies('teacher_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teacher = Teacher::with('user')->findOrFail($id);

        return view('admin.teachers.id_card', compact('teacher'));
    }

}
