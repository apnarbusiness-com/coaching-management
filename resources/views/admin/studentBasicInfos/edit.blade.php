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
                            <span class="ml-1 text-sm font-medium text-slate-900 md:ml-2 dark:white">Edit Student</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Edit Student:
                        {{ $studentBasicInfo->first_name }} {{ $studentBasicInfo->last_name }}</h1>
                    <p class="mt-1 text-slate-500 dark:text-slate-400">Update the student information below.</p>
                </div>
            </div>

            <!-- Main Form Card -->
            <form method="POST" action="{{ route('admin.student-basic-infos.update', [$studentBasicInfo->id]) }}"
                enctype="multipart/form-data"
                class="bg-white dark:bg-[#1a2632] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                @method('PUT')
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
                                    type="checkbox" name="need_login" id="need_login"
                                    {{ $studentBasicInfo->user_id ? 'checked' : '' }} />
                                <label class="ml-2 mb-0 text-sm font-medium text-slate-700 dark:text-slate-300"
                                    for="need_login">
                                    <strong class="text-primary">
                                        Has login user ?
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
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-600 border-dashed rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer group relative overflow-hidden h-48 w-full">
                                <div class="space-y-1 text-center flex flex-col items-center justify-center w-full h-full bg-cover bg-center"
                                    id="photo-preview"
                                    style="{{ $studentBasicInfo->image ? 'background-image: url(' . $studentBasicInfo->image->getUrl('preview') . ');' : '' }}">

                                    <div id="photo-placeholder-content"
                                        class="{{ $studentBasicInfo->image ? 'hidden' : '' }}">
                                        <span class="material-symbols-outlined text-slate-400 text-4xl">add_a_photo</span>
                                        <div class="flex text-sm text-slate-600 dark:text-slate-400 justify-center mt-2">
                                            <label
                                                class="relative cursor-pointer bg-white dark:bg-transparent rounded-md font-medium text-primary hover:text-blue-500 focus-within:outline-none">
                                                <span>Change Photo</span>
                                                <input class="sr-only" id="file-upload" name="file-upload" type="file"
                                                    accept="image/*" />
                                            </label>
                                        </div>
                                        <p class="text-xs text-slate-500">PNG, JPG up to 2MB</p>
                                    </div>

                                    <!-- Hidden overlay for hover if image exists -->
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center {{ $studentBasicInfo->image ? '' : 'hidden' }}"
                                        id="photo-overlay">
                                        <div class="text-white flex flex-col items-center">
                                            <span class="material-symbols-outlined">edit</span>
                                            <span class="text-xs font-medium">Click to change</span>
                                        </div>
                                    </div>

                                    <input class="sr-only" id="file-upload" name="file-upload" type="file"
                                        accept="image/*" />
                                </div>
                            </div>

                            {{-- Field for temporary storage filename --}}
                            <input type="hidden" name="image" id="image-hidden-input"
                                value="{{ $studentBasicInfo->image ? $studentBasicInfo->image->file_name : '' }}">
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
                                    value="{{ old('first_name', $studentBasicInfo->first_name) }}" required />
                                @if ($errors->has('first_name'))
                                    <div class="invalid-feedback">{{ $errors->first('first_name') }}</div>
                                @endif
                            </div>

                            {{-- last_name --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                    for="last_name">{{ trans('cruds.studentBasicInfo.fields.last_name') }}</label>
                                <input
                                    class=" {{ $errors->has('last_name') ? 'is-invalid' : '' }} mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                    id="last_name" name="last_name" placeholder="e.g. Doe" type="text"
                                    value="{{ old('last_name', $studentBasicInfo->last_name) }}" />
                                @if ($errors->has('last_name'))
                                    <div class="invalid-feedback">{{ $errors->first('last_name') }}</div>
                                @endif
                            </div>

                            {{-- contact_number --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                    for="contact_number">{{ trans('cruds.studentBasicInfo.fields.contact_number') }}</label>
                                <input
                                    class=" {{ $errors->has('contact_number') ? 'is-invalid' : '' }} mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                    id="contact_number" name="contact_number" placeholder="e.g. 01685******"
                                    value="{{ old('contact_number', $studentBasicInfo->contact_number) }}" type="tel"
                                    required />
                                @if ($errors->has('contact_number'))
                                    <div class="invalid-feedback">{{ $errors->first('contact_number') }}</div>
                                @endif
                            </div>

                            {{-- email --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 "
                                    for="email">{{ trans('cruds.studentBasicInfo.fields.email') }}</label>
                                <input
                                    class=" {{ $errors->has('email') ? 'is-invalid' : '' }} mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                    id="email" name="email" placeholder="e.g. email@example.com" type="email"
                                    value="{{ old('email', $studentBasicInfo->email) }}" />
                                @if ($errors->has('email'))
                                    <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                                @endif
                            </div>

                            {{-- password --}}
                            <div class="col-span-1 {{ $studentBasicInfo->user_id ? '' : 'd-none' }}" id="password-field">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 "
                                    for="password">New password (optional)</label>
                                <div class="relative mt-1">
                                    <input
                                        class=" {{ $errors->has('password') ? 'is-invalid' : '' }} block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3 pr-12"
                                        id="password" name="password" placeholder="Leave blank to keep current"
                                        type="password" value="{{ old('password', '') }}" />
                                    <button type="button"
                                        class="password-toggle-btn absolute inset-y-0 right-0 px-3 flex items-center text-slate-500 hover:text-primary focus:outline-none"
                                        data-target="password" aria-label="Show password" aria-pressed="false">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </button>
                                </div>
                                @if ($errors->has('password'))
                                    <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                                @endif
                            </div>

                            {{-- dob --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                    for="dob">{{ trans('cruds.studentBasicInfo.fields.dob') }}</label>
                                <input
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                    id="dob" name="dob" type="date"
                                    value="{{ old('dob', $studentBasicInfo->getRawOriginal('dob')) }}" required />
                                @if ($errors->has('dob'))
                                    <div class="invalid-feedback">{{ $errors->first('dob') }}</div>
                                @endif
                            </div>

                            {{-- gender --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                    for="gender">{{ trans('cruds.studentBasicInfo.fields.gender') }}</label>
                                <select
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                    id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    @foreach (App\Models\StudentBasicInfo::GENDER_RADIO as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('gender', $studentBasicInfo->gender) == $key ? 'selected' : '' }}>
                                            {{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- blood group --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                    for="student_blood_group">{{ trans('cruds.studentDetailsInformation.fields.student_blood_group') }}</label>
                                <select
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                    id="student_blood_group" name="student_blood_group" required>
                                    <option value="">Select</option>
                                    @foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                                        <option value="{{ $bg }}"
                                            {{ old('student_blood_group', $studentBasicInfo->studentDetails->student_blood_group ?? '') == $bg ? 'selected' : '' }}>
                                            {{ $bg }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- status --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                    for="status">{{ trans('cruds.studentBasicInfo.fields.status') }}</label>
                                <select
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                    id="status" name="status" required>
                                    @foreach (App\Models\StudentBasicInfo::STATUS_SELECT as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('status', $studentBasicInfo->status) == $key ? 'selected' : '' }}>
                                            {{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- monthly_discount --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                    for="monthly_discount">Monthly Discount</label>
                                <input type="number" step="0.01" min="0"
                                    class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                    id="monthly_discount" name="monthly_discount"
                                    value="{{ old('monthly_discount', $studentBasicInfo->monthly_discount ?? 0) }}">
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
                                    id="id_no" name="id_no" readonly type="text"
                                    value="   {{ $studentBasicInfo->id_no }}" />
                            </div>
                        </div>

                        {{-- class_id --}}
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                for="class_id">{{ trans('cruds.studentBasicInfo.fields.class') }}</label>
                            <select
                                class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                id="class_id" name="class_id">
                                @foreach ($classes as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ old('class_id', $studentBasicInfo->class_id) == $id ? 'selected' : '' }}>
                                        {{ $entry }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- section_id (not needed now) --}}
                        {{--
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="section_id">
                                {{ trans('cruds.studentBasicInfo.fields.section') }}
                            </label>
                            <select
                                class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                id="section_id" name="section_id">
                                @foreach ($sections as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ old('section_id', $studentBasicInfo->section_id) == $id ? 'selected' : '' }}>
                                        {{ $entry }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        --}}

                        {{-- academic_background_id --}}
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                for="academic_background_id">
                                {{ trans('cruds.studentBasicInfo.fields.academic_background') }}
                            </label>
                            <select
                                class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                id="academic_background_id" name="academic_background_id">
                                @foreach ($academicBackgrounds as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ old('academic_background_id', $studentBasicInfo->academic_background_id) == $id ? 'selected' : '' }}>
                                        {{ $entry }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- roll --}}
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 "
                                for="roll">{{ trans('cruds.studentBasicInfo.fields.roll') }}</label>
                            <input
                                class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                id="roll" name="roll" placeholder="e.g. 15" type="number"
                                value="{{ old('roll', $studentBasicInfo->roll) }}" />
                        </div>

                        {{-- joining_date --}}
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                for="joining_date">{{ trans('cruds.studentBasicInfo.fields.joining_date') }}</label>
                            <input
                                class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                id="joining_date" name="joining_date" type="date"
                                value="{{ old('joining_date', $studentBasicInfo->getRawOriginal('joining_date') ? date('Y-m-d', strtotime($studentBasicInfo->getRawOriginal('joining_date'))) : '') }}"
                                required />
                        </div>

                        {{-- subjects --}}
                        {{-- <div class="col-span-1 md:col-span-3">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="subjects">
                                {{ trans('cruds.studentBasicInfo.fields.subject') }}
                            </label>
                            <div class="mt-1">
                                <select
                                    class="select2 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                                    name="subjects[]" id="subjects" multiple>
                                    @foreach ($subjects as $id => $subject)
                                        <option value="{{ $id }}"
                                            {{ in_array($id, old('subjects', [])) || $studentBasicInfo->subjects->contains($id) ? 'selected' : '' }}>
                                            {{ $subject }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}

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
                                            {{ in_array($id, old('batches', [])) || $studentBasicInfo->batches->contains($id) ? 'selected' : '' }}>
                                            {{ $batch }}</option>
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
                                id="fathers_name" name="fathers_name"
                                value="{{ old('fathers_name', $studentBasicInfo->studentDetails->fathers_name ?? '') }}"
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
                                id="mothers_name" name="mothers_name"
                                value="{{ old('mothers_name', $studentBasicInfo->studentDetails->mothers_name ?? '') }}"
                                placeholder="Mother's Full Name" type="text" required />
                            @if ($errors->has('mothers_name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('mothers_name') }}
                                </div>
                            @endif
                            <span
                                class="help-block">{{ trans('cruds.studentDetailsInformation.fields.mothers_name_helper') }}</span>
                        </div>

                        {{-- guardian_name --}}
                        {{-- <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                for="guardian_name">{{ trans('cruds.studentDetailsInformation.fields.guardian_name') }}</label>
                            <input
                                class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                id="guardian_name" name="guardian_name" placeholder="Full Name" type="text"
                                value="{{ old('guardian_name', $studentBasicInfo->studentDetails->guardian_name ?? '') }}"
                                required />
                        </div> --}}

                        {{-- guardian_relation --}}
                        @php
                            $relation = $studentBasicInfo->studentDetails->guardian_relation ?? 'Father';
                            $isStandard = in_array($relation, ['Father', 'Mother']);
                        @endphp
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required">
                                Relation with Student
                            </label>
                            <div class="mt-2 flex flex-wrap gap-6">
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="guardian_relation_type" value="Father"
                                        class="w-4 h-4 text-primary border-slate-300 focus:ring-primary dark:border-slate-600 dark:bg-slate-700"
                                        {{ old('guardian_relation_type', $isStandard ? $relation : '') == 'Father' || (!$isStandard && old('guardian_relation_type') == 'Father') ? 'checked' : '' }}
                                        required>
                                    <span class="ml-2 text-sm font-medium text-slate-700 dark:text-slate-300">Father</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="guardian_relation_type" value="Mother"
                                        class="w-4 h-4 text-primary border-slate-300 focus:ring-primary dark:border-slate-600 dark:bg-slate-700"
                                        {{ old('guardian_relation_type', $isStandard ? $relation : '') == 'Mother' || (!$isStandard && old('guardian_relation_type') == 'Mother') ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm font-medium text-slate-700 dark:text-slate-300">Mother</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="guardian_relation_type" value="Other"
                                        id="relation_other"
                                        class="w-4 h-4 text-primary border-slate-300 focus:ring-primary dark:border-slate-600 dark:bg-slate-700"
                                        {{ old('guardian_relation_type', $isStandard ? '' : 'Other') == 'Other' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm font-medium text-slate-700 dark:text-slate-300">Other</span>
                                </label>
                            </div>


                            {{-- other_relation_container --}}
                            <div id="other_relation_container"
                                class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4 {{ old('guardian_relation_type', $isStandard ? '' : 'Other') == 'Other' ? '' : 'hidden' }}">



                                {{-- guardian_name --}}
                                <div class="col-span-1">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 "
                                        for="guardian_name">{{ trans('cruds.studentDetailsInformation.fields.guardian_name') }}</label>
                                    <input
                                        class=" {{ $errors->has('guardian_name') ? 'is-invalid' : '' }} mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                        id="guardian_name" name="guardian_name" placeholder="Full Name" type="text"
                                        value="{{ old('guardian_name', $studentBasicInfo->studentDetails->guardian_name ?? '') }}" />
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
                                        value="{{ old('guardian_relation_other', !$isStandard ? $relation : '') }}" />

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
                                class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                id="guardian_email" name="guardian_email" placeholder="email@example.com"
                                value="{{ old('guardian_email', $studentBasicInfo->studentDetails->guardian_email ?? '') }}"
                                type="email" />
                        </div>

                        {{-- guardian_contact_number --}}
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 required"
                                for="guardian_contact_number">{{ trans('cruds.studentDetailsInformation.fields.guardian_contact_number') }}</label>
                            <input
                                class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                id="guardian_contact_number" name="guardian_contact_number"
                                value="{{ old('guardian_contact_number', $studentBasicInfo->studentDetails->guardian_contact_number ?? '') }}"
                                placeholder="e.g. 01xxxxxxxxx" type="tel" required />
                        </div>

                        {{-- address --}}
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"
                                for="address">{{ trans('cruds.studentDetailsInformation.fields.address') }}</label>
                            <textarea
                                class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                                id="address" name="address" placeholder="Enter full address..." rows="3">{{ old('address', $studentBasicInfo->studentDetails->address ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Form Actions Footer -->
                <div
                    class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.student-basic-infos.index') }}"
                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700">
                        Cancel
                    </a>
                    <button
                        class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-lg shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                        type="submit">
                        <span class="material-symbols-outlined text-[20px] mr-2">save</span>
                        Update Student
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
            const placeholderContent = document.getElementById('photo-placeholder-content');
            const photoOverlay = document.getElementById('photo-overlay');
            const hiddenInput = document.getElementById('image-hidden-input');
            const studentForm = document.querySelector('form');

            function handleFile(file) {
                if (!file || !file.type.startsWith('image/')) {
                    alert('Please upload a valid image file.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    photoPreview.style.backgroundImage = `url('${e.target.result}')`;
                    if (placeholderContent) placeholderContent.classList.add('hidden');
                    if (photoOverlay) photoOverlay.classList.remove('hidden');
                };
                reader.readAsDataURL(file);

                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', '{{ csrf_token() }}');

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
                            hiddenInput.value = data.name;
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
                dropZone.addEventListener('click', (e) => {
                    if (!e.target.closest('label')) {
                        fileUpload.click();
                    }
                });

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
        });
    </script>
@endsection
