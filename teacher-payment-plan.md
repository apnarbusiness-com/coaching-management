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
- No "Generate Monthly Salaries" button needed

### 2. Fixed Salary Teachers
- At enrollment time: teacher's payment record is automatically added
- Fixed teachers **cannot see** batch income

### 3. Variable/Percentage Salary Teachers
- At enrollment time: salary is calculated
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

### Phase 1: Enrollment-Time Salary Calculation

#### When Student Enrolls in Batch
1. Check if batch has assigned teachers
2. For each teacher:
   - **Fixed type (from batch_teacher)**: Add payment record for that batch
   - **Percentage type**: Calculate percentage × batch revenue for that month

#### Files to Update
- `BatchController.php` → `storeStudentEnrollment()` or `assignStudent()`
- Add call to `TeacherSalaryCalculationService::addEnrollmentPayment()`

#### Service Method
```php
public function addEnrollmentPayment($batchId, $studentId, $month, $year)
{
    // For each teacher in batch:
    // If fixed: create TeachersPayment record
    // If percentage: calculate and record
}
```

---

### Phase 2: Due Calculation → Re-calculate Percentage Salaries

#### When Student Monthly Due is Calculated
1. Trigger re-calculation for all percentage-based teachers in that batch
2. Update or create their payment records for the affected month/year

#### Files to Update
- `DueCalculationService.php` → `generateDueForEnrollment()` and related methods
- Call `TeacherSalaryCalculationService::recalculatePercentageSalaries()`

#### Service Method
```php
public function recalculatePercentageSalaries($batchId, $month, $year)
{
    // Find all percentage-based teachers for this batch
    // Calculate batch revenue for month/year
    // Update their payment records
}
```

---

### Phase 3: Batch Income Visibility

#### Update Batch Manage View
- Check current user's teacher profile
- If teacher is fixed-type → hide income section
- If teacher is variable/percentage → show income section

#### Files to Update
- `resources/views/admin/batches/manage.blade.php`
- Add permission check for income display

---

### Phase 4: Attendance History Display

#### When Taking Batch Attendance
- Show student's attendance record in the same attendance row
- Display dates/times student attended

#### Files to Update
- `resources/views/admin/batch-attendances/take.blade.php`
- Update `BatchAttendanceController` to load attendance history

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
- `BatchController.php` - Add enrollment salary calculation
- `DueCalculationService.php` - Add percentage re-calculation trigger

### Services
- `TeacherSalaryCalculationService.php`
  - Add: `addEnrollmentPayment($batchId, $studentId, $month, $year)`
  - Add: `recalculatePercentageSalaries($batchId, $month, $year)`

### Views
- `resources/views/admin/batches/manage.blade.php` - Show/hide income based on teacher type
- `resources/views/admin/batch-attendances/take.blade.php` - Show attendance history

### Models
- `Teacher.php` - Check salary type helpers
- `TeachersPayment.php` - Store calculated payments

---

## Checklist

### Database & Models
- [x] salary_type on teachers table
- [x] salary_amount_type on batch_teacher pivot
- [x] TeachersPayment model with amount

### Phase 1: Enrollment Payment
- [ ] Add `addEnrollmentPayment()` to service
- [ ] Call from `BatchController` on student enrollment
- [ ] Test: enrolling student adds teacher payment

### Phase 2: Due Calculation Trigger
- [ ] Add `recalculatePercentageSalaries()` to service
- [ ] Call from `DueCalculationService` when due calculated
- [ ] Test: changing student due updates teacher payment

### Phase 3: Income Visibility
- [ ] Add teacher type check in manage view
- [ ] Conditionally show/hide batch income
- [ ] Test: fixed teacher cannot see income

### Phase 4: Attendance History
- [ ] Load attendance history for students
- [ ] Display in attendance row
- [ ] Test: attendance row shows past attendance

---

## Notes
- Student monthly fees come from `student_monthly_dues` table
- Use `batch_id` + `month` + `year` to get batch revenue
- Custom per-student fees stored in `batch_student_basic_info.custom_monthly_fee`
- Discounts affect batch revenue for percentage calculations
