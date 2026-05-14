@extends('layouts.admin')
@section('title', 'Student Details — Details')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.studentDetailsInformation.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.student-details-informations.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.studentDetailsInformation.fields.id') }}
                        </th>
                        <td>
                            {{ $studentDetailsInformation->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.studentDetailsInformation.fields.fathers_name') }}
                        </th>
                        <td>
                            {{ $studentDetailsInformation->fathers_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.studentDetailsInformation.fields.mothers_name') }}
                        </th>
                        <td>
                            {{ $studentDetailsInformation->mothers_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.studentDetailsInformation.fields.fathers_nid') }}
                        </th>
                        <td>
                            {{ $studentDetailsInformation->fathers_nid }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.studentDetailsInformation.fields.mothers_nid') }}
                        </th>
                        <td>
                            {{ $studentDetailsInformation->mothers_nid }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.studentDetailsInformation.fields.guardian_name') }}
                        </th>
                        <td>
                            {{ $studentDetailsInformation->guardian_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.studentDetailsInformation.fields.guardian_relation') }}
                        </th>
                        <td>
                            {{ $studentDetailsInformation->guardian_relation }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.studentDetailsInformation.fields.guardian_contact_number') }}
                        </th>
                        <td>
                            {{ $studentDetailsInformation->guardian_contact_number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.studentDetailsInformation.fields.student_birth_no') }}
                        </th>
                        <td>
                            {{ $studentDetailsInformation->student_birth_no }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.studentDetailsInformation.fields.student_blood_group') }}
                        </th>
                        <td>
                            {{ $studentDetailsInformation->student_blood_group }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.studentDetailsInformation.fields.address') }}
                        </th>
                        <td>
                            {{ $studentDetailsInformation->address }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.studentDetailsInformation.fields.student') }}
                        </th>
                        <td>
                            {{ $studentDetailsInformation->student->id_no ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.studentDetailsInformation.fields.id_card_delivery_status') }}
                        </th>
                        <td>
                            {{ $studentDetailsInformation->id_card_delivery_status }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.studentDetailsInformation.fields.reference') }}
                        </th>
                        <td>
                            {{ $studentDetailsInformation->reference }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.student-details-informations.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection