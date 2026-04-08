@extends('layouts.admin')
@section('content')
    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 md:p-8" style="margin-top: -2rem">
        <div class="max-w-5xl mx-auto flex flex-col gap-6 pb-12">
            <!-- Breadcrumbs -->
            <nav aria-label="Breadcrumb" class="flex">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-white"
                            href="{{ route('admin.home') }}">
                            <span class="material-symbols-outlined text-[18px] mr-2">home</span>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                            <a class="ml-1 text-sm font-medium text-slate-500 hover:text-primary md:ml-2 dark:text-slate-400 dark:hover:text-white"
                                href="{{ route('admin.student-basic-infos.index') }}">Students</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                            <span class="ml-1 text-sm font-medium text-slate-900 md:ml-2 dark:text-white">Add New
                                Student</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Add New Student
                    </h1>
                    <p class="mt-1 text-slate-500 dark:text-slate-400">
                        Fill in the details below to register a new student to the database.
                    </p>
                </div>
            </div>



            <!-- Main Form Card -->
            <form method="POST" action="{{ route('admin.student-basic-infos.store') }}" enctype="multipart/form-data"
                class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                @csrf
                <!-- Personal Information -->
                <div class="p-6 md:p-8 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between gap-2 mb-6 text-primary">
                        <div class="flex">
                            <span class="material-symbols-outlined">person</span>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Personal Information</h3>
                        </div>
                        <div>
                            <div class="flex items-center">
                                <input class="h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary"
                                    type="checkbox" name="need_login" id="need_login" checked />
                                <label class="ml-2 mb-0 text-sm font-medium text-slate-700 dark:text-slate-300"
                                    for="need_login">
                                    <strong class="text-primary">
                                        Need user for login ?
                                    </strong>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                        <!-- Photo Upload -->
                        <div class="md:col-span-4 flex flex-col gap-4">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Student
                                Photo</label>
                            <div id="drop-zone"
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-600 border-dashed rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer group">
                                <div class="space-y-1 text-center">
                                    <div id="photo-preview"
                                        class="mx-auto h-24 w-24 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform bg-cover bg-center">
                                        <span id="photo-placeholder-icon"
                                            class="material-symbols-outlined text-slate-400 text-4xl">add_a_photo</span>
                                    </div>
                                    <div class="flex text-sm text-slate-600 dark:text-slate-400 justify-center">
                                        <label
                                            class="relative cursor-pointer bg-white dark:bg-transparent rounded-md font-medium text-primary hover:text-blue-500 focus-within:outline-none"
                                            for="file-upload">
                                            <span>Upload a file</span>
                                            <input class="sr-only" id="file-upload" name="file-upload" type="file"
                                                accept="image/*" />
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-slate-500">PNG, JPG, GIF up to 2MB</p>
                                </div>
                            </div>
                        </div>




                        <!-- Inputs -->
                        <div class="md:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- first_name --}}
                            <div class="col-span-1">

                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                    for="first_name">{{ trans('cruds.studentBasicInfo.fields.first_name') }}</label>
                                <input
                                    class=" {{ $errors->has('first_name') ? 'is-invalid' : '' }} mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                    id="first_name" name="first_name" placeholder="e.g. John" type="text"
                                    value="{{ old('first_name', '') }}" required />
                                @if ($errors->has('first_name'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('first_name') }}
                                    </div>
                                @endif
                                <span
                                    class="help-block">{{ trans('cruds.studentBasicInfo.fields.first_name_helper') }}</span>
                            </div>

                            {{-- last_name --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                    for="last_name">{{ trans('cruds.studentBasicInfo.fields.last_name') }}</label>
                                <input
                                    class=" {{ $errors->has('last_name') ? 'is-invalid' : '' }} mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                    id="last_name" name="last_name" placeholder="e.g. Doe" type="text"
                                    value="{{ old('last_name', '') }}" />
                                @if ($errors->has('last_name'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('last_name') }}
                                    </div>
                                @endif
                                <span
                                    class="help-block">{{ trans('cruds.studentBasicInfo.fields.last_name_helper') }}</span>
                            </div>


                            {{-- contact_number --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                    for="contact_number">{{ trans('cruds.studentBasicInfo.fields.contact_number') }}</label>
                                <input
                                    class=" {{ $errors->has('contact_number') ? 'is-invalid' : '' }} mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                    id="contact_number" name="contact_number" placeholder="e.g. 01685******"
                                    value="{{ old('contact_number', '') }}" type="tel" required />
                                @if ($errors->has('contact_number'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('contact_number') }}
                                    </div>
                                @endif
                                <span
                                    class="help-block">{{ trans('cruds.studentBasicInfo.fields.contact_number_helper') }}</span>
                            </div>

                            {{-- email --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 "
                                    for="email">{{ trans('cruds.studentBasicInfo.fields.email') }}</label>
                                <input
                                    class=" {{ $errors->has('email') ? 'is-invalid' : '' }} mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                    id="email" name="email" placeholder="e.g. email@example.com" type="email"
                                    value="{{ old('email', '') }}" />
                                @if ($errors->has('email'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('email') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.studentBasicInfo.fields.email_helper') }}</span>
                            </div>

                            {{-- password --}}
                            <div class="col-span-1" id="password-field">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 "
                                    for="password">{{ trans('cruds.studentBasicInfo.fields.password') }}</label>
                                <div class="relative mt-1">
                                    <input
                                        class=" {{ $errors->has('password') ? 'is-invalid' : '' }} block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3 pr-12"
                                        id="password" name="password"
                                        placeholder="8 characters minimum and uppercase + lowercase letter"
                                        type="password" value="{{ old('password', '') }}" />
                                    <button type="button"
                                        class="password-toggle-btn absolute inset-y-0 right-0 px-3 flex items-center text-slate-500 hover:text-primary focus:outline-none"
                                        data-target="password" aria-label="Show password" aria-pressed="false">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </button>
                                </div>
                                @if ($errors->has('password'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('password') }}
                                    </div>
                                @endif
                                <span
                                    class="help-block">{{ trans('cruds.studentBasicInfo.fields.password_helper') }}</span>
                            </div>


                            {{-- dob: date of birth --}}
                            <div class="col-span-1">
                                <label
                                    class=" {{ $errors->has('dob') ? 'is-invalid' : '' }} block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                    for="dob">{{ trans('cruds.studentBasicInfo.fields.dob') }}</label>
                                <input
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                    id="dob" name="dob" type="date" value="{{ old('dob', '') }}"
                                    required />
                                @if ($errors->has('dob'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('dob') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.studentBasicInfo.fields.dob_helper') }}</span>
                            </div>

                            {{-- gender --}}
                            <div class="col-span-1">
                                <label
                                    class="{{ $errors->has('gender') ? 'is-invalid' : '' }}  block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                    for="gender">{{ trans('cruds.studentBasicInfo.fields.gender') }}</label>
                                <select
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                    id="gender" name="gender" required>
                                    <option value="{{ null }}">Select Gender</option>
                                    @foreach (App\Models\StudentBasicInfo::GENDER_RADIO as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('gender') == $key ? 'selected' : '' }}>{{ $label }}
                                        </option>
                                    @endforeach
                                    {{-- <option>Male</option>
                                    <option>Female</option>
                                    <option>Other</option> --}}
                                </select>
                                @if ($errors->has('gender'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('gender') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.studentBasicInfo.fields.gender_helper') }}</span>
                            </div>

                            {{-- student_blood_group --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                    for="student_blood_group">{{ trans('cruds.studentDetailsInformation.fields.student_blood_group') }}</label>
                                <select
                                    class="{{ $errors->has('student_blood_group') ? 'is-invalid' : '' }}  mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                    id="student_blood_group" name="student_blood_group" required>
                                    <option>Select</option>
                                    <option value="A+" {{ old('student_blood_group') == 'A+' ? 'selected' : '' }}>A+
                                    </option>
                                    <option value="A-" {{ old('student_blood_group') == 'A-' ? 'selected' : '' }}>A-
                                    </option>
                                    <option value="B+" {{ old('student_blood_group') == 'B+' ? 'selected' : '' }}>B+
                                    </option>
                                    <option value="B-" {{ old('student_blood_group') == 'B-' ? 'selected' : '' }}>B-
                                    </option>
                                    <option value="O+" {{ old('student_blood_group') == 'O+' ? 'selected' : '' }}>O+
                                    </option>
                                    <option value="O-" {{ old('student_blood_group') == 'O-' ? 'selected' : '' }}>O-
                                    </option>
                                    <option value="AB+" {{ old('student_blood_group') == 'AB+' ? 'selected' : '' }}>AB+
                                    </option>
                                    <option value="AB-" {{ old('student_blood_group') == 'AB-' ? 'selected' : '' }}>AB-
                                    </option>
                                </select>
                                @if ($errors->has('student_blood_group'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('student_blood_group') }}
                                    </div>
                                @endif
                                <span
                                    class="help-block">{{ trans('cruds.studentDetailsInformation.fields.student_blood_group_helper') }}</span>
                            </div>

                            {{-- religion --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                    for="religion">
                                    Religion
                                </label>
                                <input
                                    class=" {{ $errors->has('religion') ? 'is-invalid' : '' }} mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                    id="religion" name="religion" placeholder="e.g. Muslim" type="text"
                                    value="{{ old('religion', '') }}" />
                                @if ($errors->has('religion'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('religion') }}
                                    </div>
                                @endif
                                <span
                                    class="help-block">{{ trans('cruds.studentDetailsInformation.fields.religion_helper') }}</span>
                            </div>
                        </div>
                    </div>
                </div>





                <!-- Academic Details -->
                <div class="p-6 md:p-8 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-2 mb-6 text-primary">
                        <span class="material-symbols-outlined">school</span>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Academic Details</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        {{-- id_no --}}
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                for="id_no">{{ trans('cruds.studentBasicInfo.fields.id_no') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-slate-500 sm:text-sm">#</span>
                                </div>
                                <input
                                    class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-white pl-7 shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                    id="id_no" name="id_no" readonly="" type="text"
                                    value="    {{ $latest_id_no }}" />
                                {{-- value=" ST-{{ date('Y') }}-{{ date('m') }}-{{ \App\Models\StudentBasicInfo::count() + 1
                                }}" /> --}}
                            </div>
                            <small class="text-xs text-slate-500 dark:text-slate-400">{{ trans('cruds.studentBasicInfo.fields.id_no_helper') }}</small>
                        </div>

                        {{-- class_id --}}
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                for="class_id">{{ trans('cruds.studentBasicInfo.fields.class') }}</label>
                            <select
                                class="{{ $errors->has('class') ? 'is-invalid' : '' }} mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                id="class_id" name="class_id">
                                @foreach ($classes as $id => $entry)
                                    <option value="{{ $id }}" {{ old('class_id') == $id ? 'selected' : '' }}>
                                        {{ $entry }}
                                    </option>
                                @endforeach
                                {{-- <option>Select Class</option> --}}
                                {{-- <option>Grade 1</option>
                                <option>Grade 2</option>
                                <option>Grade 3</option>
                                <option>Grade 4</option>
                                <option>Grade 5</option>
                                <option>Grade 6</option> --}}
                            </select>
                            @if ($errors->has('class'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('class') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.studentBasicInfo.fields.class_helper') }}</span>
                        </div>



                        {{-- section_id (not needed now) --}}
                        {{--
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="section_id">
                                {{ trans('cruds.studentBasicInfo.fields.section') }}
                            </label>
                            <select
                                class=" {{ $errors->has('section') ? 'is-invalid' : '' }} mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                id="section_id" name="section_id">
                                @foreach ($sections as $id => $entry)
                                    <option value="{{ $id }}" {{ old('section_id') == $id ? 'selected' : '' }}>
                                        {{ $entry }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('section'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('section') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.studentBasicInfo.fields.section_helper') }}</span>
                        </div>
                        --}}


                        {{-- academic_background_id --}}
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                for="academic_background_id">
                                {{ trans('cruds.studentBasicInfo.fields.academic_background') }}
                            </label>
                            <select
                                class="{{ $errors->has('academic_background_id') ? 'is-invalid' : '' }} mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                id="academic_background_id" name="academic_background_id">
                                @foreach ($academicBackgrounds as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ old('academic_background_id') == $id ? 'selected' : '' }}>
                                        {{ $entry }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('academic_background_id'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('academic_background_id') }}
                                </div>
                            @endif
                            <span
                                class="help-block">{{ trans('cruds.studentBasicInfo.fields.academic_background_helper') }}</span>
                        </div>

                        {{-- roll --}}
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 "
                                for="roll">{{ trans('cruds.studentBasicInfo.fields.roll') }}</label>
                            <input
                                class=" {{ $errors->has('roll') ? 'is-invalid' : '' }} mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                id="roll" name="roll" placeholder="e.g. 15" type="number"
                                value="{{ old('roll', $latest_id_no) }}" />
                            @if ($errors->has('roll'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('roll') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.studentBasicInfo.fields.roll_helper') }}</span>
                        </div>

                        {{-- joining_date --}}
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                for="joining_date">{{ trans('cruds.studentBasicInfo.fields.joining_date') }}</label>
                            <input
                                class=" {{ $errors->has('joining_date') ? 'is-invalid' : '' }} mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                id="joining_date" name="joining_date" type="date"
                                value="{{ old('joining_date', now()->format('Y-m-d')) }}" required />

                            @if ($errors->has('joining_date'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('joining_date') }}
                                </div>
                            @endif
                            <span
                                class="help-block">{{ trans('cruds.studentBasicInfo.fields.joining_date_helper') }}</span>
                        </div>

                        {{-- batches --}}
                        <div class="col-span-1 md:col-span-3">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="batches">
                                Batches
                            </label>
                            <div class="mt-1">
                                <select
                                    class="select2 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                    name="batches[]" id="batches" multiple>
                                    @foreach ($batches as $id => $batch)
                                        <option value="{{ $id }}"
                                            {{ in_array($id, old('batches', [])) ? 'selected' : '' }}>
                                            {{ $batch }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>



                    </div>
                </div>




                <!-- Guardian Information -->
                <div class="p-6 md:p-8">
                    <div class="flex items-center gap-2 mb-6 text-primary">
                        <span class="material-symbols-outlined">family_restroom</span>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Guardian / Contact Info</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- fathers_name --}}
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                for="fathers_name">{{ trans('cruds.studentDetailsInformation.fields.fathers_name') }}</label>
                            <input
                                class=" {{ $errors->has('fathers_name') ? 'is-invalid' : '' }} mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                id="fathers_name" name="fathers_name" value="{{ old('fathers_name', '') }}"
                                placeholder="Father's Full Name" type="text" required />
                            @if ($errors->has('fathers_name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('fathers_name') }}
                                </div>
                            @endif
                            <span
                                class="help-block">{{ trans('cruds.studentDetailsInformation.fields.fathers_name_helper') }}</span>
                        </div>

                        {{-- mothers_name --}}
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                for="mothers_name">{{ trans('cruds.studentDetailsInformation.fields.mothers_name') }}</label>
                            <input
                                class=" {{ $errors->has('mothers_name') ? 'is-invalid' : '' }} mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                id="mothers_name" name="mothers_name" value="{{ old('mothers_name', '') }}"
                                placeholder="Mother's Full Name" type="text" required />
                            @if ($errors->has('mothers_name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('mothers_name') }}
                                </div>
                            @endif
                            <span
                                class="help-block">{{ trans('cruds.studentDetailsInformation.fields.mothers_name_helper') }}</span>
                        </div>




                        {{-- guardian_relation --}}
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required">
                                Relation with Student
                            </label>
                            <div class="mt-2 flex flex-wrap gap-6">
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="guardian_relation_type" value="Father"
                                        class="w-4 h-4 text-primary border-slate-300 focus:ring-primary dark:border-slate-600 dark:bg-slate-700"
                                        {{ old('guardian_relation_type', 'Father') == 'Father' ? 'checked' : '' }}
                                        required>
                                    <span class="ml-2 text-sm font-medium text-slate-700 dark:text-slate-300">Father</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="guardian_relation_type" value="Mother"
                                        class="w-4 h-4 text-primary border-slate-300 focus:ring-primary dark:border-slate-600 dark:bg-slate-700"
                                        {{ old('guardian_relation_type') == 'Mother' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm font-medium text-slate-700 dark:text-slate-300">Mother</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="guardian_relation_type" value="Other"
                                        id="relation_other"
                                        class="w-4 h-4 text-primary border-slate-300 focus:ring-primary dark:border-slate-600 dark:bg-slate-700"
                                        {{ old('guardian_relation_type') == 'Other' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm font-medium text-slate-700 dark:text-slate-300">Other</span>
                                </label>
                            </div>

                            <div id="other_relation_container"
                                class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4 {{ old('guardian_relation_type') == 'Other' ? '' : 'hidden' }}">

                                {{-- guardian_name --}}
                                <div class="col-span-1">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 "
                                        for="guardian_name">{{ trans('cruds.studentDetailsInformation.fields.guardian_name') }}</label>
                                    <input
                                        class=" {{ $errors->has('guardian_name') ? 'is-invalid' : '' }} mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                        id="guardian_name" name="guardian_name" placeholder="Full Name" type="text"
                                        value="{{ old('guardian_name', '') }}" />
                                    @if ($errors->has('guardian_name'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('guardian_name') }}
                                        </div>
                                    @endif
                                    <span class="help-block">
                                        {{ trans('cruds.studentDetailsInformation.fields.guardian_name_helper') }}
                                    </span>
                                </div>


                                {{-- guardian_relation --}}
                                <div class="col-span-1">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 "
                                        for="guardian_relation">{{ trans('cruds.studentDetailsInformation.fields.guardian_relation') }}</label>
                                    <input
                                        class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                        id="guardian_relation_other" name="guardian_relation_other"
                                        placeholder="Specify relation (e.g. Uncle, Aunt, Brother)" type="text"
                                        value="{{ old('guardian_relation_other', '') }}" />

                                    @if ($errors->has('guardian_relation'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('guardian_relation') }}
                                        </div>
                                    @endif
                                    <span class="help-block">
                                        {{ trans('cruds.studentDetailsInformation.fields.guardian_relation_helper') }}
                                    </span>
                                </div>

                            </div>


                        </div>

                        {{-- guardian_email --}}
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                for="guardian_email">{{ trans('cruds.studentDetailsInformation.fields.guardian_email') }}</label>
                            <input
                                class=" {{ $errors->has('guardian_email') ? 'is-invalid' : '' }} mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                id="guardian_email" name="guardian_email" placeholder="email@example.com"
                                value="{{ old('guardian_email', '') }}" type="email" />

                            @if ($errors->has('guardian_email'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('guardian_email') }}
                                </div>
                            @endif
                            <span
                                class="help-block">{{ trans('cruds.studentDetailsInformation.fields.guardian_email_helper') }}</span>
                        </div>

                        {{-- guardian_contact_number --}}
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                for="guardian_contact_number">{{ trans('cruds.studentDetailsInformation.fields.guardian_contact_number') }}</label>
                            <input
                                class=" {{ $errors->has('guardian_contact_number') ? 'is-invalid' : '' }} mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                id="guardian_contact_number" name="guardian_contact_number"
                                value="{{ old('guardian_contact_number', '') }}" placeholder="e.g. 01xxxxxxxxx"
                                type="tel" required />
                            @if ($errors->has('guardian_contact_number'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('guardian_contact_number') }}
                                </div>
                            @endif
                            <span
                                class="help-block">{{ trans('cruds.studentDetailsInformation.fields.guardian_contact_number_helper') }}</span>
                        </div>

                        {{-- address --}}
                        <div class="col-span-1 md:col-span-2">
                            <label
                                class=" {{ $errors->has('address') ? 'is-invalid' : '' }} block text-sm font-medium text-slate-700 dark:text-slate-300"
                                for="address">{{ trans('cruds.studentDetailsInformation.fields.address') }}</label>
                            <textarea
                                class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                id="address" name="address" placeholder="Enter full address..." rows="3">{{ old('address', '') }}</textarea>
                            @if ($errors->has('address'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('address') }}
                                </div>
                            @endif
                            <span
                                class="help-block">{{ trans('cruds.studentDetailsInformation.fields.address_helper') }}</span>
                        </div>
                    </div>
                </div>
                <!-- Form Actions Footer -->
                <div
                    class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-3">
                    <button
                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700"
                        type="reset">
                        Reset Form
                    </button>
                    <button
                        class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-lg shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                        type="submit">
                        Submit Registration
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Need Login Toggle
            $("#need_login").change(function() {
                if ($(this).is(":checked")) {
                    $("#password-field").removeClass("d-none").fadeIn();
                } else {
                    $("#password-field").addClass("d-none").fadeOut();
                }
            });

            // Password show/hide toggle
            $('.password-toggle-btn').on('click', function() {
                const targetId = $(this).data('target');
                const input = document.getElementById(targetId);

                if (!input) return;

                const icon = $(this).find('.material-symbols-outlined');
                const isPassword = input.type === 'password';

                input.type = isPassword ? 'text' : 'password';
                icon.text(isPassword ? 'visibility_off' : 'visibility');
                $(this).attr('aria-label', isPassword ? 'Hide password' : 'Show password');
                $(this).attr('aria-pressed', isPassword ? 'true' : 'false');
            });

            // Guardian Relation Toggle
            $('input[name="guardian_relation_type"]').change(function() {
                if ($(this).val() === 'Other') {
                    $('#other_relation_container').removeClass('hidden').fadeIn();
                    $('#guardian_relation_other').attr('required', true);
                } else {
                    $('#other_relation_container').addClass('hidden').fadeOut();
                    $('#guardian_relation_other').attr('required', false);
                }
            });

            // Image Upload Control Elements
            const fileUpload = document.getElementById('file-upload');
            const dropZone = document.getElementById('drop-zone');
            const photoPreview = document.getElementById('photo-preview');
            const placeholderIcon = document.getElementById('photo-placeholder-icon');
            const studentForm = document.querySelector('form');

            /**
             * Handles the file selection/drop process
             * @param {File} file 
             */
            function handleFile(file) {
                if (!file || !file.type.startsWith('image/')) {
                    alert('Please upload a valid image file.');
                    return;
                }

                // UI: Show Preview immediately
                const reader = new FileReader();
                reader.onload = (e) => {
                    photoPreview.style.backgroundImage = `url('${e.target.result}')`;
                    photoPreview.style.backgroundSize = 'cover';
                    photoPreview.style.backgroundPosition = 'center';
                    if (placeholderIcon) placeholderIcon.classList.add('hidden');
                };
                reader.readAsDataURL(file);

                // Backend: AJAX Upload to temporary storage (matches controller logic)
                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', '{{ csrf_token() }}');

                // Visual feedback during upload
                dropZone.classList.add('opacity-50', 'cursor-wait');

                fetch('{{ route('admin.student-basic-infos.storeMedia') }}', {
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
                            // Find and remove any existing hidden inputs for this field
                            const existingHidden = studentForm.querySelectorAll(
                                'input[name="file-upload"][type="hidden"]');
                            existingHidden.forEach(el => el.remove());

                            // Add hidden input with the filename returned from temporary storage
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'file-upload';
                            hiddenInput.value = data.name;
                            studentForm.appendChild(hiddenInput);
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
                    if (e.target.files.length > 0) {
                        handleFile(e.target.files[0]);
                    }
                });
            }

            if (dropZone) {
                // Click to trigger file input
                dropZone.addEventListener('click', (e) => {
                    if (!e.target.closest('label')) {
                        fileUpload.click();
                    }
                });

                // Drag and Drop support
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                    }, false);
                });

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => {
                        dropZone.classList.add('bg-slate-50', 'dark:bg-slate-800/50',
                            'border-primary');
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => {
                        dropZone.classList.remove('bg-slate-50', 'dark:bg-slate-800/50',
                            'border-primary');
                    }, false);
                });

                dropZone.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    if (dt.files && dt.files.length > 0) {
                        handleFile(dt.files[0]);
                    }
                });
            }

            // Form Reset handler
            if (studentForm) {
                studentForm.addEventListener('reset', () => {
                    photoPreview.style.backgroundImage = 'none';
                    if (placeholderIcon) placeholderIcon.classList.remove('hidden');
                    const hiddenInput = studentForm.querySelector(
                        'input[name="file-upload"][type="hidden"]');
                    if (hiddenInput) hiddenInput.remove();
                });
            }
        });
    </script>



    <script>
        var uploadedPaymentProofMap = {}
        Dropzone.options.paymentProofDropzone = {
            url: '{{ route('admin.student-basic-infos.storeMedia') }}',
            maxFilesize: 2, // MB
            acceptedFiles: '.jpeg,.jpg,.png,.gif',
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 2,
                width: 4096,
                height: 4096
            },
            success: function(file, response) {
                $('form').append('<input type="hidden" name="payment_proof[]" value="' + response.name + '">')
                uploadedPaymentProofMap[file.name] = response.name
            },
            removedfile: function(file) {
                console.log(file)
                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedPaymentProofMap[file.name]
                }
                $('form').find('input[name="payment_proof[]"][value="' + name + '"]').remove()
            },
            init: function() {
                @if (isset($expense) && $expense->payment_proof)
                    var files = {!! json_encode($expense->payment_proof) !!}
                    for (var i in files) {
                        var file = files[i]
                        this.options.addedfile.call(this, file)
                        this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
                        file.previewElement.classList.add('dz-complete')
                        $('form').append('<input type="hidden" name="payment_proof[]" value="' + file.file_name +
                            '">')
                    }
                @endif
            },
            error: function(file, response) {
                if ($.type(response) === 'string') {
                    var message = response //dropzone sends it's own error messages in string
                } else {
                    var message = response.errors.file
                }
                file.previewElement.classList.add('dz-error')
                _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
                _results = []
                for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                    node = _ref[_i]
                    _results.push(node.textContent = message)
                }

                return _results
            }
        }
    </script>




    {{-- Old Code --}}
    {{--
    <script>
        Dropzone.options.imageDropzone = {
            url: '{{ route('admin.student - basic - infos.storeMedia') }}',
            maxFilesize: 2, // MB
            acceptedFiles: '.jpeg,.jpg,.png,.gif',
            maxFiles: 1,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 2,
                width: 4096,
                height: 4096
            },
            success: function (file, response) {
                $('form').find('input[name="image"]').remove()
                $('form').append('<input type="hidden" name="image" value="' + response.name + '">')
            },
            removedfile: function (file) {
                file.previewElement.remove()
                if (file.status !== 'error') {
                    $('form').find('input[name="image"]').remove()
                    this.options.maxFiles = this.options.maxFiles + 1
                }
            },
            init: function () {
                @if (isset($studentBasicInfo) && $studentBasicInfo->image)
                    var file = {!! json_encode($studentBasicInfo -> image)!!
            }
                                this.options.addedfile.call(this, file)
                                this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
                                file.previewElement.classList.add('dz-complete')
                                $('form').append('<input type="hidden" name="image" value="' + file.file_name + '">')
                                this.options.maxFiles = this.options.maxFiles - 1
                            @endif
                        },
        error: function(file, response) {
            if ($.type(response) === 'string') {
                var message = response //dropzone sends it's own error messages in string
            } else {
                var message = response.errors.file
            }
            file.previewElement.classList.add('dz-error')
            _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
            _results = []
            for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                node = _ref[_i]
                _results.push(node.textContent = message)
            }

            return _results
        }
                    }
    </script> --}}
@endsection
