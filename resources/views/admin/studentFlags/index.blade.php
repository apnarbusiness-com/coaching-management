@extends('layouts.admin')
@section('content')
<style>
    .flag-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
        border-radius: 12px;
        color: white;
        margin-bottom: 20px;
    }
    .flag-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .flag-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .color-preview {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
    }
    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
    }
    .dataTables_wrapper {
        background: white;
        border-radius: 12px;
        padding: 20px;
    }
    .btn-primary {
        background: #667eea;
        border-color: #667eea;
    }
    .btn-primary:hover {
        background: #5a6fd6;
        border-color: #5a6fd6;
    }
</style>

<div class="flag-header">
    <div class="d-flex justify-content-between align-items-center">
        <h3><i class="fa fa-flag mr-2"></i>Student Flags</h3>
        <button class="btn btn-light" onclick="openCreateModal()">
            <i class="fa fa-plus"></i> Add New Flag
        </button>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover" id="flagsTable" style="width: 100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Color</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="flagModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="flagModalTitle">Create Flag</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="flag-form">
                <div class="modal-body">
                    <input type="hidden" id="flag_id">
                    <div class="form-group">
                        <label>Flag Name *</label>
                        <input type="text" class="form-control" id="flag_name" required placeholder="e.g., Not Regular, Maximum Due">
                    </div>
                    <div class="form-group">
                        <label>Color *</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" id="flag_color" value="#ff9800" style="width: 60px; height: 40px; padding: 0; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                            <input type="text" class="form-control" id="flag_color_text" value="#ff9800" placeholder="#ff9800" style="width: 120px;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" id="flag_description" rows="2" placeholder="Optional description"></textarea>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="flag_is_active" checked> Active
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Flag</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this flag? This action cannot be undone.</p>
                <input type="hidden" id="delete_flag_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
@parent
<script>
$(function() {
    var table = $('#flagsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.student-flags.index") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'color', name: 'color', orderable: false, searchable: false },
            { data: 'description', name: 'description' },
            { data: 'is_active', name: 'is_active' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });

    $('#flag_color').on('input', function() {
        $('#flag_color_text').val($(this).val());
    });

    $('#flag_color_text').on('input', function() {
        $('#flag_color').val($(this).val());
    });
});

function openCreateModal() {
    $('#flagModalTitle').text('Create Flag');
    $('#flag_id').val('');
    $('#flag-form')[0].reset();
    $('#flag_color').val('#ff9800');
    $('#flag_color_text').val('#ff9800');
    $('#flag_is_active').prop('checked', true);
    $('#flagModal').modal('show');
}

$('#flag-form').on('submit', function(e) {
    e.preventDefault();
    var id = $('#flag_id').val();
    var url = id ? '{{ route("admin.student-flags.update", ":id") }}'.replace(':id', id) : '{{ route("admin.student-flags.store") }}';
    var method = id ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        method: method,
        data: {
            _token: '{{ csrf_token() }}',
            name: $('#flag_name').val(),
            color: $('#flag_color').val(),
            description: $('#flag_description').val(),
            is_active: $('#flag_is_active').is(':checked') ? 1 : 0
        },
        success: function(response) {
            $('#flagModal').modal('hide');
            $('#flagsTable').DataTable().ajax.reload();
            alert(response.message);
        },
        error: function(xhr) {
            alert(xhr.responseJSON?.message || 'An error occurred');
        }
    });
});

$(document).on('click', '.edit-btn', function() {
    var id = $(this).data('id');
    $.get('{{ route("admin.student-flags.edit", ":id") }}'.replace(':id', id), function(response) {
        $('#flagModalTitle').text('Edit Flag');
        $('#flag_id').val(response.id);
        $('#flag_name').val(response.name);
        $('#flag_color').val(response.color);
        $('#flag_color_text').val(response.color);
        $('#flag_description').val(response.description);
        $('#flag_is_active').prop('checked', response.is_active);
        $('#flagModal').modal('show');
    });
});

$(document).on('click', '.delete-btn', function() {
    $('#delete_flag_id').val($(this).data('id'));
    $('#deleteModal').modal('show');
});

function confirmDelete() {
    var id = $('#delete_flag_id').val();
    $.ajax({
        url: '{{ route("admin.student-flags.destroy", ":id") }}'.replace(':id', id),
        method: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            $('#deleteModal').modal('hide');
            $('#flagsTable').DataTable().ajax.reload();
            alert(response.message);
        },
        error: function(xhr) {
            alert(xhr.responseJSON?.message || 'An error occurred');
        }
    });
}
</script>
@endsection