@extends('layouts.admin')

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            Student Raw Import Rows
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.student-basic-infos.rawImports') }}" class="form-inline mb-3">
                <label for="source_file" class="mr-2">Source File</label>
                <select name="source_file" id="source_file" class="form-control mr-2">
                    <option value="">All files</option>
                    @foreach ($rawSourceFiles as $file)
                        <option value="{{ $file }}" {{ $sourceFile === $file ? 'selected' : '' }}>{{ $file }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary mr-2">Filter</button>
                <a href="{{ route('admin.student-basic-infos.index') }}" class="btn btn-light">Back</a>
            </form>

            <div class="mb-3">
                <span class="badge badge-dark mr-2">Total: {{ $totalRows ?? 0 }}</span>
                <span class="badge badge-success mr-2">Processed: {{ $processedRows ?? 0 }}</span>
                <span class="badge badge-warning">Pending: {{ $pendingRows ?? 0 }}</span>
            </div>

            <form id="step2-process-form" method="POST" action="{{ route('admin.student-basic-infos.processRawToStudents') }}" class="mb-3">
                @csrf
                <div class="form-row align-items-end">
                    <div class="col-md-9">
                        <label for="process_source_file">Step-2 Source File</label>
                        <select name="source_file" id="process_source_file" class="form-control" required>
                            <option value="">Select source file</option>
                            @foreach ($rawSourceFiles as $file)
                                <option value="{{ $file }}" {{ $sourceFile === $file ? 'selected' : '' }}>{{ $file }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button id="step2-process-btn" type="submit" class="btn btn-success btn-block">Run Step-2 Processing</button>
                    </div>
                </div>
            </form>
            <div id="step2-progress-wrapper" class="mb-3 d-none">
                <div class="d-flex justify-content-between mb-1">
                    <small>Processing rows...</small>
                    <small id="step2-progress-text">0%</small>
                </div>
                <div class="progress" style="height: 18px;">
                    <div id="step2-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                        role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.student-basic-infos.rawImports.reset') }}" class="mb-3"
                onsubmit="return confirm('Are you sure you want to reset raw rows?');">
                @csrf
                <div class="form-row align-items-end">
                    <div class="col-md-4">
                        <label for="reset_scope">Reset Scope</label>
                        <select name="scope" id="reset_scope" class="form-control" required>
                            <option value="source">Current Source File</option>
                            <option value="all">All Raw Rows</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="reset_source_file">Source File (for source scope)</label>
                        <select name="source_file" id="reset_source_file" class="form-control">
                            <option value="">Select source file</option>
                            @foreach ($rawSourceFiles as $file)
                                <option value="{{ $file }}" {{ $sourceFile === $file ? 'selected' : '' }}>{{ $file }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-danger btn-block">Total Clear / Reset</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Source File</th>
                            <th>Sheet</th>
                            <th>Row #</th>
                            <th>Processed</th>
                            <th>Status</th>
                            <th>Note</th>
                            <th>Row Data</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rawRows as $row)
                            <tr class="{{ $row->is_processed ? 'table-success' : '' }}">
                                <td>{{ $row->id }}</td>
                                <td>{{ $row->source_file }}</td>
                                <td>{{ $row->sheet_name }}</td>
                                <td>{{ $row->row_index }}</td>
                                <td>
                                    @if ($row->is_processed)
                                        <span class="badge badge-success px-2 py-1">Yes</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    @php $status = (string) ($row->processed_status ?? ''); @endphp
                                    @if ($status !== '' && $status !== 'created')
                                        <span class="badge badge-danger px-2 py-1">{{ $status }}</span>
                                    @elseif ($status === 'created')
                                        <span class="badge badge-success px-2 py-1">{{ $status }}</span>
                                    @else
                                        {{ $status }}
                                    @endif
                                </td>
                                <td style="max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $row->processed_note ?? '' }}
                                </td>
                                <td style="max-width: 520px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ json_encode($row->row_data, JSON_UNESCAPED_UNICODE) }}
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.student-basic-infos.rawImports.delete', $row->id) }}"
                                        onsubmit="return confirm('Delete this raw row?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">No raw rows found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $rawRows->links() }}
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        (function() {
            const form = document.getElementById('step2-process-form');
            const btn = document.getElementById('step2-process-btn');
            const wrapper = document.getElementById('step2-progress-wrapper');
            const bar = document.getElementById('step2-progress-bar');
            const text = document.getElementById('step2-progress-text');

            if (!form || !btn || !wrapper || !bar || !text) {
                return;
            }

            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.textContent = 'Processing...';
                wrapper.classList.remove('d-none');

                let percent = 0;
                const timer = setInterval(function() {
                    percent = Math.min(percent + 5, 95);
                    bar.style.width = percent + '%';
                    bar.setAttribute('aria-valuenow', String(percent));
                    bar.textContent = percent + '%';
                    text.textContent = percent + '%';
                }, 300);

                // Keep animating until server response navigates away.
                window.__step2Timer = timer;
            });
        })();
    </script>
@endsection
