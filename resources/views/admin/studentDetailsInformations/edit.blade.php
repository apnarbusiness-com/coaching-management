@extends('layouts.admin')
@section('title', 'Student Details — Edit')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.studentDetailsInformation.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.student-details-informations.update", [$studentDetailsInformation->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="fathers_name">{{ trans('cruds.studentDetailsInformation.fields.fathers_name') }}</label>
                <input class="form-control {{ $errors->has('fathers_name') ? 'is-invalid' : '' }}" type="text" name="fathers_name" id="fathers_name" value="{{ old('fathers_name', $studentDetailsInformation->fathers_name) }}" required>
                @if($errors->has('fathers_name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('fathers_name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.studentDetailsInformation.fields.fathers_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="mothers_name">{{ trans('cruds.studentDetailsInformation.fields.mothers_name') }}</label>
                <input class="form-control {{ $errors->has('mothers_name') ? 'is-invalid' : '' }}" type="text" name="mothers_name" id="mothers_name" value="{{ old('mothers_name', $studentDetailsInformation->mothers_name) }}">
                @if($errors->has('mothers_name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('mothers_name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.studentDetailsInformation.fields.mothers_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="fathers_nid">{{ trans('cruds.studentDetailsInformation.fields.fathers_nid') }}</label>
                <input class="form-control {{ $errors->has('fathers_nid') ? 'is-invalid' : '' }}" type="text" name="fathers_nid" id="fathers_nid" value="{{ old('fathers_nid', $studentDetailsInformation->fathers_nid) }}">
                @if($errors->has('fathers_nid'))
                    <div class="invalid-feedback">
                        {{ $errors->first('fathers_nid') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.studentDetailsInformation.fields.fathers_nid_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="mothers_nid">{{ trans('cruds.studentDetailsInformation.fields.mothers_nid') }}</label>
                <input class="form-control {{ $errors->has('mothers_nid') ? 'is-invalid' : '' }}" type="text" name="mothers_nid" id="mothers_nid" value="{{ old('mothers_nid', $studentDetailsInformation->mothers_nid) }}">
                @if($errors->has('mothers_nid'))
                    <div class="invalid-feedback">
                        {{ $errors->first('mothers_nid') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.studentDetailsInformation.fields.mothers_nid_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="guardian_name">{{ trans('cruds.studentDetailsInformation.fields.guardian_name') }}</label>
                <input class="form-control {{ $errors->has('guardian_name') ? 'is-invalid' : '' }}" type="text" name="guardian_name" id="guardian_name" value="{{ old('guardian_name', $studentDetailsInformation->guardian_name) }}" required>
                @if($errors->has('guardian_name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('guardian_name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.studentDetailsInformation.fields.guardian_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="guardian_relation">{{ trans('cruds.studentDetailsInformation.fields.guardian_relation') }}</label>
                <input class="form-control {{ $errors->has('guardian_relation') ? 'is-invalid' : '' }}" type="text" name="guardian_relation" id="guardian_relation" value="{{ old('guardian_relation', $studentDetailsInformation->guardian_relation) }}">
                @if($errors->has('guardian_relation'))
                    <div class="invalid-feedback">
                        {{ $errors->first('guardian_relation') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.studentDetailsInformation.fields.guardian_relation_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="guardian_contact_number">{{ trans('cruds.studentDetailsInformation.fields.guardian_contact_number') }}</label>
                <input class="form-control {{ $errors->has('guardian_contact_number') ? 'is-invalid' : '' }}" type="text" name="guardian_contact_number" id="guardian_contact_number" value="{{ old('guardian_contact_number', $studentDetailsInformation->guardian_contact_number) }}" required>
                @if($errors->has('guardian_contact_number'))
                    <div class="invalid-feedback">
                        {{ $errors->first('guardian_contact_number') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.studentDetailsInformation.fields.guardian_contact_number_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="student_birth_no">{{ trans('cruds.studentDetailsInformation.fields.student_birth_no') }}</label>
                <input class="form-control {{ $errors->has('student_birth_no') ? 'is-invalid' : '' }}" type="text" name="student_birth_no" id="student_birth_no" value="{{ old('student_birth_no', $studentDetailsInformation->student_birth_no) }}">
                @if($errors->has('student_birth_no'))
                    <div class="invalid-feedback">
                        {{ $errors->first('student_birth_no') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.studentDetailsInformation.fields.student_birth_no_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="student_blood_group">{{ trans('cruds.studentDetailsInformation.fields.student_blood_group') }}</label>
                <input class="form-control {{ $errors->has('student_blood_group') ? 'is-invalid' : '' }}" type="text" name="student_blood_group" id="student_blood_group" value="{{ old('student_blood_group', $studentDetailsInformation->student_blood_group) }}">
                @if($errors->has('student_blood_group'))
                    <div class="invalid-feedback">
                        {{ $errors->first('student_blood_group') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.studentDetailsInformation.fields.student_blood_group_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="address">{{ trans('cruds.studentDetailsInformation.fields.address') }}</label>
                <input class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}" type="text" name="address" id="address" value="{{ old('address', $studentDetailsInformation->address) }}">
                @if($errors->has('address'))
                    <div class="invalid-feedback">
                        {{ $errors->first('address') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.studentDetailsInformation.fields.address_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="student_id">{{ trans('cruds.studentDetailsInformation.fields.student') }}</label>
                <select class="form-control select2 {{ $errors->has('student') ? 'is-invalid' : '' }}" name="student_id" id="student_id" required>
                    @foreach($students as $id => $entry)
                        <option value="{{ $id }}" {{ (old('student_id') ? old('student_id') : $studentDetailsInformation->student->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('student'))
                    <div class="invalid-feedback">
                        {{ $errors->first('student') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.studentDetailsInformation.fields.student_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="id_card_delivery_status">{{ trans('cruds.studentDetailsInformation.fields.id_card_delivery_status') }}</label>
                <input class="form-control {{ $errors->has('id_card_delivery_status') ? 'is-invalid' : '' }}" type="text" name="id_card_delivery_status" id="id_card_delivery_status" value="{{ old('id_card_delivery_status', $studentDetailsInformation->id_card_delivery_status) }}">
                @if($errors->has('id_card_delivery_status'))
                    <div class="invalid-feedback">
                        {{ $errors->first('id_card_delivery_status') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.studentDetailsInformation.fields.id_card_delivery_status_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="reference">{{ trans('cruds.studentDetailsInformation.fields.reference') }}</label>
                <textarea class="form-control {{ $errors->has('reference') ? 'is-invalid' : '' }}" name="reference" id="reference">{{ old('reference', $studentDetailsInformation->reference) }}</textarea>
                @if($errors->has('reference'))
                    <div class="invalid-feedback">
                        {{ $errors->first('reference') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.studentDetailsInformation.fields.reference_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection