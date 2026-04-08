# Dev Coaching Management - Quick Reference

## Project Basics
- **Framework**: Laravel 10
- **Purpose**: Coaching center management (students, teachers, payments, attendance)
- **Auth**: admin@admin.com / password
- **DB**: MySQL

---

## Key Routes

| URL | Purpose |
|-----|---------|
| `/` | Redirect to login or dashboard |
| `/admission` | Public student admission form |
| `/admin` | Admin dashboard |
| `/admin/student-basic-infos` | Student CRUD |
| `/admin/batches` | Batch management |
| `/admin/due-collections` | Due collection |
| `/admin/earnings` | Payment records |
| `/admin/expenses` | Expense records |
| `/admin/teachers` | Teacher management |
| `/admin/batch-attendances` | Attendance |

---

## Core Models

| Model | Table | Key |
|-------|-------|-----|
| `User` | users | auth + admission_id for students |
| `StudentBasicInfo` | student_basic_infos | core student data |
| `StudentDetailsInformation` | student_details_informations | extended info |
| `StudentMonthlyDue` | student_monthly_dues | monthly fee tracking |
| `Batch` | batches | course/class with schedule |
| `Teacher` | teachers | teacher profiles |
| `Earning` | earnings | student payments |
| `Expense` | expenses | center expenses |
| `BatchAttendance` | batch_attendances | attendance |

---

## Relationships

```
User (1) ──< StudentBasicInfo ──< StudentDetailsInformation
User (1) ──< Teacher
StudentBasicInfo >──< Batch (many-to-many pivot)
Teacher >──< Batch (pivot with salary)
Batch >──< Subject
StudentBasicInfo >----< StudentMonthlyDue >----< Earning
```

---

## Key Services

- `DueCalculationService` - Calculate monthly dues
- `StudentImportService` - CSV/Excel import with duplicate handling

---

## Important Patterns

1. **Status**: `1` = active, `0` = inactive
2. **Timestamps**: `created_at`, `updated_at`, `deleted_at` (SoftDeletes)
3. **Media**: Spatie Media Library
4. **RBAC**: Spatie Permission (roles: Admin, Teacher, Student)
5. **Student login**: admission_id (from `id_no`) + password

---

## Key Files

- Controllers: `app/Http/Controllers/Admin/`
- Models: `app/Models/`
- Views: `resources/views/admin/`
- Services: `app/Services/`
- Routes: `routes/web.php`

---

## Common Tasks

- **Add student**: POST `/admin/student-basic-infos`
- **Create batch**: POST `/admin/batches`
- **Take attendance**: `/admin/batch-attendances/{batchId}/take`
- **Record payment**: POST `/admin/earnings`
- **Import students**: Upload CSV/Excel via `/admin/student-basic-infos/parse-import`

---

## Console Commands

- `php artisan generate:monthly-due` - Generate monthly dues

---

## Quick DB Info

- Core tables: `2026_01_26_*` (roles, permissions, users, students)
- Batches: `2026_02_19_*` (batches, batch_student)
- Dues: `2026_03_16_*` (student_monthly_dues)