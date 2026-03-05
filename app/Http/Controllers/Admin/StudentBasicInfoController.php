<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyStudentBasicInfoRequest;
use App\Http\Requests\StoreStudentBasicInfoRequest;
use App\Http\Requests\UpdateStudentBasicInfoRequest;
use App\Models\AcademicClass;
use App\Models\AcademicBackground;
use App\Models\Batch;
use App\Models\Section;
use App\Models\Shift;
use App\Models\StudentBasicInfo;
use App\Models\StudentDetailsInformation;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
// use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class StudentBasicInfoController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

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

        return view('admin.studentBasicInfos.index');
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

        return view('admin.studentBasicInfos.create', compact('classes', 'sections', 'shifts', 'academicBackgrounds', 'subjects', 'users', 'batches'));
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
        $studentBasicInfo->roll = $request->roll;
        $studentBasicInfo->id_no = $request->id_no;
        $studentBasicInfo->first_name = $request->first_name;
        $studentBasicInfo->last_name = $request->last_name;
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
                'name' => $request->first_name . ' ' . $request->last_name,
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
            'last_name' => $request->last_name,
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
                        'name' => $request->first_name . ' ' . $request->last_name,
                        'email' => $request->email,
                        'password' => isset($request->password) && !empty($request->password) ? bcrypt($request->password) : bcrypt($request->email),
                    ]);
                }
                $studentBasicInfo->user_id = $user->id;
                $studentBasicInfo->save();
            } else {
                $user = $studentBasicInfo->user;
                $userData = [
                    'name' => $request->first_name . ' ' . $request->last_name,
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

        return view('admin.studentBasicInfos.show', compact('studentBasicInfo', 'attendancePercent', 'score', 'subjects'));
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
}
