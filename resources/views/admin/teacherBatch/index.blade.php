@extends('layouts.admin')
@section('content')
    <div class="max-w-7xl mx-auto p-6 flex flex-col gap-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col gap-1">
                <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-100">{{ trans('cruds.teacherBatch.title') }}</h1>
                <p class="text-slate-600 dark:text-slate-400 text-sm">
                    View all teacher batch assignments
                </p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm p-4">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex flex-col gap-1.5 min-w-[140px]">
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 ml-1">Month</label>
                    <select id="filterMonth"
                        class="custom-select-arrow w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none appearance-none">
                        @php $months = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December']; @endphp
                        @foreach ($months as $num => $name)
                            <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1.5 min-w-[100px]">
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 ml-1">Year</label>
                    <select id="filterYear"
                        class="custom-select-arrow w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none appearance-none">
                        @php $currentYear = now()->year; $years = range($currentYear - 1, $currentYear + 1); @endphp
                        @foreach ($years as $yr)
                            <option value="{{ $yr }}" {{ $year == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1.5 min-w-[200px]">
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 ml-1">{{ trans('cruds.batch.title') }}</label>
                    <select id="filterBatch"
                        class="custom-select-arrow w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none appearance-none">
                        <option value="">All Batches</option>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch->id }}">{{ $batch->batch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1.5 min-w-[200px]">
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 ml-1">{{ trans('cruds.teacher.title') }}</label>
                    <select id="filterTeacher"
                        class="custom-select-arrow w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none appearance-none">
                        <option value="">All Teachers</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->emloyee_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center">
                    <button type="button" id="applyFilter"
                        class="px-5 py-2.5 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors">
                        <span class="material-symbols-outlined text-lg mr-1">filter_list</span>
                        Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Results Info -->
        <div id="resultsInfo" class="rounded-lg bg-primary/10 text-primary px-4 py-3 text-sm flex items-center justify-between">
            <span id="resultsCount">Loading...</span>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700 dark:text-slate-200">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 uppercase tracking-wider font-semibold">
                            <th class="px-6 py-4">{{ trans('cruds.batch.title') }}</th>
                            <th class="px-6 py-4">{{ trans('cruds.teacher.title') }}</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4">Salary Amount</th>
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4 text-right">Batch Expected</th>
                            <th class="px-6 py-4 text-right">Teacher Salary</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-slate-100 dark:divide-slate-700">
                        <tr>
                            <td class="px-6 py-8 text-center text-slate-500" colspan="8">
                                Loading...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        const routes = {
            filter: '{{ route("admin.teacher-batch.filter") }}'
        };

        function fetchData() {
            const month = document.getElementById('filterMonth').value;
            const year = document.getElementById('filterYear').value;
            const batchId = document.getElementById('filterBatch').value;
            const teacherId = document.getElementById('filterTeacher').value;

            const params = new URLSearchParams({
                month: month,
                year: year,
                batch_id: batchId || '',
                teacher_id: teacherId || ''
            });

            fetch(routes.filter + '?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    document.getElementById('resultsCount').textContent = 'Showing ' + data.data.length + ' result(s) - ' + getMonthName(data.month) + ' ' + data.year;
                    renderTable(data.data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('tableBody').innerHTML = '<tr><td class="px-6 py-8 text-center text-red-500" colspan="8">Error loading data</td></tr>';
                });
        }

        function renderTable(data) {
            const tbody = document.getElementById('tableBody');
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td class="px-6 py-8 text-center text-slate-500" colspan="8">No teacher batch assignments found.</td></tr>';
                return;
            }

            let html = '';
            data.forEach(assignment => {
                const roleClass = assignment.role === 'primary' ? 'bg-primary/10 text-primary' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300';
                const typeClass = assignment.salary_amount_type === 'percentage' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400';
                const salaryDisplay = assignment.salary_amount_type === 'percentage' 
                    ? parseFloat(assignment.salary_amount).toFixed(2) + '%' 
                    : parseFloat(assignment.salary_amount).toFixed(2);
                const batchRevenue = parseFloat(assignment.batch_revenue || 0);
                const teacherSalary = parseFloat(assignment.teacher_salary || 0);

                html += `
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-900 dark:text-slate-100">${assignment.batch_name}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-medium text-slate-900 dark:text-slate-100">${assignment.teacher_name}</span>
                                <span class="text-xs text-slate-500">${assignment.emloyee_code}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-lg text-xs font-medium ${roleClass}">${assignment.role.charAt(0).toUpperCase() + assignment.role.slice(1)}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-900 dark:text-slate-100">${salaryDisplay}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-lg text-xs font-medium ${typeClass}">${assignment.salary_amount_type === 'percentage' ? 'Percentage' : 'Fixed'}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-medium text-slate-900 dark:text-slate-100">${batchRevenue.toFixed(2)}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-medium text-green-600 dark:text-green-400">${teacherSalary.toFixed(2)}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="/admin/batches/${assignment.batch_id}/manage" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-medium hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                                View
                            </a>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        function getMonthName(num) {
            const months = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            return months[num] || '';
        }

        document.getElementById('applyFilter').addEventListener('click', fetchData);
        
        [document.getElementById('filterMonth'), document.getElementById('filterYear')].forEach(el => {
            el.addEventListener('change', fetchData);
        });

        document.addEventListener('DOMContentLoaded', fetchData);
    </script>
@endsection