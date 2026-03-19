<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyEarningCategoryRequest;
use App\Http\Requests\StoreEarningCategoryRequest;
use App\Http\Requests\UpdateEarningCategoryRequest;
use App\Models\EarningCategory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EarningCategoriesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('earning_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $earningCategories = EarningCategory::all();

        return view('admin.earningCategories.index', compact('earningCategories'));
    }

    public function create()
    {
        abort_if(Gate::denies('earning_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.earningCategories.create');
    }

    public function store(StoreEarningCategoryRequest $request)
    {
        $earningCategory = EarningCategory::create($request->all());

        return redirect()->route('admin.earning-categories.index');
    }

    public function edit(EarningCategory $earningCategory)
    {
        abort_if(Gate::denies('earning_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.earningCategories.edit', compact('earningCategory'));
    }

    public function update(UpdateEarningCategoryRequest $request, EarningCategory $earningCategory)
    {
        $earningCategory->update($request->all());

        return redirect()->route('admin.earning-categories.index');
    }

    public function show(EarningCategory $earningCategory)
    {
        abort_if(Gate::denies('earning_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $earningCategory->load('earningCategoryEarnings');

        return view('admin.earningCategories.show', compact('earningCategory'));
    }

    public function destroy(EarningCategory $earningCategory)
    {
        abort_if(Gate::denies('earning_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $earningCategory->delete();

        return back();
    }

    public function massDestroy(MassDestroyEarningCategoryRequest $request)
    {
        $earningCategories = EarningCategory::find(request('ids'));

        foreach ($earningCategories as $earningCategory) {
            $earningCategory->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
