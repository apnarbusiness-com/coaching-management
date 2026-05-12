# PLAN: Teacher Salary Calculation Type (Monthly-Fixed vs Batch-Wise)

## Goal
Use existing `teachers.salary_type` column with new values.

- **Monthly-Fixed** (`salary_type = 'monthly_fixed'`): Teacher gets one fixed amount per month regardless of which/how many batches they teach. `teachers.salary_amount` is the total monthly salary. No salary input needed at batch enrollment time.
- **Batch-Wise** (`salary_type = 'batch_wise'`): Teacher gets paid per batch. `teachers.salary_amount` is ignored. Salary amount & type are set at batch enrollment time via `batch_teacher` pivot.

---

## [x] Step 1: Teacher Model (`app/Models/Teacher.php`)

Updated `SALARY_TYPE_SELECT` constant:
```php
'monthly_fixed' => 'Monthly Fixed',
'batch_wise' => 'Batch Wise',
```

---

## [x] Step 2: Migration

File: `database/migrations/2026_05_12_000001_convert_salary_type_values_in_teachers.php`

Converts existing `fixed` → `monthly_fixed`, `variable` → `batch_wise`.

---

## [x] Step 3: Store & Update Teacher Requests

### `StoreTeacherRequest.php`
- `salary_type` → `required|string|in:monthly_fixed,batch_wise`
- `salary_amount` → `required_if:salary_type,monthly_fixed`

### `UpdateTeacherRequest.php`
- Same validation rules

---

## [x] Step 4: Teacher Create & Edit Views

### `resources/views/admin/teachers/create.blade.php`
- Replaced old 3 fields (Salary Type + Base Salary + Salary Amount Type) with:
  - **Salary Calculation Type** select (`monthly_fixed` / `batch_wise`)
  - **Monthly Salary Amount** input (shown/hidden via JS based on selection)
- Removed `salary_amount_type` field from profile

### `resources/views/admin/teachers/edit.blade.php`
- Same changes + same JS toggle

---

## [X] Step 5: Batch Assignment View

### `resources/views/admin/batches/assign_teachers.blade.php`
- Add data attributes to teacher `<option>`: `data-salary-type`, `data-salary-amount`
- JS: on teacher select change, check if `monthly_fixed` → hide salary fields + show badge
- If `batch_wise` → show salary fields as normal

---

## [x] Step 6: BatchController@storeAssignedTeacher

- If teacher is `monthly_fixed`:
  - Store batch_teacher pivot with `salary_amount = 0`, `salary_amount_type = 'fixed'`
  - Use teacher's `salary_amount` for the TeachersPayment record
- If `batch_wise`: keep existing logic unchanged

---

## [x] Step 7: TeacherSalaryCalculationService

### `calculateMonthlySalary()` — FIX double-addition bug:
- `monthly_fixed` → return `teacher->salary_amount` only (ignore batch_teacher pivot)
- `batch_wise` → sum all batch_teacher salaries only (ignore `teacher->salary_amount`)

### `createOrUpdateTeacherPayment()`:
- `monthly_fixed` → use `teacher->salary_amount`
- `batch_wise` → call `calculateMonthlySalary()`

### `addEnrollmentPayment()`:
- `monthly_fixed` → skip (payment is fixed)
- `batch_wise` → keep existing recalculation

---

## [x] Step 8: BatchController@calculateAndCreateTeacherPayment

- `monthly_fixed` → payment with `amount = teacher->salary_amount`
- `batch_wise` → keep existing logic

---

## [ ] Step 9: Update All Existing Teacher Profiles

Manually edit each teacher via Admin UI:
| Current | New |
|---------|-----|
| `fixed` → `monthly_fixed` (enter salary_amount) |
| `variable` → `batch_wise` (clear salary_amount) |

---

## Progress

| Step | Status |
|------|--------|
| 1. Teacher model constants | ✅ Done |
| 2. Migration (convert data) | ✅ Done |
| 3. Request validation | ✅ Done |
| 4. Teacher create/edit views | ✅ Done |
| 5. Batch assign_teachers view | ✅ Done |
| 6. BatchController@storeAssignedTeacher | ✅ Done |
| 7. TeacherSalaryCalculationService | ✅ Done |
| 8. BatchController@calculateAndCreateTeacherPayment | ✅ Done |
| 9. Manual teacher profile updates | ⬜ Pending |
