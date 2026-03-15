@extends('layouts.admin')
@section('content')

    <main class="flex-1 overflow-y-auto">

            <nav class="hidden sm:block">
                <ol class="flex items-center gap-2 text-sm">
                    <li><a class="text-slate-500 hover:text-primary dark:text-slate-400" href="#">Finance</a>
                    </li>
                    <li><span class="text-slate-300 dark:text-slate-600">/</span></li>
                    <li><a class="text-slate-500 hover:text-primary dark:text-slate-400" href="{{ route('admin.expenses.index') }}">Expenses</a>
                    </li>
                    <li><span class="text-slate-300 dark:text-slate-600">/</span></li>
                    <li><span class="font-medium text-primary">{{ trans('global.edit') }}</span></li>
                </ol>
            </nav>
            <div class="mx-auto max-w-5xl px-6 py-8">
                <div class="mb-8 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ trans('global.edit') }} {{ trans('cruds.expense.title_singular') }}</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Update the details of this expense record.</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.expenses.index') }}"
                            class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white dark:bg-slate-700 px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:border-slate-700 dark:bg-surface-dark dark:text-slate-200 dark:hover:bg-slate-800 transition-all">
                            <span class="material-icons-round mr-2 text-base">arrow_back</span>
                            Back to List
                        </a>
                    </div>
                </div>
                <form action="{{ route('admin.expenses.update', [$expense->id]) }}" method="POST" enctype="multipart/form-data"
                    class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    @method('PUT')
                    @csrf
                    <!-- Main Form Section -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Category & Teacher Card -->
                        <div
                            class="rounded-xl border border-slate-200 bg-surface-light p-6 shadow-soft dark:border-slate-800 dark:bg-surface-dark">
                            <h2 class="mb-4 text-lg font-semibold text-slate-900 dark:text-white">Expense Details</h2>
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div class="col-span-2">
                                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                                        for="title">Expense Title <span class="text-red-500">*</span></label>
                                    <input
                                        class="block w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-primary focus:bg-white focus:ring-primary dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:placeholder-slate-400 sm:text-sm {{ $errors->has('title') ? 'border-red-500 ring-red-500' : '' }}"
                                        id="title" name="title" placeholder="e.g. Monthly Electricity Bill" type="text"
                                        value="{{ old('title', $expense->title) }}" required />
                                    @if($errors->has('title'))
                                        <p class="mt-1 text-xs text-red-500">{{ $errors->first('title') }}</p>
                                    @endif
                                </div>
                                <div class="col-span-2">
                                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                                        for="category">Category <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <select
                                            class="block w-full appearance-none rounded-lg border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-primary focus:bg-white focus:ring-primary dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:placeholder-slate-400 sm:text-sm {{ $errors->has('expense_category_id') ? 'border-red-500 ring-red-500' : '' }}"
                                            id="expense_category_id" name="expense_category_id" required>
                                            @foreach ($expense_categories as $id => $entry)
                                                <option value="{{ $id }}"
                                                    data-teacher-connected="{{ ($expense_category_flags[$id] ?? false) ? 1 : 0 }}"
                                                    {{ (old('expense_category_id') ? old('expense_category_id') : $expense->expense_category->id ?? '') == $id ? 'selected' : '' }}>
                                                    {{ $entry }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('expense_category_id'))
                                            <p class="mt-1 text-xs text-red-500">{{ $errors->first('expense_category_id') }}</p>
                                        @endif
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                                            <span class="material-icons-round">expand_more</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Conditional Field: Teacher Selection -->
                                <div id="teacher-section"
                                    class="col-span-2 hidden animate-fade-in-down rounded-lg border border-primary/20 bg-primary/5 p-4 dark:border-primary/30 dark:bg-primary/10">
                                    <label class="mb-2 flex items-center gap-2 text-sm font-medium text-primary"
                                        for="teacher_search">
                                        <span class="material-icons-round text-base">person</span>
                                        Select Teacher <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input
                                            class="block w-full rounded-lg border-primary/30 bg-white px-4 py-2.5 pl-10 text-slate-900 placeholder-slate-400 focus:border-primary focus:ring-primary dark:bg-slate-900 dark:text-white dark:placeholder-slate-500 sm:text-sm"
                                            id="teacher_search" placeholder="Search by name or ID..." type="text"
                                            autocomplete="off" value="{{ $expense->teacher->name ?? '' }}" />
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-3 text-primary/60">
                                            <span class="material-icons-round text-lg">search</span>
                                        </div>
                                        <input type="hidden" name="teacher_id" id="teacher_id_hidden"
                                            value="{{ old('teacher_id', $expense->teacher_id) }}">
                                        @if($errors->has('teacher_id'))
                                            <p class="mt-1 text-xs text-red-500">{{ $errors->first('teacher_id') }}</p>
                                        @endif

                                        <!-- Search Results -->
                                        <div id="teacher-results"
                                            class="absolute z-20 mt-1 hidden w-full overflow-hidden rounded-lg border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900">
                                            <ul class="max-h-48 overflow-y-auto py-1">
                                                <!-- Injected via JS -->
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @php $count = 0; @endphp
                                        @foreach($teachers as $teacher_id => $name)
                                            @if($teacher_id && $count < 5)
                                                <span onclick="setTeacher('{{ $teacher_id }}', '{{ $name }}')"
                                                    class="inline-flex cursor-pointer items-center rounded-full bg-white px-2.5 py-0.5 text-xs font-medium text-slate-600 shadow-sm ring-1 ring-inset ring-slate-200 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700 transition-colors">
                                                    {{ $name }}
                                                </span>
                                                @php $count++; @endphp
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                                        for="amount">Amount <span class="text-red-500">*</span></label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-slate-500 sm:text-sm symbol-of-tk">৳</span>
                                        </div>
                                        <input
                                            class="block w-full rounded-lg border-slate-200 bg-slate-50 py-3 pl-7 pr-12 text-slate-900 placeholder:text-slate-400 focus:border-primary focus:bg-white focus:ring-primary dark:border-slate-700 dark:bg-slate-900 dark:text-white sm:text-sm {{ $errors->has('amount') ? 'border-red-500 ring-red-500' : '' }}"
                                            id="amount" name="amount" placeholder="0.00" type="number" step="0.01"
                                            value="{{ old('amount', $expense->amount) }}" required />
                                        @if($errors->has('amount'))
                                            <p class="mt-1 text-xs text-red-500">{{ $errors->first('amount') }}</p>
                                        @endif
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                            <span class="text-slate-500 sm:text-sm">BDT</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                                        for="expense_date">Date <span class="text-red-500">*</span></label>
                                    <input
                                        class="block w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-primary focus:bg-white focus:ring-primary dark:border-slate-700 dark:bg-slate-900 dark:text-white sm:text-sm {{ $errors->has('expense_date') ? 'border-red-500 ring-red-500' : '' }}"
                                        id="expense_date" name="expense_date" type="datetime-local"
                                        value="{{ old('expense_date', $expense->expense_date ? \Carbon\Carbon::parse($expense->getRawOriginal('expense_date'))->format('Y-m-d\TH:i') : '') }}" required />
                                    @if($errors->has('expense_date'))
                                        <p class="mt-1 text-xs text-red-500">{{ $errors->first('expense_date') }}</p>
                                    @endif
                                </div>
                                <div class="col-span-2">
                                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                                        for="details">Description / Note</label>
                                    <textarea
                                        class="block w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-primary focus:bg-white focus:ring-primary dark:border-slate-700 dark:bg-slate-900 dark:text-white sm:text-sm ckeditor"
                                        id="details" name="details" placeholder="e.g. Monthly salary payment for October"
                                        rows="3">{{ old('details', $expense->details) }}</textarea>
                                </div>
                            </div>
                        </div>
                        <!-- Payment Method Card -->
                        <div
                            class="rounded-xl border border-slate-200 bg-surface-light p-6 shadow-soft dark:border-slate-800 dark:bg-surface-dark">
                            <h2 class="mb-4 text-lg font-semibold text-slate-900 dark:text-white">Payment Information
                            </h2>
                            <div class="space-y-6">
                                <div>
                                    <label class="mb-3 block text-sm font-medium text-slate-700 dark:text-slate-300">Payment
                                        Method</label>
                                    <div class="grid grid-cols-3 gap-3">
                                        <label class="cursor-pointer">
                                            <input class="peer sr-only" name="payment_method" type="radio" value="Cash" {{ old('payment_method', $expense->payment_method) == 'Cash' ? 'checked' : '' }} />
                                            <div
                                                class="flex flex-col items-center justify-center rounded-lg border border-slate-200 p-3 hover:bg-slate-50 peer-checked:border-primary peer-checked:bg-primary/5 peer-checked:text-primary dark:border-slate-700 dark:hover:bg-slate-800 dark:peer-checked:bg-primary/20 transition-all">
                                                <span class="material-icons-round mb-1 text-2xl">payments</span>
                                                <span class="text-xs font-medium">Cash</span>
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input class="peer sr-only" name="payment_method" type="radio" value="Bank Transfer"
                                                {{ old('payment_method', $expense->payment_method) == 'Bank Transfer' ? 'checked' : '' }} />
                                            <div
                                                class="flex flex-col items-center justify-center rounded-lg border border-slate-200 p-3 hover:bg-slate-50 peer-checked:border-primary peer-checked:bg-primary/5 peer-checked:text-primary dark:border-slate-700 dark:hover:bg-slate-800 dark:peer-checked:bg-primary/20 transition-all">
                                                <span class="material-icons-round mb-1 text-2xl">account_balance</span>
                                                <span class="text-xs font-medium">Bank Transfer</span>
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input class="peer sr-only" name="payment_method" type="radio" value="Check" {{ old('payment_method', $expense->payment_method) == 'Check' ? 'checked' : '' }} />
                                            <div
                                                class="flex flex-col items-center justify-center rounded-lg border border-slate-200 p-3 hover:bg-slate-50 peer-checked:border-primary peer-checked:bg-primary/5 peer-checked:text-primary dark:border-slate-700 dark:hover:bg-slate-800 dark:peer-checked:bg-primary/20 transition-all">
                                                <span class="material-icons-round mb-1 text-2xl">confirmation_number</span>
                                                <span class="text-xs font-medium">Check</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                                            for="expense_reference">Transaction Reference ID</label>
                                        <input
                                            class="block w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-primary focus:bg-white focus:ring-primary dark:border-slate-700 dark:bg-slate-900 dark:text-white sm:text-sm"
                                            id="expense_reference" name="expense_reference" placeholder="e.g. TRX-987456123"
                                            type="text" value="{{ old('expense_reference', $expense->expense_reference) }}" />
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300" for="expense_month">Month</label>
                                            <input class="block w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-primary focus:bg-white focus:ring-primary dark:border-slate-700 dark:bg-slate-900 dark:text-white sm:text-sm"
                                                id="expense_month" name="expense_month" type="number" step="1" value="{{ old('expense_month', $expense->expense_month) }}" />
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300" for="expense_year">Year</label>
                                            <input class="block w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-primary focus:bg-white focus:ring-primary dark:border-slate-700 dark:bg-slate-900 dark:text-white sm:text-sm"
                                                id="expense_year" name="expense_year" type="number" step="1" value="{{ old('expense_year', $expense->expense_year) }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Side Panel: Proof & Actions -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- File Upload -->
                        <div
                            class="rounded-xl border border-slate-200 bg-surface-light p-6 shadow-soft dark:border-slate-800 dark:bg-surface-dark">
                            <h2 class="mb-4 text-lg font-semibold text-slate-900 dark:text-white">Proof of Payment</h2>
                            <div id="payment_proof-dropzone"
                                class="relative flex w-full flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 py-4 transition-colors hover:border-primary/50 hover:bg-primary/5 dark:border-slate-600 dark:bg-slate-800/50 dark:hover:border-primary/50 dark:hover:bg-primary/10 cursor-pointer">
                                <div class="flex flex-col items-center justify-center text-center pointer-events-none">
                                    <div
                                        class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-primary">
                                        <span class="material-icons-round">cloud_upload</span>
                                    </div>
                                    <p class="mb-1 text-sm font-semibold text-slate-700 dark:text-slate-200">Click to upload</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">or drag and drop receipt here</p>
                                </div>
                            </div>
                            <div id="payment_proof-container" class="mt-4 space-y-2">
                                <!-- Existing Files -->
                                @foreach($expense->payment_proof as $media)
                                    @php
                                        $fileId = 'existing_' . $loop->index;
                                        $isPdf = strtolower(pathinfo($media->file_name, PATHINFO_EXTENSION)) === 'pdf';
                                    @endphp
                                    <div id="file-{{ $fileId }}" class="flex items-center justify-between rounded-lg border border-slate-200 bg-white p-2 dark:border-slate-700 dark:bg-slate-900 animate-fade-in shadow-sm">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded bg-primary/10 text-primary overflow-hidden">
                                                @if(!$isPdf)
                                                    <img src="{{ $media->getUrl('preview') ?? $media->getUrl() }}" class="h-full w-full object-cover">
                                                @else
                                                    <span class="material-icons-round text-sm">picture_as_pdf</span>
                                                @endif
                                            </div>
                                            <div class="flex flex-col min-w-0">
                                                <span class="text-xs font-medium text-slate-900 dark:text-white line-clamp-1" title="{{ $media->file_name }}">{{ $media->file_name }}</span>
                                                <span class="text-[10px] text-slate-500">{{ number_format($media->size / 1024, 2) }} KB</span>
                                            </div>
                                        </div>
                                        <button type="button" class="text-slate-400 hover:text-red-500 transition-colors" onclick="removeFile('{{ $media->file_name }}', 'file-{{ $fileId }}')">
                                            <span class="material-icons-round text-lg">close</span>
                                        </button>
                                        <input type="hidden" name="payment_proof[]" value="{{ $media->file_name }}">
                                    </div>
                                @endforeach
                            </div>
                            <!-- Payment Proof Details -->
                            <div class="mt-6">
                                <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                                    for="payment_proof_details">Payment Proof Details</label>
                                <textarea
                                    class="block w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-primary focus:bg-white focus:ring-primary dark:border-slate-700 dark:bg-slate-900 dark:text-white sm:text-sm ckeditor"
                                    id="payment_proof_details" name="payment_proof_details"
                                    placeholder="Any additional notes about the proof/receipt..."
                                    rows="2">{{ old('payment_proof_details', $expense->payment_proof_details) }}</textarea>
                            </div>
                        </div>

                        <!-- Summary & Actions -->
                        <div
                            class="rounded-xl border border-slate-200 bg-surface-light p-6 shadow-soft dark:border-slate-800 dark:bg-surface-dark">
                            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Action</h3>
                            <div class="space-y-3">
                                <button
                                    class="flex w-full items-center justify-center rounded-lg bg-primary py-3 text-sm font-semibold text-white shadow-md shadow-primary/20 transition-all hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-slate-900"
                                    type="submit">
                                    <span class="material-icons-round mr-2 text-lg">save</span>
                                    Update Record
                                </button>
                                <a href="{{ route('admin.expenses.index') }}"
                                    class="flex w-full items-center justify-center rounded-lg border border-slate-200 bg-white py-3 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-200 dark:border-slate-700 dark:bg-transparent dark:text-slate-300 dark:hover:bg-slate-800"
                                    type="button">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            function SimpleUploadAdapter(editor) {
                editor.plugins.get('FileRepository').createUploadAdapter = function (loader) {
                    return {
                        upload: function () {
                            return loader.file
                                .then(function (file) {
                                    return new Promise(function (resolve, reject) {
                                        // Init request
                                        var xhr = new XMLHttpRequest();
                                        xhr.open('POST',
                                            '{{ route('admin.expenses.storeCKEditorImages') }}',
                                            true);
                                        xhr.setRequestHeader('x-csrf-token', window._token);
                                        xhr.setRequestHeader('Accept', 'application/json');
                                        xhr.responseType = 'json';

                                        // Init listeners
                                        var genericErrorText =
                                            `Couldn't upload file: ${file.name}.`;
                                        xhr.addEventListener('error', function () {
                                            reject(genericErrorText)
                                        });
                                        xhr.addEventListener('abort', function () {
                                            reject()
                                        });
                                        xhr.addEventListener('load', function () {
                                            var response = xhr.response;

                                            if (!response || xhr.status !== 201) {
                                                return reject(response && response
                                                    .message ?
                                                    `${genericErrorText}\n${xhr.status} ${response.message}` :
                                                    `${genericErrorText}\n ${xhr.status} ${xhr.statusText}`
                                                );
                                            }

                                            $('form').append(
                                                '<input type="hidden" name="ck-media[]" value="' +
                                                response.id + '">');

                                            resolve({
                                                default: response.url
                                            });
                                        });

                                        if (xhr.upload) {
                                            xhr.upload.addEventListener('progress', function (
                                                e) {
                                                if (e.lengthComputable) {
                                                    loader.uploadTotal = e.total;
                                                    loader.uploaded = e.loaded;
                                                }
                                            });
                                        }

                                        // Send request
                                        var data = new FormData();
                                        data.append('upload', file);
                                        data.append('crud_id', '{{ $expense->id ?? 0 }}');
                                        xhr.send(data);
                                    });
                                })
                        }
                    };
                }
            }

            var allEditors = document.querySelectorAll('.ckeditor');
            for (var i = 0; i < allEditors.length; ++i) {
                ClassicEditor.create(
                    allEditors[i], {
                    extraPlugins: [SimpleUploadAdapter]
                }
                );
            }
        });
    </script>

    <script>
        $(document).ready(function () {
            // Teacher section visibility logic
            function toggleTeacherSection() {
                const selectedOption = $('#expense_category_id').find('option:selected');
                const teacherSection = document.getElementById('teacher-section');
                const isTeacherConnected = Number(selectedOption.data('teacher-connected')) === 1;

                if (isTeacherConnected) {
                    teacherSection.classList.remove('hidden');
                } else {
                    teacherSection.classList.add('hidden');
                }
            }

            $('#expense_category_id').on('change', toggleTeacherSection);
            toggleTeacherSection(); // Initial check

            // Teacher Search Logic
            const teachers = @json($teachers);
            const teacherSearchInput = document.getElementById('teacher_search');
            const teacherResults = document.getElementById('teacher-results');
            const teacherResultsList = teacherResults.querySelector('ul');
            const teacherIdHidden = document.getElementById('teacher_id_hidden');

            window.setTeacher = function (id, name) {
                teacherSearchInput.value = name;
                teacherIdHidden.value = id;
                teacherResults.classList.add('hidden');
                teacherSearchInput.classList.add('border-primary');
            }

            teacherSearchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase();
                teacherResultsList.innerHTML = '';

                if (query.length < 1) {
                    teacherResults.classList.add('hidden');
                    return;
                }

                let matches = 0;
                for (const [id, name] of Object.entries(teachers)) {
                    if (id && name.toLowerCase().includes(query)) {
                        const li = document.createElement('li');
                        li.className = 'cursor-pointer px-4 py-2 hover:bg-primary/10 dark:hover:bg-primary/20 text-sm text-slate-700 dark:text-slate-200 transition-colors flex items-center gap-2';
                        li.innerHTML = `<span class="material-icons-round text-primary text-sm">person</span> ${name}`;
                        li.onclick = () => setTeacher(id, name);
                        teacherResultsList.appendChild(li);
                        matches++;
                    }
                }

                if (matches > 0) {
                    teacherResults.classList.remove('hidden');
                } else {
                    teacherResults.classList.add('hidden');
                }
            });

            document.addEventListener('click', function (e) {
                if (!teacherSearchInput.contains(e.target) && !teacherResults.contains(e.target)) {
                    teacherResults.classList.add('hidden');
                }
            });

            // Custom Dropzone Implementation
            const proofContainer = document.getElementById('payment_proof-container');
            const uploadedFiles = {};

            const myDropzone = new Dropzone("#payment_proof-dropzone", {
                url: '{{ route('admin.expenses.storeMedia') }}',
                maxFilesize: 5,
                acceptedFiles: '.jpeg,.jpg,.png,.pdf',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                previewsContainer: false,
                clickable: true,
                init: function () {
                    // New Dropzone initialization (existing files handled by Blade)

                    this.on("success", function (file, response) {
                        const fileId = Math.random().toString(36).substr(2, 9);
                        uploadedFiles[file.name] = response.name;

                        const isPdf = file.type.includes('pdf');
                        const previewHtml = `
                            <div id="file-${fileId}" class="flex items-center justify-between rounded-lg border border-slate-200 bg-white p-2 dark:border-slate-700 dark:bg-slate-900 animate-fade-in shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded bg-primary/10 text-primary">
                                        <span class="material-icons-round text-sm">${isPdf ? 'picture_as_pdf' : 'image'}</span>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="text-xs font-medium text-slate-900 dark:text-white line-clamp-1" title="${file.name}">${file.name}</span>
                                        <span class="text-[10px] text-slate-500">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
                                    </div>
                                </div>
                                <button type="button" class="text-slate-400 hover:text-red-500 transition-colors" onclick="removeFile('${file.name}', 'file-${fileId}')">
                                    <span class="material-icons-round text-lg">close</span>
                                </button>
                                <input type="hidden" name="payment_proof[]" value="${response.name}">
                            </div>
                        `;
                        $(proofContainer).append(previewHtml);
                    });

                    this.on("error", function (file, message) {
                        console.error(message);
                        alert("Error uploading file: " + (typeof message === 'string' ? message : message.errors.file));
                    });
                }
            });

            window.removeFile = function (fileName, elementId) {
                $(`#${elementId}`).remove();
            };
        });
    </script>
@endsection
