<?php

use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\StudentBasicInfoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
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
    // Ajax Route for Monthly Revenue Breakdown

    Route::get('/monthly-revenue/{months}', [HomeController::class, 'getMonthLyRevenueBreakdown'])->name('monthly.revenue');



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
    Route::get('batches/{batch}/assign-students', [BatchController::class, 'assignStudents'])->name('batches.assignStudents');
    Route::post('batches/{batch}/assign-students', [BatchController::class, 'storeAssignedStudents'])->name('batches.assignStudents.store');
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

    // Teachers Payment
    Route::delete('teachers-payments/destroy', 'TeachersPaymentController@massDestroy')->name('teachers-payments.massDestroy');
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
    Route::get('due-collections/student-dues/{studentId}', 'DueCollectionController@getStudentDues')->name('due-collections.student-dues');
    Route::post('due-collections/pay', 'DueCollectionController@payDue')->name('due-collections.pay');
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
