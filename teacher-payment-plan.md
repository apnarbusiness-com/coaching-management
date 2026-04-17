# Teacher Payment System Plan (Updated)

## Overview
Real-time teacher salary calculation with two modes: Fixed and Variable (percentage), integrated with student enrollment and due calculation.

---

## Current State
- `Teacher` model has `salary_type` (fixed/variable) and `salary_amount`
- `batch_teacher` pivot table has `salary_amount` and `role`
- `TeachersPayment` model records monthly payments
- Basic `TeacherSalaryCalculationService` exists

---

## New Requirements (from task.txt)

### 1. Live Salary Calculation
- Teacher salary is calculated **automatically**, not manually generated
- "Generate Monthly Salaries" button needed to update accordingly. if any time admin thinks that payemnt needs to be re-calulate then click that button

### 2. Fixed Salary Teachers
- At enrollment time: teacher's payment record is automatically added
- Fixed teachers **cannot see** batch income

### 3. Variable/Percentage Salary Teachers
- At enrollment time: salary is calculated (percentage × batch revenue ( batch revenue = total due - total discount ) for that month)
- When any student's due is calculated → **re-calculate** all percentage-based teacher payments
- Percentage teachers **can see** batch's monthly income + any student discounts

### 4. Batch Income Visibility
| Teacher Type | Can See Batch Income |
|--------------|---------------------|
| Fixed salary | No |
| Variable/Percentage | Yes |

### 5. Attendance Display
- When taking student attendance, show student's attendance history in the same row
- Display: which days the student was present in that batch

---

## Implementation Plan

### Phase 0: http://dev-coaching-management.test/admin/batches in that rote/url or page:
' Enroll Last Month Students (All Batches)' like that i want to add one more button 'Assign Last Month Teacher (All Batches)' when click that button then it will assign last month teacher to this month batch + calculate teachr's salary for this month

**[COMPLETED]**

- Added route `POST /admin/batches/assign-teachers/copy-previous-all`
- Added `copyPreviousMonthTeachersAll()` method in BatchController
- Added button in batches index view
- Copies last month's teacher assignments to current month
- Calculates and creates teacher payments for the new month

---

### Phase 1: Enrollment-Time Salary Calculation

**[COMPLETED]**

#### When Student Enrolls in Batch
1. Check if batch has assigned teachers
2. For each teacher:
   - **Fixed type (from batch_teacher)**: Add payment record for that batch
   - **Percentage type**: Calculate percentage × batch revenue for that month

#### Files Updated
- `BatchController.php` → Added calls to `TeacherSalaryCalculationService::addEnrollmentPayment()`
- `TeacherSalaryCalculationService.php` → Added `addEnrollmentPayment()` method
- Calls added in:
  - `storeAssignedStudents()`
  - `quickEnrollStudents()`
  - `quickEnrollStudentsAjax()`
  - `copyPreviousMonthEnrollmentsAll()`

---

### Phase 2: Due Calculation → Re-calculate Percentage Salaries

**[COMPLETED]**

#### When Student Monthly Due is Calculated
1. Trigger re-calculation for all percentage-based teachers in that batch
2. Update or create their payment records for the affected month/year

#### Files Updated
- `DueCalculationService.php` → Added `TeacherSalaryCalculationService` dependency
- `generateDueForEnrollment()` now calls `recalculatePercentageSalaries()`
- `TeacherSalaryCalculationService.php` → Added `recalculatePercentageSalaries()` method

---

### Phase 3: Batch Income Visibility

**[COMPLETED]**

#### Update Batch Manage View
- Check current user's teacher profile
- If teacher is fixed-type → hide income section
- If teacher is variable/percentage → show income section

#### Files Updated
- `BatchController.php` → `manage()` method: Added `canViewIncome` flag check
- `resources/views/admin/batches/manage.blade.php` → Conditionally show/hide income cards

---

### Phase 4: Attendance History Display

**[COMPLETED]**

#### When Taking Batch Attendance
- Show student's attendance record in the same attendance row
- Display dates/times student attended

#### Files Updated
- `BatchAttendanceController.php` → `showAttendanceForm()` method: Added attendance history data
- `resources/views/admin/batchAttendances/take.blade.php` → Display attendance history badges

---

## Calculation Logic

### Fixed Salary (at enrollment)
```
For each assigned batch:
  Payment Amount = batch_teacher.salary_amount
```

### Percentage Salary (at enrollment + recalculate)
```
Batch Revenue = Sum of (student monthly fees for batch, month, year)
Payment Amount = (batch_teacher.salary_amount / 100) × Batch Revenue
```

---

## File Changes Summary

### Controllers
- `BatchController.php` - Added enrollment salary calculation, teacher copy method
- `BatchAttendanceController.php` - Added attendance history data
- `DueCalculationService.php` - Added percentage recalculation trigger

### Services
- `TeacherSalaryCalculationService.php`
  - Added: `addEnrollmentPayment($batchId, $studentId, $month, $year)`
  - Added: `recalculatePercentageSalaries($batchId, $month, $year)`
  - Added: `createOrUpdateTeacherPayment()`

### Views
- `resources/views/admin/batches/index.blade.php` - Added teacher copy button
- `resources/views/admin/batches/manage.blade.php` - Show/hide income based on teacher type
- `resources/views/admin/batchAttendances/take.blade.php` - Show attendance history

### Models
- `Teacher.php` - Check salary type helpers
- `TeachersPayment.php` - Store calculated payments

### Routes
- `POST /admin/batches/assign-teachers/copy-previous-all` - Copy last month teachers

---

## Checklist

### Database & Models
- [x] salary_type on teachers table
- [x] salary_amount_type on batch_teacher pivot
- [x] TeachersPayment model with amount

### Phase 0: Copy Last Month Teachers
- [x] Add route for copying teachers
- [x] Add controller method
- [x] Add button to view
- [x] Test: button copies teachers and creates payments

### Phase 1: Enrollment Payment
- [x] Add `addEnrollmentPayment()` to service
- [x] Call from `BatchController` on student enrollment
- [x] Test: enrolling student adds teacher payment

### Phase 2: Due Calculation Trigger
- [x] Add `recalculatePercentageSalaries()` to service
- [x] Call from `DueCalculationService` when due calculated
- [x] Test: changing student due updates teacher payment

### Phase 3: Income Visibility
- [x] Add teacher type check in manage view
- [x] Conditionally show/hide batch income
- [x] Test: fixed teacher cannot see income

### Phase 4: Attendance History
- [x] Load attendance history for students
- [x] Display in attendance row
- [x] Test: attendance row shows past attendance

---

## Notes
- Student monthly fees come from `student_monthly_dues` table
- Use `batch_id` + `month` + `year` to get batch revenue
- Custom per-student fees stored in `batch_student_basic_info.custom_monthly_fee`
- Discounts affect batch revenue for percentage calculations
