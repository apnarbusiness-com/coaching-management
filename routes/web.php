<?php

use App\Http\Controllers\Admin\AdmissionApplicationsController;
use App\Http\Controllers\Admin\BatchAttendanceController;
use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\DueCollectionController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\StudentBasicInfoController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TeachersPaymentController;
use App\Http\Controllers\AdmissionApplicationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
Route::get('/admission', [AdmissionApplicationController::class, 'create'])->name('admission.public');
Route::post('/admission', [AdmissionApplicationController::class, 'store'])->name('admission.public.store');
Route::get('/admission/thank-you/{application}', [AdmissionApplicationController::class, 'thankYou'])->name('admission.public.thankyou');
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
});

Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('student/profile', 'HomeController@studentProfile')->name('student.profile');
    Route::get('student/batches', 'HomeController@myBatches')->name('student.myBatches');
    Route::get('teacher/profile', 'HomeController@teacherProfile')->name('teacher.profile');
    Route::get('teacher/my-id-card', 'HomeController@myIdCard')->name('teacher.myIdCard');
    
    // Ajax Route for Monthly Revenue Breakdown

    Route::get('/monthly-revenue/{months}', [HomeController::class, 'getMonthLyRevenueBreakdown'])->name('monthly.revenue');

    // Dashboard Widget Configuration
    Route::get('dashboard-widgets', 'DashboardWidgetConfigController@index')->name('dashboard-widgets.index');
    Route::get('dashboard-widgets/role/{role}/edit', 'DashboardWidgetConfigController@edit')->name('dashboard-widgets.edit');
    Route::put('dashboard-widgets/role/{role}', 'DashboardWidgetConfigController@update')->name('dashboard-widgets.update');



    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::post('users/{user}/send-credentials', 'UsersController@sendCredentials')->name('users.sendCredentials');
    Route::resource('users', 'UsersController');

    // Audit Logs
    Route::resource('audit-logs', 'AuditLogsController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

    // Section
    Route::delete('sections/destroy', 'SectionController@massDestroy')->name('sections.massDestroy');
    Route::post('sections/parse-csv-import', 'SectionController@parseCsvImport')->name('sections.parseCsvImport');
    Route::post('sections/process-csv-import', 'SectionController@processCsvImport')->name('sections.processCsvImport');
    Route::resource('sections', 'SectionController');

    // Shift
    Route::delete('shifts/destroy', 'ShiftController@massDestroy')->name('shifts.massDestroy');
    Route::post('shifts/parse-csv-import', 'ShiftController@parseCsvImport')->name('shifts.parseCsvImport');
    Route::post('shifts/process-csv-import', 'ShiftController@processCsvImport')->name('shifts.processCsvImport');
    Route::resource('shifts', 'ShiftController');

    // Academic Class
    Route::delete('academic-classes/destroy', 'AcademicClassController@massDestroy')->name('academic-classes.massDestroy');
    Route::post('academic-classes/parse-csv-import', 'AcademicClassController@parseCsvImport')->name('academic-classes.parseCsvImport');
    Route::post('academic-classes/process-csv-import', 'AcademicClassController@processCsvImport')->name('academic-classes.processCsvImport');
    Route::resource('academic-classes', 'AcademicClassController');

    // Batch
    Route::get('batches/{batch}/manage', [BatchController::class, 'manage'])->name('batches.manage');
    Route::post('batches/{batch}/quick-enroll', [BatchController::class, 'quickEnrollStudents'])->name('batches.quickEnroll');
    Route::post('batches/{batch}/quick-enroll-ajax', [BatchController::class, 'quickEnrollStudentsAjax'])->name('batches.quickEnrollAjax');
    Route::get('batches/{batch}/enrolled-students-ajax', [BatchController::class, 'getEnrolledStudentsAjax'])->name('batches.getEnrolledStudentsAjax');
    Route::delete('batches/{batch}/un-enroll-ajax/{student}', [BatchController::class, 'unEnrollStudentAjax'])->name('batches.unEnrollAjax');
    Route::delete('batches/{batch}/un-enroll/{student}', [BatchController::class, 'unEnrollStudent'])->name('batches.unEnroll');
    Route::get('batches/{batch}/assign-students', [BatchController::class, 'assignStudents'])->name('batches.assignStudents');
    Route::post('batches/{batch}/assign-students', [BatchController::class, 'storeAssignedStudents'])->name('batches.assignStudents.store');
    Route::post('batches/{batch}/assign-students/copy-previous', [BatchController::class, 'copyPreviousMonthEnrollments'])->name('batches.assignStudents.copyPrevious');
    Route::post('batches/assign-students/copy-previous-all', [BatchController::class, 'copyPreviousMonthEnrollmentsAll'])->name('batches.assignStudents.copyPreviousAll');
    Route::get('batches/{batch}/assign-teachers', [BatchController::class, 'assignTeachers'])->name('batches.assignTeachers');
    Route::post('batches/{batch}/assign-teachers', [BatchController::class, 'storeAssignedTeacher'])->name('batches.assignTeachers.store');
    Route::delete('batches/{batch}/assign-teachers/{teacher}', [BatchController::class, 'removeAssignedTeacher'])->name('batches.assignTeachers.remove');
    Route::delete('batches/destroy', 'BatchController@massDestroy')->name('batches.massDestroy');
    Route::resource('batches', 'BatchController');

    // Student Basic Info
    Route::delete('student-basic-infos/destroy', 'StudentBasicInfoController@massDestroy')->name('student-basic-infos.massDestroy');
    Route::post('student-basic-infos/media', 'StudentBasicInfoController@storeMedia')->name('student-basic-infos.storeMedia');
    Route::post('student-basic-infos/ckmedia', 'StudentBasicInfoController@storeCKEditorImages')->name('student-basic-infos.storeCKEditorImages');
    Route::post('student-basic-infos/parse-csv-import', [StudentBasicInfoController::class, 'parseStudentImport'])->name('student-basic-infos.parseStudentImport');
    Route::post('student-basic-infos/process-csv-import', [StudentBasicInfoController::class, 'processStudentImport'])->name('student-basic-infos.processStudentImport');
    Route::post('student-basic-infos/import-raw', [StudentBasicInfoController::class, 'importRawToTable'])->name('student-basic-infos.importRawToTable');
    Route::post('student-basic-infos/process-raw', [StudentBasicInfoController::class, 'processRawToStudents'])->name('student-basic-infos.processRawToStudents');
    Route::get('student-basic-infos/raw-imports', [StudentBasicInfoController::class, 'rawImports'])->name('student-basic-infos.rawImports');
    Route::delete('student-basic-infos/raw-imports/{studentImportRaw}', [StudentBasicInfoController::class, 'deleteRawImportRow'])->name('student-basic-infos.rawImports.delete');
    Route::post('student-basic-infos/raw-imports/reset', [StudentBasicInfoController::class, 'resetRawImports'])->name('student-basic-infos.rawImports.reset');
    Route::get('student-basic-infos/demo-csv', [StudentBasicInfoController::class, 'downloadDemoCsv'])->name('student-basic-infos.demoCsv');
    Route::get('student-basic-infos/print-id-card/{id}', [StudentBasicInfoController::class, 'printIdCard'])->name('student-basic-infos.printIdCard');
    Route::post('student-basic-infos/{studentBasicInfo}/sync-subjects', 'StudentBasicInfoController@syncSubjects')->name('student-basic-infos.syncSubjects');
    Route::resource('student-basic-infos', 'StudentBasicInfoController');

    // Admission Applications
    Route::get('admission-applications', [AdmissionApplicationsController::class, 'index'])->name('admission-applications.index');
    Route::get('admission-applications/{application}', [AdmissionApplicationsController::class, 'show'])->name('admission-applications.show');
    Route::post('admission-applications/{application}/approve', [AdmissionApplicationsController::class, 'approve'])->name('admission-applications.approve');
    Route::delete('admission-applications/{application}', [AdmissionApplicationsController::class, 'destroy'])->name('admission-applications.destroy');

    // Student Details Information
    Route::delete('student-details-informations/destroy', 'StudentDetailsInformationController@massDestroy')->name('student-details-informations.massDestroy');
    Route::post('student-details-informations/parse-csv-import', 'StudentDetailsInformationController@parseCsvImport')->name('student-details-informations.parseCsvImport');
    Route::post('student-details-informations/process-csv-import', 'StudentDetailsInformationController@processCsvImport')->name('student-details-informations.processCsvImport');
    Route::resource('student-details-informations', 'StudentDetailsInformationController');

    // Expense Categories
    Route::delete('expense-categories/destroy', 'ExpenseCategoriesController@massDestroy')->name('expense-categories.massDestroy');
    Route::resource('expense-categories', 'ExpenseCategoriesController');

    // Earning Categories
    Route::delete('earning-categories/destroy', 'EarningCategoriesController@massDestroy')->name('earning-categories.massDestroy');
    Route::resource('earning-categories', 'EarningCategoriesController');

    // Expenses
    Route::delete('expenses/destroy', 'ExpensesController@massDestroy')->name('expenses.massDestroy');
    Route::post('expenses/media', 'ExpensesController@storeMedia')->name('expenses.storeMedia');
    Route::post('expenses/ckmedia', 'ExpensesController@storeCKEditorImages')->name('expenses.storeCKEditorImages');
    Route::get('expenses/summary', 'ExpensesController@summary')->name('expenses.summary');
    Route::get('expenses/demo-csv', 'ExpensesController@downloadDemoCsv')->name('expenses.demoCsv');
    Route::post('expenses/import', 'ExpensesController@importExcel')->name('expenses.import');
    Route::resource('expenses', 'ExpensesController');

    // Teacher
    Route::get('teachers/{id}/id-card', [TeacherController::class,'idCard'])->name('teachers.idCard');
    Route::post('teachers/{teacher}/toggle-status', [TeacherController::class, 'toggleStatus'])->name('teachers.toggleStatus');
    Route::delete('teachers/destroy', 'TeacherController@massDestroy')->name('teachers.massDestroy');
    Route::post('teachers/media', 'TeacherController@storeMedia')->name('teachers.storeMedia');
    Route::post('teachers/ckmedia', 'TeacherController@storeCKEditorImages')->name('teachers.storeCKEditorImages');
    Route::resource('teachers', 'TeacherController');

    // Subjects
    Route::delete('subjects/destroy', 'SubjectsController@massDestroy')->name('subjects.massDestroy');
    Route::resource('subjects', 'SubjectsController');

    // Academic Backgrounds
    Route::delete('academic-backgrounds/destroy', 'AcademicBackgroundsController@massDestroy')->name('academic-backgrounds.massDestroy');
    Route::resource('academic-backgrounds', 'AcademicBackgroundsController');

    // Class Rooms
    Route::delete('class-rooms/destroy', 'ClassRoomsController@massDestroy')->name('class-rooms.massDestroy');
    Route::resource('class-rooms', 'ClassRoomsController');

    // Teachers Payment
    Route::delete('teachers-payments/destroy', 'TeachersPaymentController@massDestroy')->name('teachers-payments.massDestroy');
    Route::post('teachers-payments/generate', [TeachersPaymentController::class, 'generate'])->name('teachers-payments.generate');
    Route::post('teachers-payments/calculate', 'TeachersPaymentController@calculate')->name('teachers-payments.calculate');
    Route::post('teachers-payments/{teachersPayment}/transactions', 'TeachersPaymentController@storeTransaction')->name('teachers-payments.transactions.store');
    Route::delete('teachers-payments/{teachersPayment}/transactions/{transaction}', 'TeachersPaymentController@destroyTransaction')->name('teachers-payments.transactions.destroy');
    Route::resource('teachers-payments', 'TeachersPaymentController');

    // Earnings
    Route::get('students/search', 'EarningsController@getStudents')->name('students.search');
    Route::delete('earnings/destroy', 'EarningsController@massDestroy')->name('earnings.massDestroy');
    Route::post('earnings/media', 'EarningsController@storeMedia')->name('earnings.storeMedia');
    Route::post('earnings/ckmedia', 'EarningsController@storeCKEditorImages')->name('earnings.storeCKEditorImages');
    Route::get('earnings/summary', 'EarningsController@summary')->name('earnings.summary');
    Route::get('earnings/demo-csv', 'EarningsController@downloadDemoCsv')->name('earnings.demoCsv');
    Route::post('earnings/import', 'EarningsController@importExcel')->name('earnings.import');
    Route::resource('earnings', 'EarningsController');

    // Due Collections
    Route::get('due-collections', 'DueCollectionController@index')->name('due-collections.index');
    Route::post('due-collections/generate', 'DueCollectionController@generateDues')->name('due-collections.generate');
    Route::get('due-collections/students', 'DueCollectionController@getStudentList')->name('due-collections.students');
    Route::get('due-collections/student-dues/{studentId}',[DueCollectionController::class, 'getStudentDues'])->name('due-collections.student-dues');
    Route::post('due-collections/pay', 'DueCollectionController@payDue')->name('due-collections.pay');
    Route::post('due-collections/pay-all', 'DueCollectionController@payAllDues')->name('due-collections.payAll');

    // Due Checker
    Route::get('due-collections/checker', 'DueCollectionController@checker')->name('due-collections.checker');
    Route::get('due-collections/checker/search', 'DueCollectionController@searchStudentsForChecker')->name('due-collections.checker.search');
    Route::get('due-collections/checker/student/{studentId}', 'DueCollectionController@getStudentFullHistory')->name('due-collections.checker.student');

    // Batch Attendance
    Route::get('batch-attendances', [BatchAttendanceController::class, 'index'])->name('batch-attendances.index');
    Route::get('batch-attendances/calendar', [BatchAttendanceController::class, 'calendar'])->name('batch-attendances.calendar');
    Route::get('batch-attendances/{batchId}/take', [BatchAttendanceController::class, 'showAttendanceForm'])->name('batch-attendances.take');
    Route::post('batch-attendances/{batchId}/take', [BatchAttendanceController::class, 'store'])->name('batch-attendances.store');
    Route::get('batch-attendances/{batchId}/report', [BatchAttendanceController::class, 'getReport'])->name('batch-attendances.report');
    Route::get('batch-attendances/{batchId}/students/{studentId}/due-summary', [BatchAttendanceController::class, 'getStudentDueSummary'])->name('batch-attendances.dueSummary');
});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
});
