<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyAcademicBackgroundRequest;
use App\Http\Requests\StoreAcademicBackgroundRequest;
use App\Http\Requests\UpdateAcademicBackgroundRequest;
use App\Models\AcademicBackground;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AcademicBackgroundsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('academic_background_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $academicBackgrounds = AcademicBackground::all();

        return view('admin.academicBackgrounds.index', compact('academicBackgrounds'));
    }

    public function create()
    {
        abort_if(Gate::denies('academic_background_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.academicBackgrounds.create');
    }

    public function store(StoreAcademicBackgroundRequest $request)
    {
        AcademicBackground::create($request->all());

        return redirect()->route('admin.academic-backgrounds.index');
    }

    public function edit(AcademicBackground $academicBackground)
    {
        abort_if(Gate::denies('academic_background_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.academicBackgrounds.edit', compact('academicBackground'));
    }

    public function update(UpdateAcademicBackgroundRequest $request, AcademicBackground $academicBackground)
    {
        $academicBackground->update($request->all());

        return redirect()->route('admin.academic-backgrounds.index');
    }

    public function show(AcademicBackground $academicBackground)
    {
        abort_if(Gate::denies('academic_background_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.academicBackgrounds.show', compact('academicBackground'));
    }

    public function destroy(AcademicBackground $academicBackground)
    {
        abort_if(Gate::denies('academic_background_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $academicBackground->delete();

        return back();
    }

    public function massDestroy(MassDestroyAcademicBackgroundRequest $request)
    {
        $academicBackgrounds = AcademicBackground::find(request('ids'));

        foreach ($academicBackgrounds as $academicBackground) {
            $academicBackground->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
