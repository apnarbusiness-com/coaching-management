@extends('layouts.admin')

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="card panel-default">
                <div class="card-header">
                    Student Import Preview
                </div>

                <div class="card-body table-responsive">
                    <form class="form-horizontal" method="POST"
                        action="{{ route('admin.student-basic-infos.processStudentImport') }}">
                        @csrf
                        <input type="hidden" name="filename" value="{{ $filename }}" />
                        <input type="hidden" name="redirect" value="{{ $redirect }}" />
                        <input type="hidden" name="headerIndex" value="{{ $headerIndex }}" />

                        <table class="table table-bordered">
                            @if (isset($headers))
                                <tr>
                                    @foreach ($headers as $field)
                                        <th>{{ $field }}</th>
                                    @endforeach
                                </tr>
                            @endif
                            @if ($lines)
                                @foreach ($lines as $line)
                                    <tr>
                                        @foreach ($line as $field)
                                            <td>{{ $field }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endif
                        </table>

                        <button type="submit" class="btn btn-primary">
                            Import Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
