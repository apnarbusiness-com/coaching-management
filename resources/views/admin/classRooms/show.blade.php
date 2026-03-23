@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.classRoom.title_singular') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <a class="btn btn-default" href="{{ route('admin.class-rooms.index') }}">
                {{ trans('global.back_to_list') }}
            </a>
        </div>
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <th>
                        {{ trans('cruds.classRoom.fields.id') }}
                    </th>
                    <td>
                        {{ $classRoom->id }}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('cruds.classRoom.fields.name') }}
                    </th>
                    <td>
                        {{ $classRoom->name }}
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="form-group">
            <a class="btn btn-default" href="{{ route('admin.class-rooms.index') }}">
                {{ trans('global.back_to_list') }}
            </a>
        </div>
    </div>
</div>
@endsection
