@extends('layouts.admin')
@section('content')

    <!-- Page Scroll Container -->
    <div
        class="flex-1 overflow-y-auto p-6 lg:px-10 lg:py-8 bg-background-light dark:bg-background-dark transition-colors duration-200">
        <div class="max-w-[1024px] mx-auto flex flex-col gap-8">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-sm">
                <a class="text-text-secondary dark:text-gray-400 hover:text-primary transition-colors"
                    href="#">Dashboard</a>
                <span class="text-text-secondary dark:text-gray-500">/</span>
                <a class="text-text-secondary dark:text-gray-400 hover:text-primary transition-colors" href="#">Teachers</a>
                <span class="text-text-secondary dark:text-gray-500">/</span>
                <span class="text-text-main dark:text-white font-medium">Register</span>
            </nav>
            <!-- Page Heading -->
            <div class="flex flex-col gap-2">
                <h1 class="text-3xl font-bold text-text-main dark:text-white tracking-tight">Register New Teacher
                </h1>
                <p class="text-text-secondary dark:text-gray-400 max-w-2xl">Enter the details below to add a new
                    faculty member to the system. Ensure all mandatory fields marked with * are filled correctly.
                </p>
            </div>
            <!-- Main Form Card -->
            <form action="{{ route('admin.teachers.store') }}" method="POST" enctype="multipart/form-data"
                class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark overflow-hidden transition-colors duration-200">
                @csrf
                <!-- Section 1: Personal Details -->
                <div class="p-6 lg:p-8 border-b border-border-light dark:border-border-dark">
                    <h2 class="text-xl font-bold text-text-main dark:text-white mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">person</span>
                        Personal Information
                    </h2>
                    <div class="flex flex-col lg:flex-row gap-8">
                        <!-- Photo Upload -->
                        <div class="flex-shrink-0 flex flex-col items-center gap-3">
                            <div id="drop-zone"
                                class="relative group cursor-pointer size-32 rounded-full bg-background-light dark:bg-background-dark border-2 border-dashed border-border-light dark:border-border-dark flex items-center justify-center overflow-hidden hover:border-primary transition-colors">
                                <div id="photo-preview" class="absolute inset-0 w-full h-full bg-cover bg-center"></div>
                                <span id="photo-placeholder-icon"
                                    class="material-symbols-outlined text-4xl text-text-secondary group-hover:text-primary transition-colors">add_a_photo</span>
                                <div
                                    class="absolute inset-0 bg-black/40 hidden group-hover:flex items-center justify-center text-white text-xs font-medium">
                                    Change</div>
                                <input class="sr-only" id="file-upload" name="file-upload" type="file" accept="image/*" />
                            </div>
                            <span class="text-sm font-medium text-text-secondary dark:text-gray-400">Profile Photo</span>
                            @if ($errors->has('profile_img'))
                                <p class="text-xs text-red-500 mt-1">{{ $errors->first('profile_img') }}</p>
                            @endif
                        </div>
                        <!-- Inputs -->
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-300 mb-1.5">Full
                                    Name <span class="text-red-500">*</span></label>
                                <input
                                    class="w-full rounded-lg border-none bg-background-light dark:bg-background-dark text-text-main dark:text-white py-2.5 px-4 placeholder-text-secondary/50 focus:ring-2 focus:ring-primary {{ $errors->has('name') ? 'ring-2 ring-red-500' : '' }}"
                                    placeholder="e.g. John Doe" type="text" name="name" value="{{ old('name', '') }}"
                                    required />
                                @if($errors->has('name'))
                                    <p class="mt-1 text-xs text-red-500">{{ $errors->first('name') }}</p>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-text-main dark:text-gray-300 mb-1.5">Email
                                    Address <span class="text-red-500">*</span></label>
                                <input
                                    class="w-full rounded-lg border-none bg-background-light dark:bg-background-dark text-text-main dark:text-white py-2.5 px-4 placeholder-text-secondary/50 focus:ring-2 focus:ring-primary {{ $errors->has('email') ? 'ring-2 ring-red-500' : '' }}"
                                    placeholder="john.doe@school.edu" type="email" name="email"
                                    value="{{ old('email', '') }}" required />
                                @if($errors->has('email'))
                                    <p class="mt-1 text-xs text-red-500">{{ $errors->first('email') }}</p>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-text-main dark:text-gray-300 mb-1.5">Phone
                                    Number <span class="text-red-500">*</span></label>
                                <input
                                    class="w-full rounded-lg border-none bg-background-light dark:bg-background-dark text-text-main dark:text-white py-2.5 px-4 placeholder-text-secondary/50 focus:ring-2 focus:ring-primary {{ $errors->has('phone') ? 'ring-2 ring-red-500' : '' }}"
                                    placeholder="+1 (555) 000-0000" type="tel" name="phone" value="{{ old('phone', '') }}"
                                    required />
                                @if($errors->has('phone'))
                                    <p class="mt-1 text-xs text-red-500">{{ $errors->first('phone') }}</p>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-text-main dark:text-gray-300 mb-1.5">Default
                                    Password</label>
                                <input
                                    class="w-full rounded-lg border-none bg-background-light dark:bg-background-dark text-text-main dark:text-white py-2.5 px-4 placeholder-text-secondary/50 focus:ring-2 focus:ring-primary"
                                    placeholder="Leave blank to use email" type="password" name="password" />
                                <p class="mt-1 text-[10px] text-text-secondary">Used for the initial login of the new
                                    teacher account.</p>
                            </div>
                            <div class="col-span-1 md:col-span-2">
                                <label
                                    class="block text-sm font-medium text-text-main dark:text-gray-300 mb-1.5">Residential
                                    Address</label>
                                <textarea name="address"
                                    class="w-full rounded-lg border-none bg-background-light dark:bg-background-dark text-text-main dark:text-white py-2.5 px-4 placeholder-text-secondary/50 focus:ring-2 focus:ring-primary resize-none {{ $errors->has('address') ? 'ring-2 ring-red-500' : '' }}"
                                    placeholder="Street address, city, state, zip code"
                                    rows="2">{{ old('address', '') }}</textarea>
                                @if($errors->has('address'))
                                    <p class="mt-1 text-xs text-red-500">{{ $errors->first('address') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Section 2: Employment Information -->
                <div
                    class="p-6 lg:p-8 border-b border-border-light dark:border-border-dark bg-background-light/30 dark:bg-background-dark/30">
                    <h2 class="text-xl font-bold text-text-main dark:text-white mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">badge</span>
                        Employment Details
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-text-main dark:text-gray-300 mb-1.5">Employee
                                Code <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input
                                    class="w-full rounded-lg border-none bg-card-light dark:bg-card-dark text-text-main dark:text-white py-2.5 px-4 focus:ring-2 focus:ring-primary font-mono text-sm {{ $errors->has('emloyee_code') ? 'ring-2 ring-red-500' : '' }}"
                                    readonly="" type="text" name="emloyee_code"
                                    value="TCH-{{ date('Y') }}-{{ sprintf('%03d', \App\Models\Teacher::count() + 1) }}" />
                                <span
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-primary font-medium cursor-pointer">Auto</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-main dark:text-gray-300 mb-1.5">Joining
                                Date <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input
                                    class="w-full rounded-lg border-none bg-card-light dark:bg-card-dark text-text-main dark:text-white py-2.5 px-4 focus:ring-2 focus:ring-primary [color-scheme:light] dark:[color-scheme:dark] {{ $errors->has('joining_date') ? 'ring-2 ring-red-500' : '' }}"
                                    type="text" name="joining_date" id="joining_date"
                                    value="{{ old('joining_date') ?? date(config('panel.date_format') . ' ' . config('panel.time_format')) }}" />
                                @if($errors->has('joining_date'))
                                    <p class="mt-1 text-xs text-red-500">{{ $errors->first('joining_date') }}</p>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-main dark:text-gray-300 mb-1.5">Gender <span
                                    class="text-red-500">*</span></label>
                            <select name="gender" id="gender"
                                class="w-full rounded-lg border-none bg-card-light dark:bg-card-dark text-text-main dark:text-white py-2.5 px-3 focus:ring-2 focus:ring-primary {{ $errors->has('gender') ? 'ring-2 ring-red-500' : '' }}">
                                @foreach(App\Models\Teacher::GENDER_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('gender') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-main dark:text-gray-300 mb-1.5">Salary
                                Type</label>
                            <select name="salary_type" id="salary_type"
                                class="w-full rounded-lg border-none bg-card-light dark:bg-card-dark text-text-main dark:text-white py-2.5 px-3 focus:ring-2 focus:ring-primary {{ $errors->has('salary_type') ? 'ring-2 ring-red-500' : '' }}">
                                @foreach(App\Models\Teacher::SALARY_TYPE_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('salary_type', 'variable') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @if($errors->has('salary_type'))
                                <p class="mt-1 text-xs text-red-500">{{ $errors->first('salary_type') }}</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-main dark:text-gray-300 mb-1.5">Base
                                Salary / Rate</label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-text-secondary symbol-of-tk">৳</span>
                                <input name="salary_amount"
                                    class="w-full rounded-lg border-none bg-card-light dark:bg-card-dark text-text-main dark:text-white py-2.5 pl-8 pr-4 placeholder-text-secondary/50 focus:ring-2 focus:ring-primary {{ $errors->has('salary_amount') ? 'ring-2 ring-red-500' : '' }}"
                                    placeholder="0.00" type="number" step="0.01" value="{{ old('salary_amount', '') }}" />
                            </div>
                            @if($errors->has('salary_amount'))
                                <p class="mt-1 text-xs text-red-500">{{ $errors->first('salary_amount') }}</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-main dark:text-gray-300 mb-1.5">Salary Amount Type</label>
                            <select name="salary_amount_type" id="salary_amount_type"
                                class="w-full rounded-lg border-none bg-card-light dark:bg-card-dark text-text-main dark:text-white py-2.5 px-3 focus:ring-2 focus:ring-primary {{ $errors->has('salary_amount_type') ? 'ring-2 ring-red-500' : '' }}">
                                @foreach(App\Models\Teacher::SALARY_AMOUNT_TYPE_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('salary_amount_type', 'fixed') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-[10px] text-text-secondary">Used when salary_type is 'fixed'</p>
                        </div>
                    </div>
                </div>
                <!-- Section 3: Academic Assignment -->
                <div class="p-6 lg:p-8">
                    <h2 class="text-xl font-bold text-text-main dark:text-white mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">menu_book</span>
                        Academic Assignment
                    </h2>
                    <div>
                        <label class="block text-sm font-medium text-text-main dark:text-gray-300 mb-2">Assign
                            Subjects</label>
                        <select name="subjects[]" id="subjects" class="form-control select2 w-full" multiple>
                            @foreach($subjects as $id => $name)
                                <option value="{{ $id }}" {{ in_array($id, old('subjects', [])) ? 'selected' : '' }}>{{ $name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-text-secondary dark:text-gray-400">You can assign multiple subjects for
                            this teacher.</p>
                        @if($errors->has('subjects'))
                            <p class="mt-1 text-xs text-red-500">{{ $errors->first('subjects') }}</p>
                        @endif
                    </div>
                </div>
                <!-- Footer Actions -->
                <div
                    class="p-6 lg:p-8 bg-background-light/50 dark:bg-black/20 border-t border-border-light dark:border-border-dark flex items-center justify-end gap-4">
                    <a href="{{ route('admin.teachers.index') }}"
                        class="px-6 py-2.5 rounded-lg text-sm font-medium text-text-secondary dark:text-gray-300 hover:text-text-main hover:bg-white dark:hover:bg-white/5 transition-colors">
                        Cancel
                    </a>
                    <button
                        class="px-6 py-2.5 rounded-lg text-sm font-medium text-white bg-primary hover:bg-primary-hover shadow-lg shadow-primary/30 transition-all transform active:scale-95 flex items-center gap-2"
                        type="submit">
                        <span class="material-symbols-outlined !text-[20px]">check</span>
                        Register Teacher
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Initialize Select2
            $('#subjects').select2({
                placeholder: 'Select subjects',
                allowClear: true,
                width: '100%'
            });

            // Initialize Datetimepicker
            if ($.fn.datetimepicker) {
                $('#joining_date').datetimepicker({
                    format: 'YYYY-MM-DD HH:mm:ss',
                    locale: 'en',
                    sideBySide: true,
                    icons: {
                        up: 'fas fa-chevron-up',
                        down: 'fas fa-chevron-down',
                        previous: 'fas fa-chevron-left',
                        next: 'fas fa-chevron-right',
                        today: 'fa fa-arrows-alt',
                        clear: 'fa fa-trash',
                        close: 'fa fa-times'
                    }
                });
            }

            // Photo Upload Logic
            const fileUpload = document.getElementById('file-upload');
            const dropZone = document.getElementById('drop-zone');
            const photoPreview = document.getElementById('photo-preview');
            const placeholderIcon = document.getElementById('photo-placeholder-icon');
            const teacherForm = document.querySelector('form');

            function handleFile(file) {
                if (!file || !file.type.startsWith('image/')) {
                    alert('Please upload a valid image file.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    photoPreview.style.backgroundImage = `url('${e.target.result}')`;
                    if (placeholderIcon) placeholderIcon.classList.add('hidden');
                };
                reader.readAsDataURL(file);

                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', '{{ csrf_token() }}');

                dropZone.classList.add('opacity-50', 'cursor-wait');

                fetch('{{ route('admin.teachers.storeMedia') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Upload failed');
                        return response.json();
                    })
                    .then(data => {
                        if (data.name) {
                            const existingHidden = teacherForm.querySelectorAll('input[name="profile_img"][type="hidden"]');
                            existingHidden.forEach(el => el.remove());

                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'profile_img';
                            hiddenInput.value = data.name;
                            teacherForm.appendChild(hiddenInput);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to upload image. Please try again.');
                    })
                    .finally(() => {
                        dropZone.classList.remove('opacity-50', 'cursor-wait');
                    });
            }

            if (fileUpload) {
                fileUpload.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) handleFile(e.target.files[0]);
                });
            }

            if (dropZone) {
                dropZone.addEventListener('click', (e) => {
                    if (!e.target.closest('label')) fileUpload.click();
                });

                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                    }, false);
                });

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => {
                        dropZone.classList.add('border-primary', 'bg-primary/5');
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => {
                        dropZone.classList.remove('border-primary', 'bg-primary/5');
                    }, false);
                });

                dropZone.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    if (dt.files && dt.files.length > 0) handleFile(dt.files[0]);
                });
            }
        });
    </script>

@endsection