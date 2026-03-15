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
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class EarningsController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('earning_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $earnings = Earning::with(['earning_category', 'student', 'subject', 'created_by', 'updated_by', 'media'])->get();

        return view('admin.earnings.index', compact('earnings'));
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
