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
| `/login` | User login |
| `/admin` | Admin dashboard |
| `/admin/student-basic-infos` | Student CRUD |
| `/admin/batches` | Batch management |
| `/admin/due-collections` | Due collection |
| `/admin/earnings` | Payment records |
| `/admin/expenses` | Expense records |
| `/admin/teachers` | Teacher management |
| `/admin/batch-attendances` | Attendance |
| `/admin/teachers-payments` | Teacher salary payments |
| `/admin/admission-applications` | Public admission applications |
| `/admin/student-basic-infos/print-id-card/{id}` | Print student ID card |
| `/admin/teachers/{id}/id-card` | Print teacher ID card |

---

## Core Models

| Model | Table | Key |
|-------|-------|-----|
| `User` | users | auth + admission_id for students |
| `Role` | roles | Admin, Teacher, Student |
| `Permission` | permissions | RBAC permissions |
| `StudentBasicInfo` | student_basic_infos | core student data |
| `StudentDetailsInformation` | student_details_informations | extended info |
| `StudentMonthlyDue` | student_monthly_dues | monthly fee tracking |
| `StudentAdmissionApplication` | student_admission_applications | public admission form |
| `Batch` | batches | course/class with schedule |
| `Subject` | subjects | subjects offered |
| `Teacher` | teachers | teacher profiles |
| `TeachersPayment` | teachers_payments | salary records |
| `TeacherPaymentTransaction` | teacher_payment_transactions | salary transactions |
| `Earning` | earnings | student payments |
| `EarningCategory` | earning_categories | payment categories |
| `Expense` | expenses | center expenses |
| `ExpenseCategory` | expense_categories | expense categories |
| `BatchAttendance` | batch_attendances | attendance |
| `AcademicClass` | academic_classes | grade/class levels |
| `Section` | sections | class sections |
| `Shift` | shifts | time shifts |
| `ClassRoom` | class_rooms | physical classrooms |
| `AuditLog` | audit_logs | activity logging |

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
- `TeacherSalaryCalculationService` - Calculate teacher salaries
- `DashboardWidgetService` - Dashboard widgets data

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
- **Batch manage**: `/admin/batches/{batch}/manage`
- **Enroll students**: `/admin/batches/{batch}/assign-students`
- **Assign teacher**: `/admin/batches/{batch}/assign-teachers`
- **Take attendance**: `/admin/batch-attendances/{batchId}/take`
- **Record payment**: POST `/admin/earnings`
- **Import students**: Upload CSV/Excel via `/admin/student-basic-infos/parse-csv-import`
- **Demo CSV**: `/admin/student-basic-infos/demo-csv`
- **Raw import**: `/admin/student-basic-infos/raw-imports` (2-step import)
- **Approve admission**: POST `/admin/admission-applications/{id}/approve`
- **Teacher payment**: POST `/admin/teachers-payments/generate`
- **Due summary**: `/admin/due-collections`

---

## Console Commands

- `php artisan due:generate-monthly` - Generate monthly dues
- Options: `--month=`, `--year=`, `--dry-run`

---

## Quick DB Info

- Core tables: `2026_01_26_*` (roles, permissions, users, students)
- Academic: `2026_02_01_*` (sections, shifts, classes, subjects)
- Batches: `2026_02_19_*` (batches, batch_student)
- Dues: `2026_03_16_*` (student_monthly_dues)
- Import: `2026_03_06_*` (student_import_raws)