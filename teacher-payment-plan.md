# Teacher Payment System Plan

## Overview
Teacher salary calculation with two modes: Fixed and Variable, where variable can be either fixed amount per batch or percentage of batch revenue.

---

## Current State
- `Teacher` model has `salary_type` (fixed/variable) and `salary_amount`
- `batch_teacher` pivot table has `salary_amount` and `role`
- `TeachersPayment` model records monthly payments manually
- No automatic calculation exists

---

## Requirements

### 1. Teacher Salary Type
- **Fixed**: Fixed amount paid every month
- **Variable**: Amount based on batch assignments

### 2. Variable Salary Calculation
When teacher is assigned to a batch, `salary_amount` can be:
- **Fixed type**: Fixed number (e.g., 5000 BDT per month)
- **Percentage type**: Percentage of batch's total monthly revenue

### 3. Monthly Calculation Logic
- Calculate per batch: teacher gets salary based on their batch assignments
- Example: March 2026
  - Batch "ICT" has 10 students: 5 × 1000 BDT + 5 × 500 BDT = 7500 BDT total
  - Teacher assigned to ICT with 30% → gets 30% of 7500 = 2250 BDT
- Teacher can get different amounts each month based on:
  - Number of enrolled students
  - Student fee variations (custom monthly fee per student)

---

## Implementation Checklist

### Database & Model Changes
- [ ] Add `salary_amount_type` column to `teachers` table (fixed/percentage)
- [ ] Add `salary_amount_type` column to `batch_teacher` pivot table (fixed/percentage)
- [ ] Update `Teacher` model with new fields and accessors
- [ ] Update migration files

### Teacher CRUD Updates
- [ ] Update teacher create/edit forms to include salary_type and salary_amount_type
- [ ] Update `TeacherController` store/update logic

### Batch Teacher Assignment Updates
- [ ] Update batch assign teachers form to include salary_amount_type selector
- [ ] Update `BatchController::storeAssignedTeacher()` to save amount type

### Salary Calculation Service
- [ ] Create `TeacherSalaryCalculationService` 
- [ ] Method: `calculateMonthlySalary(teacher_id, month, year)`
- [ ] Method: `calculateBatchTeacherSalary(batch_id, month, year)`
- [ ] Handle fixed salary_type on Teacher model
- [ ] Handle variable with fixed amount from batch
- [ ] Handle variable with percentage from batch revenue

### TeachersPayment Integration
- [ ] Update TeachersPayment creation to auto-calculate salary
- [ ] Add "Generate Monthly Salaries" button/action
- [ ] Bulk create salary records for all teachers

### Dashboard & Reports
- [ ] Add teacher salary summary on admin dashboard
- [ ] Show monthly teacher expense breakdown

### Testing
- [ ] Write tests for fixed salary calculation
- [ ] Write tests for variable fixed amount calculation
- [ ] Write tests for variable percentage calculation
- [ ] Write tests for mixed batch assignments

---

## Calculation Logic

### Fixed Salary (Teacher.salary_type = 'fixed')
```
Monthly Salary = Teacher.salary_amount
```

### Variable Salary (Teacher.salary_type = 'variable')

#### Option A: Fixed Amount (batch_teacher.salary_amount_type = 'fixed')
```
Monthly Salary = Sum of (batch_teacher.salary_amount) for all assigned batches
```

#### Option B: Percentage (batch_teacher.salary_amount_type = 'percentage')
```
Batch Revenue = Sum of all student monthly dues for that batch (month/year)
Teacher Share = (batch_teacher.salary_amount / 100) × Batch Revenue
Monthly Salary = Sum of Teacher Share for all assigned batches
```

### Combined Calculation (if teacher has both batch types)
```
Monthly Salary = Fixed from Teacher + Sum of (Fixed Batch) + Sum of (% Batch)
```

---

## Data Structure Changes

### teachers table
```php
// Current
$fillable = ['salary_type', 'salary_amount', ...];

// New
$fillable = ['salary_type', 'salary_amount', 'salary_amount_type', ...];
```

### batch_teacher pivot table
```php
// Current
$table->decimal('salary_amount', 15, 2)->default(0);

// New
$table->decimal('salary_amount', 15, 2)->default(0);
$table->string('salary_amount_type')->default('fixed'); // 'fixed' or 'percentage'
```

---

## File Changes

### Models
- `app/Models/Teacher.php`
- `app/Models\Batch.php` (update relationship)

### Controllers
- `app/Http/Controllers/Admin/TeacherController.php`
- `app/Http/Controllers/Admin/BatchController.php`
- `app/Http/Controllers/Admin/TeachersPaymentController.php`

### Services
- `app/Services/TeacherSalaryCalculationService.php` (new)

### Views
- `resources/views/admin/teachers/create.blade.php`
- `resources/views/admin/teachers/edit.blade.php`
- `resources/views/admin/batches/assign_teachers.blade.php`
- `resources/views/admin/teachersPayments/index.blade.php`

### Routes
- Add route for generating monthly salaries

---

## Notes
- Student monthly fees come from `student_monthly_dues` table
- Use `batch_id` + `month` + `year` to get batch revenue
- Custom per-student fees stored in `batch_student_basic_info.custom_monthly_fee`
- Need to sum both regular dues and custom fees for accurate calculation
