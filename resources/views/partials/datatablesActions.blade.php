@can($viewGate)
    <a class="btn btn-xs btn-primary" href="{{ route('admin.' . $crudRoutePart . '.show', $row->id) }}">
        {{ trans('global.view') }}
    </a>
@endcan
@can($editGate)
    <a class="btn btn-xs btn-info" href="{{ route('admin.' . $crudRoutePart . '.edit', $row->id) }}">
        {{ trans('global.edit') }}
    </a>
    @if(\Illuminate\Support\Facades\Route::has('admin.' . $crudRoutePart . '.manage'))
        <a class="btn btn-xs btn-success" href="{{ route('admin.' . $crudRoutePart . '.manage', $row->id) }}">
            Manage
        </a>
    @endif
@endcan
@can($deleteGate)
    @if(isset($studentInfo) && !empty($studentInfo))
    <form action="{{ route('admin.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST" id="delete-form-{{ $row->id }}">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    </form>
    <button type="button" class="btn btn-xs btn-danger" onclick="confirmStudentDelete({{ $row->id }}, '{{ $studentInfo }}')">
        {{ trans('global.delete') }}
    </button>
    @else
    <form action="{{ route('admin.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST" id="delete-form-{{ $row->id }}">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    </form>
    <button type="button" class="btn btn-xs btn-danger" onclick="confirmDelete({{ $row->id }})">
        {{ trans('global.delete') }}
    </button>
    @endif
@endcan

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

function confirmStudentDelete(id, studentInfo) {
    const info = studentInfo.replace(/\\/g, '');
    Swal.fire({
        title: 'Warning!',
        html: '<div style="text-align: left;"><strong>This earning is linked to a student payment!</strong><br><br>' +
              '<span style="color: #e53e3e;">Deleting will revert the due status to unpaid.</span><br><br>' +
              '<div style="background: #fef3c7; padding: 10px; border-radius: 8px; font-size: 13px;">' +
              '<strong>Details:</strong><br>' + info +
              '</div></div>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete anyway!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>