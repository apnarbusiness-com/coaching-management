# Student Due System Documentation

## Overview

The student due system manages monthly tuition/coeaching fees for students enrolled in batches. It tracks payments, generates monthly dues, and integrates with attendance.

---

## Data Model

### StudentMonthlyDue

The core model (`app/Models/StudentMonthlyDue.php`) stores:

| Field | Type | Description |
|-------|------|-------------|
| `student_id` | integer | Reference to StudentBasicInfo |
| `batch_id` | integer | Reference to Batch |
| `academic_class_id` | integer | Reference to AcademicClass |
| `section_id` | integer | Reference to Section |
| `shift_id` | integer | Reference to Shift |
| `month` | integer | Month (1-12) |
| `year` | integer | Year (e.g., 2026) |
| `due_amount` | float | Original due amount |
| `paid_amount` | float | Amount paid so far |
| `discount_amount` | float | Discount applied |
| `due_remaining` | float | Remaining balance |
| `status` | string | `unpaid`, `partial`, or `paid` |
| `due_date` | date | Payment due date |
| `paid_date` | date | Date of full payment |

---

## How It Works

### 1. Due Generation

When students enroll in a batch, monthly dues are generated via `DueCalculationService::generateMonthlyDues()`:

```php
// Called from DueCollectionController::generateDues()
$results = $this->dueService->generateMonthlyDues($month, $year);
```

**Process:**
1. Finds all students enrolled in batches during the specified month/year
2. For each student-batch combination, calculates the due amount
3. Creates a `StudentMonthlyDue` record with status `unpaid`

**Due Amount Calculation** (`calculateDueAmount`):
- If student has `custom_monthly_fee` → use that minus discount
- If batch `fee_type` is `course` → `fee_amount / duration_in_months` minus discount
- Otherwise → `fee_amount` minus discount

### 2. Payment Collection

Payments are processed via `DueCollectionController::payDue()`:

```php
$this->dueService->allocatePayment($due, $amount);
```

**Process:**
1. Add payment to `paid_amount`
2. Recalculate `due_remaining` = `due_amount - paid_amount`
3. Update status:
   - `due_remaining <= 0` → `paid`
   - `paid_amount > 0` but `due_remaining > 0` → `partial`
   - Otherwise → `unpaid`
4. Create an `Earning` record for accounting

### 3. Viewing Dues

**Due Collection Dashboard** (`DueCollectionController::index`):
- Shows all dues for selected month/year
- Filters by batch, class, section, status
- Shows stats: total due, collected, remaining, paid/partial/unpaid counts

**Student Due Summary** (`BatchAttendanceController::getStudentDueSummary`):
- Returns dues for specific student + batch
- Calculates totals: total_due, total_paid, total_discount, total_remaining

---

## Integration with Attendance

In `BatchAttendanceController::showAttendanceForm()` (lines 80-101):

```php
$studentTotalDues = StudentMonthlyDue::whereIn('student_id', $students->pluck('id'))
    ->where('batch_id', $batchId)
    ->whereIn('status', ['unpaid', 'partial'])
    ->groupBy('student_id')
    ->select('student_id', DB::raw('sum(due_remaining) as due_remaining_total'))
    ->pluck('due_remaining_total', 'student_id');
```

This shows which students have outstanding dues when taking attendance.

---

## Key Scopes

```php
// In StudentMonthlyDue model
$query->unpaid()        // status = 'unpaid'
$query->partial()       // status = 'partial'
$query->paid()          // status = 'paid'
$query->forMonth($m, $y) // month = $m AND year = $y
```

---

## Related Files

| File | Purpose |
|------|---------|
| `app/Models/StudentMonthlyDue.php` | Core data model |
| `app/Services/DueCalculationService.php` | Business logic for dues |
| `app/Http/Controllers/Admin/DueCollectionController.php` | UI for managing dues |
| `app/Http/Controllers/Admin/BatchAttendanceController.php` | Shows dues during attendance |
| `app/Models/Batch.php` | Contains `fee_type`, `fee_amount`, `duration_in_months` |

---

## Flow Summary

```
1. Student enrolls in Batch
      ↓
2. Admin generates monthly dues (DueCollectionController)
      ↓
3. DueCalculationService creates StudentMonthlyDue records
      ↓
4. Student pays → DueCollectionController::payDue()
      ↓
5. Earning record created, due status updated
      ↓
6. Attendance shows due status, Dashboard shows collection stats
```
