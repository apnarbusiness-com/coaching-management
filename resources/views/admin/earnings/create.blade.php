@extends('layouts.admin')
@section('content')
    <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50 dark:bg-[#0F172A] relative">

        <div class="flex-1 overflow-y-auto p-4 md:p-8 lg:px-12">
            <div class="max-w-4xl mx-auto space-y-6">
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <a class="text-slate-500 hover:text-[#2563EB] dark:text-slate-400 dark:hover:text-[#60A5FA] transition-colors"
                        href="#">Home</a>
                    <span class="text-slate-400">/</span>
                    <a class="text-slate-500 hover:text-[#2563EB] dark:text-slate-400 dark:hover:text-[#60A5FA] transition-colors"
                        href="#">Finances</a>
                    <span class="text-slate-400">/</span>
                    <span class="text-[#1F2937] dark:text-[#F9FAFB] font-medium">Record Earnings</span>
                </div>
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-bold text-[#1F2937] dark:text-[#F9FAFB] tracking-tight">
                            Record Earnings</h2>
                        <p class="mt-2 text-slate-500 dark:text-slate-400">Record a new student fee or income
                            transaction with proof of payment.</p>
                    </div>
                    <button
                        class="p-2 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:ring-2 ring-primary-light/20 transition-all"
                        onclick="document.documentElement.classList.toggle('dark')">
                        <span class="material-symbols-outlined block dark:hidden">dark_mode</span>
                        <span class="material-symbols-outlined hidden dark:block">light_mode</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.earnings.store') }}" enctype="multipart/form-data"
                    class="bg-white dark:bg-[#1E293B] rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                    @csrf

                    <div
                        class="p-6 md:p-8  grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/50 dark:bg-slate-900/20 border-slate-100 dark:border-slate-800">
                        <!-- Earning Category -->
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Earning Category
                            </label>
                            <div class="relative">
                                <select name="earning_category_id" id="earning_category_id" required
                                    class="w-full pl-4 pr-10  appearance-none bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-[#2563EB]/20 dark:focus:ring-[#60A5FA]/20 focus:border-[#2563EB] dark:focus:border-[#60A5FA] text-[#1F2937] dark:text-[#F9FAFB] {{ $errors->has('earning_category') ? 'border-red-500' : '' }}">
                                    <!-- <option value="">Select category...</option> -->
                                    @foreach ($earning_categories as $id => $entry)
                                        <option value="{{ $id }}"
                                            data-student-connected="{{ ($earning_category_flags[$id] ?? false) ? 1 : 0 }}"
                                            {{ old('earning_category_id') == $id ? 'selected' : '' }}>
                                            {{ $entry }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('earning_category'))
                                    <p class="text-red-500 text-xs mt-1">{{ $errors->first('earning_category') }}</p>
                                @endif
                                {{-- <div
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined">expand_more</span>
                                </div> --}}
                            </div>
                        </div>

                        <!-- Transaction Title -->
                        <div class="space-y-1.5 ">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Transaction Title
                            </label>
                            <input name="title" id="title" required
                                class="w-full px-4  bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-700 
                                                                                                            rounded-lg focus:ring-2 focus:ring-[#2563EB]/20 dark:focus:ring-[#60A5FA]/20 
                                                                                                            focus:border-[#2563EB] dark:focus:border-[#60A5FA] text-[#1F2937] dark:text-[#F9FAFB] mt-0 {{ $errors->has('title') ? 'border-red-500' : '' }}"
                                placeholder="e.g. Tuition Fee for Grade 10" type="text" value="{{ old('title', '') }}" />
                            @if ($errors->has('title'))
                                <p class="text-red-500 text-xs mt-1">{{ $errors->first('title') }}</p>
                            @endif
                        </div>
                    </div>


                    <div class="p-6 md:p-8 border-b border-slate-100 dark:border-slate-800 student-fee-field"
                        id="student-info" style="display: none;">
                        <div>
                            <h3
                                class="text-lg font-semibold text-[#1F2937] dark:text-[#F9FAFB] mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#2563EB] dark:text-[#60A5FA]">
                                    person_search
                                </span>
                                Student Information
                            </h3>
                            <div class="max-w-xl">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                    Select Student
                                </label>
                                <div class="relative group">
                                    <select name="student_id" id="student_id" class="w-full select2-ajax">
                                        @if (old('student_id'))
                                            @php
                                                $oldStudent = \App\Models\StudentBasicInfo::find(old('student_id'));
                                            @endphp
                                            @if ($oldStudent)
                                                <option value="{{ $oldStudent->id }}" selected>
                                                    {{ $oldStudent->first_name }} {{ $oldStudent->last_name }}
                                                    ({{ $oldStudent->id_no }})
                                                </option>
                                            @endif
                                        @else
                                            <option value="">Search by name or Student ID...</option>
                                        @endif
                                    </select>
                                </div>
                                @if ($errors->has('student'))
                                    <p class="text-red-500 text-xs mt-1">{{ $errors->first('student') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- subject_id  -->
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Subject / Subject
                                </label>
                                <div class="relative">
                                    <select name="subject_id" id="subject_id"
                                        class="w-full pl-4 pr-10  appearance-none bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-[#2563EB]/20 dark:focus:ring-[#60A5FA]/20 focus:border-[#2563EB] dark:focus:border-[#60A5FA] text-[#1F2937] dark:text-[#F9FAFB]">
                                        <option value="">Select Subject</option>
                                        @foreach ($subjects as $id => $entry)
                                            <option value="{{ $id }}" {{ old('subject_id') == $id ? 'selected' : '' }}>
                                                {{ $entry }}
                                            </option>
                                        @endforeach
                                    </select>
                                    {{-- <div
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                        <span class="material-symbols-outlined">expand_more</span>
                                    </div> --}}
                                </div>
                            </div>
                            <!-- academic_background -->
                            <div class="space-y-1.5 ">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Academic Background
                                </label>
                                <input name="academic_background" id="academic_background"
                                    class="w-full px-4  bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-[#2563EB]/20 dark:focus:ring-[#60A5FA]/20 focus:border-[#2563EB] dark:focus:border-[#60A5FA] text-[#1F2937] dark:text-[#F9FAFB] mt-0"
                                    placeholder="e.g. Science / Arts / Business" type="text"
                                    value="{{ old('academic_background', '') }}" />
                            </div>
                            <!-- exam_year -->
                            <div class="space-y-1.5 ">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Exam Year
                                </label>
                                <input name="exam_year" id="exam_year"
                                    class="w-full px-4  bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-[#2563EB]/20 dark:focus:ring-[#60A5FA]/20 focus:border-[#2563EB] dark:focus:border-[#60A5FA] text-[#1F2937] dark:text-[#F9FAFB]"
                                    placeholder="e.g. 2020, 2021 or 2022" type="text" value="{{ old('exam_year', '') }}" />
                            </div>
                        </div>
                    </div>




                    <div
                        class="p-6 md:p-8 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/20">
                        <h3 class="text-lg font-semibold text-[#1F2937] dark:text-[#F9FAFB] mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#2563EB] dark:text-[#60A5FA]">
                                receipt_long
                            </span>
                            Transaction Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Earning
                                    Date</label>
                                <div class="relative">
                                    <input name="earning_date" id="earning_date" required
                                        class="w-full pl-10 pr-4  bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-[#2563EB]/20 dark:focus:ring-[#60A5FA]/20 focus:border-[#2563EB] dark:focus:border-[#60A5FA] text-[#1F2937] dark:text-[#F9FAFB] {{ $errors->has('earning_date') ? 'border-red-500' : '' }}"
                                        type="date" value="{{ old('earning_date', date('Y-m-d')) }}" />
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <span class="material-symbols-outlined text-xl">calendar_today</span>
                                    </div>
                                    @if ($errors->has('earning_date'))
                                        <p class="text-red-500 text-xs mt-1">{{ $errors->first('earning_date') }}</p>
                                    @endif
                                </div>
                                <input type="hidden" name="earning_month" id="earning_month"
                                    value="{{ old('earning_month', date('n')) }}">
                                <input type="hidden" name="earning_year" id="earning_year"
                                    value="{{ old('earning_year', date('Y')) }}">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Amount</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-500 font-semibold symbol-of-tk">৳</span>
                                    </div>
                                    <input name="amount" id="amount" required
                                        class="w-full pl-8 pr-4  bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-[#2563EB]/20 dark:focus:ring-[#60A5FA]/20 focus:border-[#2563EB] dark:focus:border-[#60A5FA] text-[#1F2937] dark:text-[#F9FAFB] font-mono {{ $errors->has('amount') ? 'border-red-500' : '' }}"
                                        placeholder="0.00" step="0.01" type="number" value="{{ old('amount', '') }}" />
                                </div>
                                @if ($errors->has('amount'))
                                    <p class="text-red-500 text-xs mt-1">{{ $errors->first('amount') }}</p>
                                @endif
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Receipt
                                    Number</label>
                                <div class="relative">
                                    <input name="earning_reference" id="earning_reference"
                                        class="w-full pl-4 pr-10  bg-slate-100 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-500 dark:text-slate-400 font-mono"
                                        type="text" value="{{ old('earning_reference', $receipt_numbers) }}" />

                                    <div
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-green-500 dark:text-green-400">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 md:p-8">
                        <h3 class="text-lg font-semibold text-[#1F2937] dark:text-[#F9FAFB] mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#2563EB] dark:text-[#60A5FA]">payments</span>
                            Payment Confirmation
                        </h3>
                        <div class="space-y-8">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Payment Method
                                </label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <label class="relative cursor-pointer">
                                        <input checked="" class="peer sr-only" name="payment_method" type="radio"
                                            value="cash" />
                                        <div class="flex flex-col items-center justify-center 
                                                                                            p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white 
                                                                                            dark:bg-[#111827] peer-checked:border-[#2563EB] 
                                                                                            dark:peer-checked:border-[#60A5FA] peer-checked:bg-blue-50/50 
                                                                                            dark:peer-checked:bg-blue-900/20 peer-checked:text-[#2563EB] 
                                                                                            dark:peer-checked:text-[#60A5FA] transition-all hover:bg-slate-50 dark:hover:bg-slate-800
                                                                                            text-slate-500">
                                            <span class="material-symbols-outlined mb-1">attach_money</span>
                                            <span class="text-sm font-medium">Cash</span>
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer">
                                        <input class="peer sr-only" name="payment_method" type="radio" value="bank" />
                                        <div
                                            class="text-slate-500 flex flex-col items-center justify-center p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#111827] peer-checked:border-[#2563EB] dark:peer-checked:border-[#60A5FA] peer-checked:bg-blue-50/50 dark:peer-checked:bg-blue-900/20 peer-checked:text-[#2563EB] dark:peer-checked:text-[#60A5FA] transition-all hover:bg-slate-50 dark:hover:bg-slate-800">
                                            <span class="material-symbols-outlined mb-1">account_balance</span>
                                            <span class="text-sm font-medium">Bank Transfer</span>
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer">
                                        <input class="peer sr-only" name="payment_method" type="radio"
                                            value="mobile_banking" />
                                        <div
                                            class="text-slate-500 flex flex-col items-center justify-center p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#111827] peer-checked:border-[#2563EB] dark:peer-checked:border-[#60A5FA] peer-checked:bg-blue-50/50 dark:peer-checked:bg-blue-900/20 peer-checked:text-[#2563EB] dark:peer-checked:text-[#60A5FA] transition-all hover:bg-slate-50 dark:hover:bg-slate-800">
                                            <span class="material-symbols-outlined mb-1">account_balance_wallet</span>
                                            <span class="text-sm font-medium">Mobile Banking</span>
                                        </div>
                                    </label>
                                    <!-- <label class="relative cursor-pointer">
                                                                                                                                                                                                    <input class="peer sr-only" name="payment_method" type="radio"
                                                                                                                                                                                                        value="check" />
                                                                                                                                                                                                    <div
                                                                                                                                                                                                        class="flex flex-col items-center justify-center p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#111827] peer-checked:border-[#2563EB] dark:peer-checked:border-[#60A5FA] peer-checked:bg-blue-50/50 dark:peer-checked:bg-blue-900/20 peer-checked:text-[#2563EB] dark:peer-checked:text-[#60A5FA] transition-all hover:bg-slate-50 dark:hover:bg-slate-800">
                                                                                                                                                                                                        <span class="material-symbols-outlined mb-1">check_circle</span>
                                                                                                                                                                                                        <span class="text-sm font-medium">Check</span>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </label> -->
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Paid
                                        By (Payer Name)</label>
                                    <input name="paid_by" id="paid_by"
                                        class="w-full px-4  bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-[#2563EB]/20 dark:focus:ring-[#60A5FA]/20 focus:border-[#2563EB] dark:focus:border-[#60A5FA] text-[#1F2937] dark:text-[#F9FAFB]"
                                        placeholder="e.g. Parent Name or Student Name" type="text"
                                        value="{{ old('paid_by', '') }}" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                        Received By
                                    </label>
                                    <div
                                        class="flex items-center gap-3 px-4 py-2.5 bg-slate-100 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg">
                                        @if (auth()->user()->photo)
                                            <img src="{{ auth()->user()->photo->getUrl('thumb') }}"
                                                class="size-6 rounded-full object-cover">
                                        @else
                                            <div class="size-6 rounded-full bg-slate-200 flex items-center justify-center">
                                                <span class="material-symbols-outlined text-xs">person</span>
                                            </div>
                                        @endif
                                        <span
                                            class="text-sm text-slate-700 dark:text-slate-300 font-medium">{{ auth()->user()->name }}
                                            (You)</span>
                                        <input type="hidden" name="recieved_by" value="{{ auth()->user()->name }}">
                                        <span class="ml-auto material-symbols-outlined text-slate-400 text-sm">lock</span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Payment
                                    Proof Details</label>
                                <textarea name="payment_proof_details" id="payment_proof_details"
                                    class="w-full px-4 py-3 bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-[#2563EB]/20 dark:focus:ring-[#60A5FA]/20 focus:border-[#2563EB] dark:focus:border-[#60A5FA] text-[#1F2937] dark:text-[#F9FAFB] placeholder-slate-400"
                                    placeholder="Enter bank transaction ID, check number, or any other relevant reference details..."
                                    rows="3">{{ old('payment_proof_details', '') }}</textarea>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Payment
                                    Proof (Uploads)</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-700 border-dashed rounded-xl bg-slate-50 dark:bg-[#111827] hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer group dz-clickable"
                                    id="payment-proof-dropzone">
                                    <div class="dz-message space-y-1 text-center pointer-events-none">
                                        <span
                                            class="material-symbols-outlined text-4xl text-slate-400 group-hover:text-[#2563EB] dark:group-hover:text-[#60A5FA]">upload_file</span>
                                        <div class="flex text-sm text-slate-600 dark:text-slate-400">
                                            <span
                                                class="relative rounded-md font-medium text-[#2563EB] dark:text-[#60A5FA]">
                                                Click to upload files or drag and drop
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-500">PNG, JPG, PDF up to 10MB each
                                            (Multiple files supported)</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-4"
                                    id="dropzone-previews">
                                    {{-- Dropzone previews will appear here --}}
                                </div>

                                {{-- Custom Dropzone Preview Template --}}
                                <div id="dropzone-template" style="display: none;">
                                    <div
                                        class="dz-preview dz-file-preview relative group bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2 transition-all hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-500/50">
                                        <div
                                            class="relative aspect-square rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-900 flex items-center justify-center mb-2">
                                            <img data-dz-thumbnail class="w-full h-full object-cover" />
                                            <div
                                                class="dz-error-mark absolute inset-0 flex items-center justify-center bg-red-500/10 opacity-0 transition-opacity">
                                                <span class="material-symbols-outlined text-red-500 text-3xl">error</span>
                                            </div>
                                            <div
                                                class="dz-success-mark absolute inset-0 flex items-center justify-center bg-green-500/10 opacity-0 transition-opacity">
                                                <span
                                                    class="material-symbols-outlined text-green-500 text-3xl">check_circle</span>
                                            </div>
                                            {{-- PDF Overlay Icon (shown if thumbnail is not available or it's a PDF) --}}
                                            <div
                                                class="pdf-icon-overlay absolute inset-0 flex items-center justify-center bg-slate-50 dark:bg-slate-900 hidden">
                                                <span
                                                    class="material-symbols-outlined text-red-500 text-4xl">picture_as_pdf</span>
                                            </div>
                                        </div>

                                        <div class="px-1">
                                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate"
                                                data-dz-name></p>
                                            <p class="text-[10px] text-slate-500 dark:text-slate-400" data-dz-size></p>
                                        </div>

                                        <div
                                            class="dz-progress absolute bottom-0 left-0 right-0 h-1 bg-slate-100 dark:bg-slate-700 overflow-hidden rounded-b-xl">
                                            <span class="dz-upload block h-full bg-blue-500 transition-all duration-300"
                                                data-dz-uploadprogress style="width: 0%"></span>
                                        </div>

                                        <button type="button" data-dz-remove
                                            class="absolute -top-2 -right-2 w-7 h-7 bg-red-500 text-white rounded-full flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transition-all hover:bg-red-600 scale-75 group-hover:scale-100">
                                            <span class="material-symbols-outlined text-sm">close</span>
                                        </button>

                                        <div class="dz-error-message mt-1 text-[10px] text-red-500 hidden"
                                            data-dz-errormessage></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="p-6 md:p-8 bg-slate-50 dark:bg-slate-900/40 border-t border-slate-200 dark:border-slate-800 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                        <button
                            class="px-6 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 text-[#1F2937] dark:text-[#F9FAFB] font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors w-full sm:w-auto"
                            type="button">
                            Cancel
                        </button>
                        <button
                            class="px-6 py-2.5 rounded-lg bg-[#2563EB] dark:bg-[#60A5FA] hover:bg-[#2563EB]/90 dark:hover:bg-[#60A5FA]/90 text-white dark:text-[#111827] font-medium shadow-md shadow-blue-500/20 flex items-center justify-center gap-2 transition-colors w-full sm:w-auto"
                            type="submit">
                            <span class="material-symbols-outlined text-xl">save</span>
                            Record Payment
                        </button>
                    </div>
                </form>
                <div class="h-8"></div>
            </div>
        </div>
    </main>
@endsection

@section('styles')
    <style>
        /* Dropzone State Handling */
        .dz-preview.dz-success .dz-success-mark {
            opacity: 1 !important;
            transform: scale(1);
        }

        .dz-preview.dz-error .dz-error-mark {
            opacity: 1 !important;
            transform: scale(1);
        }

        .dz-preview.dz-error .dz-error-message {
            display: block !important;
        }

        .dz-preview.dz-processing .dz-progress {
            opacity: 1;
            visibility: visible;
        }

        .dz-preview.dz-complete .dz-progress {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s;
        }

        /* Thumbnail fix for non-image files */
        .dz-preview.dz-file-preview [data-dz-thumbnail] {
            display: none;
        }

        .dz-preview.dz-image-preview .pdf-icon-overlay {
            display: none;
        }
    </style>
@endsection

@section('scripts')
    <script>
        Dropzone.autoDiscover = false;

        $(document).ready(function () {
            // Enhanced Select2 Regular
            $('.select2').select2({
                width: '100%',
                placeholder: 'Please select',
                allowClear: true
            });

            // Enhanced Select2 AJAX for Students
            $('.select2-ajax').select2({
                width: '100%',
                placeholder: 'Search by name or Student ID (min 3 chars)...',
                allowClear: true,
                minimumInputLength: 3,
                ajax: {
                    url: '{{ route('admin.students.search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

            // Initialize CKEditor
            var allEditors = document.querySelectorAll('.ckeditor');
            for (var i = 0; i < allEditors.length; ++i) {
                ClassicEditor.create(allEditors[i]).then(editor => {
                    const editable = editor.editing.view.document.getRoot();
                    editor.editing.view.change(writer => {
                        writer.setStyle('color', '#1e293b', editable);
                    });
                });
            }

            // Conditional fields based on earning category
            function toggleStudentFeeFields() {
                const selectedOption = $('#earning_category_id').find('option:selected');
                const isStudentConnected = Number(selectedOption.data('student-connected')) === 1;

                if (isStudentConnected) {
                    $('.student-fee-field').slideDown(300);
                } else {
                    $('.student-fee-field').slideUp(300);
                }
            }

            toggleStudentFeeFields();
            $('#earning_category_id').on('change', toggleStudentFeeFields);

            // Auto-calculate month and year from earning date
            $('#earning_date').on('change', function () {
                const dateValue = $(this).val();
                if (dateValue) {
                    const date = new Date(dateValue);
                    if (!isNaN(date.getTime())) {
                        $('#earning_month').val(date.getMonth() + 1);
                        $('#earning_year').val(date.getFullYear());
                    }
                }
            });

            // Dropzone Initialization
            if ($('#payment-proof-dropzone').length > 0) {
                var uploadedPaymentProofMap = {}
                var myDropzone = new Dropzone("#payment-proof-dropzone", {
                    url: '{{ route('admin.earnings.storeMedia') }}',
                    maxFilesize: 10,
                    acceptedFiles: '.jpeg,.jpg,.png,.gif,.pdf',
                    addRemoveLinks: true,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    params: {
                        size: 10
                    },
                    previewsContainer: "#dropzone-previews",
                    previewTemplate: document.querySelector('#dropzone-template').innerHTML,
                    clickable: true,
                    success: function (file, response) {
                        $('form').append('<input type="hidden" name="payment_proof[]" value="' +
                            response.name + '">')
                        uploadedPaymentProofMap[file.name] = response.name
                        file.previewElement.classList.add('dz-success');
                    },
                    removedfile: function (file) {
                        file.previewElement.remove()
                        var name = ''
                        if (typeof file.file_name !== 'undefined') {
                            name = file.file_name
                        } else {
                            name = uploadedPaymentProofMap[file.name]
                        }
                        $('form').find('input[name="payment_proof[]"][value="' + name + '"]').remove()
                    },
                    init: function () {
                        // Handle custom logic when a file is added
                        this.on("addedfile", function (file) {
                            if (file.type === 'application/pdf' || file.name.toLowerCase()
                                .endsWith('.pdf')) {
                                setTimeout(() => {
                                    const overlay = file.previewElement.querySelector(
                                        '.pdf-icon-overlay');
                                    if (overlay) overlay.classList.remove('hidden');
                                }, 10);
                            }
                        });

                        @if (isset($earning) && $earning->payment_proof)
                            var files = {!! json_encode($earning->payment_proof) !!}
                            for (var i in files) {
                                var file = files[i]
                                this.options.addedfile.call(this, file)
                                this.options.thumbnail.call(this, file, file.preview ?? file
                                    .preview_url)
                                file.previewElement.classList.add('dz-complete')
                                $('form').append('<input type="hidden" name="payment_proof[]" value="' +
                                    file.file_name + '">')

                                if (file.file_name.toLowerCase().endsWith('.pdf')) {
                                    const overlay = file.previewElement.querySelector(
                                        '.pdf-icon-overlay');
                                    if (overlay) overlay.classList.remove('hidden');
                                }
                            }
                        @endif
                                                                    },
                    error: function (file, response) {
                        if ($.type(response) === 'string') {
                            var message = response
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
                });
            }
        });
    </script>
@endsection
