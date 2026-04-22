<div id="sidebar"
    class="c-sidebar c-sidebar-fixed c-sidebar-lg-show bg-[#1a202c] dark:bg-slate-900 border-r border-[#282e39] dark:border-slate-800 transition-colors duration-300">

    <div class="c-sidebar-brand d-md-down-none">
        <a class="c-sidebar-brand-full h4" href="#">
            {{-- {{ trans('panel.site_title') }} --}}
            <img src="{{ asset('assets/images/logo_for_menu.svg') }}" alt="Logo" class="h-10 mx-auto">
        </a>
    </div>

    <style>
        #sidebar .c-sidebar-nav > li.c-sidebar-nav-dropdown {
            position: relative;
        }
        #sidebar .c-sidebar-nav > li.c-sidebar-nav-dropdown > .c-sidebar-nav-dropdown-items {
            display: none;
            position: absolute;
            left: 100% !important;
            top: 0 !important;
            min-width: 220px;
            background: #1a202c !important;
            border: 1px solid #282e39;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 9999 !important;
            padding: 8px 0;
            margin: 0 !important;
        }
        #sidebar .c-sidebar-nav > li.c-sidebar-nav-dropdown:hover > .c-sidebar-nav-dropdown-items,
        #sidebar .c-sidebar-nav > li.c-sidebar-nav-dropdown.hovered > .c-sidebar-nav-dropdown-items {
            display: block !important;
        }
        #sidebar .c-sidebar-nav > li.c-sidebar-nav-dropdown > .c-sidebar-nav-dropdown-items {
            display: none;
        }
        #sidebar .c-sidebar-nav > li.c-sidebar-nav-dropdown > ul.c-sidebar-nav-dropdown-items li {
            padding: 0;
        }
        #sidebar .c-sidebar-nav > li.c-sidebar-nav-dropdown > ul.c-sidebar-nav-dropdown-items a {
            padding: 10px 16px;
            color: #c4c9d4;
        }
        #sidebar .c-sidebar-nav > li.c-sidebar-nav-dropdown > ul.c-sidebar-nav-dropdown-items a:hover {
            background: #2d3748;
            color: #fff;
        }
        #sidebar .c-sidebar-nav > li.c-sidebar-nav-dropdown > ul.c-sidebar-nav-dropdown-items a.c-active {
            background: #4a5568;
            color: #fff;
        }


        .c-sidebar-nav-item {
            background: rgb(26 32 44 / var(--tw-bg-opacity, 1));
        }

        .hovered  i{
            display: none;
        }
        .c-sidebar-nav-dropdown > a >  i{
            display: block !important;
        }

        .c-show{
            background: #fff !important;
            color: #1e293b !important;
        }

        .c-show > a{
            color: #1e293b !important;
        }

        .c-show > a > i{
            display: block;
            color: #1e293b !important;
        }

        @media (max-width: 991px) {
            #sidebar .c-sidebar-nav > li.c-sidebar-nav-dropdown {
                position: static;
            }
            #sidebar .c-sidebar-nav > li.c-sidebar-nav-dropdown > .c-sidebar-nav-dropdown-items {
                display: none !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('.c-sidebar-nav-dropdown');
            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('mouseenter', function() {
                    console.log('Dropdown hovered');
                    
                    this.classList.add('hovered');
                });
                dropdown.addEventListener('mouseleave', function() {
                    console.log('Dropdown unhovered');
                    this.classList.remove('hovered');
                });
            });
        });
    </script>

    <ul class="c-sidebar-nav" style="overflow: visible  !important;">
        <li class="c-sidebar-nav-item">
            <a href="{{ route('admin.home') }}" class="c-sidebar-nav-link">
                <i class="c-sidebar-nav-icon fas fa-fw fa-tachometer-alt">

                </i>
                {{ trans('global.dashboard') }}
            </a>
        </li>
        @can('user_management_access')
            <li
                class="c-sidebar-nav-dropdown {{ request()->is('admin/permissions*') ? 'c-show' : '' }} {{ request()->is('admin/roles*') ? 'c-show' : '' }} {{ request()->is('admin/users*') ? 'c-show' : '' }} {{ request()->is('admin/audit-logs*') ? 'c-show' : '' }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-users c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.userManagement.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('permission_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.permissions.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/permissions') || request()->is('admin/permissions/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-unlock-alt c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.permission.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('role_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.roles.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/roles') || request()->is('admin/roles/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-briefcase c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.role.title') }}
                            </a>
                        </li>
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.dashboard-widgets.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/dashboard-widgets') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-th-large c-sidebar-nav-icon">

                                </i>
                                Dashboard Widgets
                            </a>
                        </li>
                    @endcan
                    @can('user_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.users.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-user c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.user.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('audit_log_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.audit-logs.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/audit-logs') || request()->is('admin/audit-logs/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-file-alt c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.auditLog.title') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @can('class_information_access')
            <li
                class="c-sidebar-nav-dropdown {{ request()->is('admin/academic-classes*') ? 'c-show' : '' }} {{ request()->is('admin/batches*') ? 'c-show' : '' }} {{ request()->is('admin/sections*') ? 'c-show' : '' }} {{ request()->is('admin/shifts*') ? 'c-show' : '' }} {{ request()->is('admin/subjects*') ? 'c-show' : '' }} {{ request()->is('admin/academic-backgrounds*') ? 'c-show' : '' }} {{ request()->is('admin/class-rooms*') ? 'c-show' : '' }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw far fa-address-book c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.classInformation.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('academic_class_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.academic-classes.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/academic-classes') || request()->is('admin/academic-classes/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-book-open c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.academicClass.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('batch_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.batches.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/batches') || request()->is('admin/batches/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-layer-group c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.batch.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('section_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.sections.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/sections') || request()->is('admin/sections/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-puzzle-piece c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.section.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('shift_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.shifts.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/shifts') || request()->is('admin/shifts/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fab fa-shirtsinbulk c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.shift.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('subject_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.subjects.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/subjects') || request()->is('admin/subjects/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-book c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.subject.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('academic_background_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.academic-backgrounds.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/academic-backgrounds') || request()->is('admin/academic-backgrounds/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-graduation-cap c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.academicBackground.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('class_room_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.class-rooms.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/class-rooms') || request()->is('admin/class-rooms/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-door-open c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.classRoom.title') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan


        @can('student_information_access')
            {{-- hello --}}
            <li
                class="c-sidebar-nav-dropdown {{ request()->is('admin/student-basic-infos*') ? 'c-show' : '' }} {{ request()->is('admin/student-details-informations*') ? 'c-show' : '' }} {{ request()->is('admin/due-collections*') ? 'c-show' : '' }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-users c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.studentInformation.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('student_basic_info_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.student-basic-infos.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/student-basic-infos') || request()->is('admin/student-basic-infos/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-user c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.studentBasicInfo.title') }}
                            </a>
                        </li>
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.admission-applications.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/admission-applications') || request()->is('admin/admission-applications/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-file-signature c-sidebar-nav-icon">

                                </i>
                                Admission Applications
                            </a>
                        </li>
                    @endcan
                    @can('due_collection_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.due-collections.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/due-collections') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-dollar-sign c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.dueCollection.title') }}
                            </a>
                        </li>
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.due-collections.checker') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/due-collections/checker') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-search c-sidebar-nav-icon">

                                </i>
                                Due Checker
                            </a>
                        </li>
                    @endcan
                    @can('student_flag_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.student-flags.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/student-flags*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-flag c-sidebar-nav-icon">

                                </i>
                                Student Flags
                            </a>
                        </li>
                    @endcan
                    @can('batch_attendance_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.batch-attendances.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/batch-attendances') || request()->is('admin/batch-attendances/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-clipboard-check c-sidebar-nav-icon">

                                </i>
                                Batch Attendance
                            </a>
                        </li>
                    @endcan
                    {{-- @can('student_details_information_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.student-details-informations.index") }}"
                                class="c-sidebar-nav-link {{ request()->is("admin/student-details-informations") || request()->is("admin/student-details-informations/*") ? "c-active" : "" }}">
                                <i class="fa-fw far fa-user-circle c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.studentDetailsInformation.title') }}
                            </a>
                        </li>
                    @endcan --}}
                </ul>
            </li>
        @endcan
        @can('teacher_sudent_access')
            <li
                class="c-sidebar-nav-dropdown {{ request()->is('admin/teachers*') ? 'c-show' : '' }} {{ request()->is('admin/teachers-payments*') ? 'c-show' : '' }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-school c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.teacherSudent.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('teacher_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.teachers.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/teachers') || request()->is('admin/teachers/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-user-tie c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.teacher.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('teachers_payment_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.teachers-payments.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/teachers-payments') || request()->is('admin/teachers-payments/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fab fa-amazon-pay c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.teachersPayment.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('teacher_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.teacher-batch.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/teacher-batch*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-chalkboard-teacher c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.teacherBatch.title') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @can('category_access')
            <li
                class="c-sidebar-nav-dropdown {{ request()->is('admin/expense-categories*') ? 'c-show' : '' }} {{ request()->is('admin/earning-categories*') ? 'c-show' : '' }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-tags c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.category.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('expense_category_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.expense-categories.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/expense-categories') || request()->is('admin/expense-categories/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-dollar-sign c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.expenseCategory.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('earning_category_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.earning-categories.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/earning-categories') || request()->is('admin/earning-categories/*') ? 'c-active' : '' }}">
                                <i class="fa-fw fas fa-hand-holding-usd c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.earningCategory.title') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @can('expense_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route('admin.expenses.index') }}"
                    class="c-sidebar-nav-link {{ request()->is('admin/expenses') || request()->is('admin/expenses/*') ? 'c-active' : '' }}">
                    <i class="fa-fw fas fa-dollar-sign c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.expense.title') }}
                </a>
            </li>
        @endcan
        @can('cash_book_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route('admin.cash-books.index') }}"
                    class="c-sidebar-nav-link {{ request()->is('admin/cash-books*') ? 'c-active' : '' }}">
                    <i class="fa-fw fas fa-wallet c-sidebar-nav-icon">

                    </i>
                    Cash Book
                </a>
            </li>
        @endcan
        @can('earning_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route('admin.earnings.index') }}"
                    class="c-sidebar-nav-link {{ request()->is('admin/earnings') || request()->is('admin/earnings/*') ? 'c-active' : '' }}">
                    <i class="fa-fw fas fa-dollar-sign c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.earning.title') }}
                </a>
            </li>
        @endcan
        @if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
            @can('profile_password_edit')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'c-active' : '' }}"
                        href="{{ route('profile.password.edit') }}">
                        <i class="fa-fw fas fa-key c-sidebar-nav-icon">
                        </i>
                        {{ trans('global.change_password') }}
                    </a>
                </li>
            @endcan
        @endif
        <li class="c-sidebar-nav-item">
            <a href="#" class="c-sidebar-nav-link"
                onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                <i class="c-sidebar-nav-icon fas fa-fw fa-sign-out-alt">

                </i>
                {{ trans('global.logout') }}
            </a>
        </li>
    </ul>

</div>
