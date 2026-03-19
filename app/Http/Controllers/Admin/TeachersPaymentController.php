<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTeachersPaymentRequest;
use App\Http\Requests\StoreTeachersPaymentRequest;
use App\Http\Requests\UpdateTeachersPaymentRequest;
use App\Models\Teacher;
use App\Models\TeachersPayment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeachersPaymentController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('teachers_payment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teachersPayments = TeachersPayment::with(['teacher'])->get();

        return view('admin.teachersPayments.index', compact('teachersPayments'));
    }

    public function create()
    {
        abort_if(Gate::denies('teachers_payment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teachers = Teacher::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.teachersPayments.create', compact('teachers'));
    }

    public function store(StoreTeachersPaymentRequest $request)
    {
        $teachersPayment = TeachersPayment::create($request->all());

        return redirect()->route('admin.teachers-payments.index');
    }

    public function edit(TeachersPayment $teachersPayment)
    {
        abort_if(Gate::denies('teachers_payment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teachers = Teacher::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $teachersPayment->load('teacher');

        return view('admin.teachersPayments.edit', compact('teachers', 'teachersPayment'));
    }

    public function update(UpdateTeachersPaymentRequest $request, TeachersPayment $teachersPayment)
    {
        $teachersPayment->update($request->all());

        return redirect()->route('admin.teachers-payments.index');
    }

    public function show(TeachersPayment $teachersPayment)
    {
        abort_if(Gate::denies('teachers_payment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teachersPayment->load('teacher');

        return view('admin.teachersPayments.show', compact('teachersPayment'));
    }

    public function destroy(TeachersPayment $teachersPayment)
    {
        abort_if(Gate::denies('teachers_payment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teachersPayment->delete();

        return back();
    }

    public function massDestroy(MassDestroyTeachersPaymentRequest $request)
    {
        $teachersPayments = TeachersPayment::find(request('ids'));

        foreach ($teachersPayments as $teachersPayment) {
            $teachersPayment->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
