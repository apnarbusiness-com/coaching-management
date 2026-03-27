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
- [x] Add `salary_amount_type` column to `teachers` table (fixed/percentage)
- [x] Add `salary_amount_type` column to `batch_teacher` pivot table (fixed/percentage)
- [x] Update `Teacher` model with new fields and accessors
- [x] Add `amount` column to `teachers_payments` table

### Teacher CRUD Updates
- [x] Update teacher create/edit forms to include salary_type and salary_amount_type
- [x] Update `TeacherController` store/update logic

### Batch Teacher Assignment Updates
- [x] Update batch assign teachers form to include salary_amount_type selector
- [x] Update `BatchController::storeAssignedTeacher()` to save amount type

### Salary Calculation Service
- [x] Create `TeacherSalaryCalculationService` 
- [x] Method: `calculateMonthlySalary(teacher_id, month, year)`
- [x] Method: `calculateBatchTeacherSalary(batch_id, month, year)`
- [x] Handle fixed salary_type on Teacher model
- [x] Handle variable with fixed amount from batch
- [x] Handle variable with percentage from batch revenue

### TeachersPayment Integration
- [x] Update TeachersPayment creation to auto-calculate salary
- [x] Add "Generate Monthly Salaries" route/action
- [x] Bulk create salary records for all teachers

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

### teachers_payments table
```php
// Added
$table->decimal('amount', 15, 2)->nullable();
```

---

## File Changes

### Models
- `app/Models/Teacher.php` - Added SALARY_AMOUNT_TYPE_SELECT, updated $fillable, batches relationship
- `app/Models/TeachersPayment.php` - Added amount field, getCalculatedAmountAttribute

### Controllers
- `app/Http/Controllers/Admin/BatchController.php` - Updated storeAssignedTeacher to save salary_amount_type
- `app/Http/Controllers/Admin/TeachersPaymentController.php` - Added generate, calculate methods

### Services
- `app/Services/TeacherSalaryCalculationService.php` (new)

### Views
- `resources/views/admin/teachers/create.blade.php` - Added salary_amount_type dropdown
- `resources/views/admin/teachers/edit.blade.php` - Added salary_amount_type dropdown
- `resources/views/admin/batches/assign_teachers.blade.php` - Added salary_amount_type selector

### Routes
- `POST /admin/teachers-payments/generate` - Generate monthly salaries
- `POST /admin/teachers-payments/calculate` - Calculate teacher salary

### Migrations
- `2026_03_24_000001_add_salary_amount_type_to_teachers_and_batch_teacher.php` (new)
- `2026_03_24_000002_add_amount_to_teachers_payments_table.php` (new)

---

## Usage

### 1. Set Teacher Salary Type
When creating/editing a teacher:
- Select `salary_type`: 'fixed' or 'variable'
- If 'fixed': Set `salary_amount` and `salary_amount_type` (used for base salary)

### 2. Assign Teacher to Batch
In batch management → Assign Teachers:
- Set `salary_amount`: Fixed amount or percentage
- Set `salary_amount_type`: 'fixed' (BDT) or 'percentage' (%)

### 3. Generate Monthly Salaries
Go to Teachers Payments → Generate Salaries:
- Select month and year
- Click Generate
- System calculates all teachers' salaries based on their settings

### 4. View Salary Breakdown
In Teachers Payment show page, view:
- Fixed salary component
- Per-batch breakdown with revenue and calculated amount

---

## Notes
- Student monthly fees come from `student_monthly_dues` table
- Use `batch_id` + `month` + `year` to get batch revenue
- Custom per-student fees stored in `batch_student_basic_info.custom_monthly_fee`
- Need to sum both regular dues and custom fees for accurate calculation
