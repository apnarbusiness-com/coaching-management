# Dev Coaching Management System

A Laravel-based coaching center management system for managing students, teachers, batches, payments, attendance, and more.

---

## Project Overview

- **Framework**: Laravel 10+
- **Purpose**: Coaching center/student management system
- **Key Features**:
  - Student admission & enrollment
  - Batch management with scheduling
  - Teacher management with salary payments
  - Monthly due collection & tracking
  - Batch attendance
  - Financial management (earnings/expenses)
  - Student import via CSV/Excel

---

## Tech Stack

- **Backend**: Laravel 10
- **Frontend**: Blade templates + Bootstrap
- **Database**: MySQL
- **Authentication**: Laravel Auth (Breeze/Jetstream style)
- **Media**: Spatie Media Library
- **PDF Generation**: Barryvdh DomPDF

---

## Database Models

| Model | Table | Description |
|-------|-------|-------------|
| User | users | Authentication, stores admission_id for students |
| Role | roles | User roles (Admin, Student, Teacher) |
| Permission | permissions | Access control permissions |
| StudentBasicInfo | student_basic_infos | Core student data |
| StudentDetailsInformation | student_details_informations | Extended student details |
| StudentMonthlyDue | student_monthly_dues | Monthly fee tracking |
| Batch | batches | Course/class batches |
| Subject | subjects | Subjects offered |
| Teacher | teachers | Teacher profiles |
| AcademicClass | academic_classes | Grade/class levels |
| Section | sections | Class sections |
| Shift | shifts | Time shifts |
| ClassRoom | class_rooms | Physical classrooms |
| Earning | earnings | Student payments |
| EarningCategory | earning_categories | Payment categories |
| Expense | expenses | Center expenses |
| ExpenseCategory | expense_categories | Expense categories |
| TeachersPayment | teachers_payments | Teacher salary payments |
| BatchAttendance | batch_attendances | Attendance records |
| AuditLog | audit_logs | Activity logging |
| StudentAdmissionApplication | student_admission_applications | Public admission form |

---

## Key Relationships

```
User (1) ----< StudentBasicInfo (1) ----< StudentDetailsInformation
User (1) ----< Teacher
StudentBasicInfo >----< Batch (many-to-many with pivot)
Teacher >----< Batch (many-to-many with salary pivot)
Batch >----< Subject
StudentBasicInfo >----< Subject
Earning >----< StudentMonthlyDue
```

---

## Authentication & Authorization

- **Roles**: Admin, Teacher, Student (stored in `roles` table)
- **Permissions**: Uses Spatie Permission package
- **Auth Gates**: Custom middleware in `AuthGates` middleware
- **Student Login**: Uses `admission_id` (from id_no) + password

---

## Key Features

### 1. Student Management
- CRUD operations for students
- CSV/Excel import with duplicate handling
- ID card generation
- Profile with batch enrollment
- Subject synchronization

### 2. Batch Management
- Create batches with subjects, fees, capacity
- Class schedule (days + time + room)
- Student enrollment (manual + quick enroll)
- Copy previous month enrollments
- Teacher assignment

### 3. Due Collection
- Generate monthly dues for enrolled students
- Track paid/unpaid status
- Record payments via Earning model

### 4. Attendance
- Batch-wise attendance taking
- Attendance reports
- Student due summary view

### 5. Financial Management
- **Earnings**: Student payments, categorized by type
- **Expenses**: Center expenses, teacher salary expenses
- Summary views with filtering

### 6. Public Admission
- `/admission` - Public form for new student applications
- Admin approval workflow

---

## Key Routes

| Route | Controller | Description |
|-------|------------|-------------|
| `/admission` | AdmissionApplicationController | Public admission form |
| `/admin` | HomeController | Admin dashboard |
| `/admin/student-basic-infos` | StudentBasicInfoController | Student CRUD |
| `/admin/batches` | BatchController | Batch management |
| `/admin/due-collections` | DueCollectionController | Due collection |
| `/admin/batch-attendances` | BatchAttendanceController | Attendance |
| `/admin/earnings` | EarningsController | Payments |
| `/admin/expenses` | ExpensesController | Expenses |
| `/admin/teachers` | TeacherController | Teacher management |
| `/profile/password` | ChangePasswordController | Password change |

---

## Key Services

### DueCalculationService
Calculates monthly dues for students based on batch enrollment and custom fees.

### StudentImportService
Handles student import from CSV/Excel with:
- Duplicate detection (contact_number, id_no, email)
- User creation/update
- StudentDetailsInformation creation
- Role assignment (Student)

---

## Console Commands

### GenerateMonthlyDue
Creates monthly due records for all enrolled students.

---

## Key Files & Locations

### Controllers
- `app/Http/Controllers/Admin/` - Admin CRUD controllers
- `app/Http/Controllers/Auth/` - Auth controllers
- `app/Http/Controllers/AdmissionApplicationController.php`

### Models
- `app/Models/` - All Eloquent models

### Views
- `resources/views/admin/` - Admin panel views
- `resources/views/student/` - Student portal views
- `resources/views/auth/` - Auth views
- `resources/views/layouts/` - Layouts

### Requests
- `app/Http/Requests/` - Form request validation

### Services
- `app/Services/DueCalculationService.php`
- `app/Services/StudentImportService.php`

---

## Database Migrations Key

- Core tables: 2026_01_26_* (roles, permissions, users, students, etc.)
- Academic: 2026_02_01_* (sections, shifts, classes)
- Batches: 2026_02_19_* (batches, batch_student pivot)
- Monthly Dues: 2026_03_16_* (student_monthly_dues, earning links)
- Import: 2026_03_06_* (student_import_raws)

---

## Key Conventions

1. **Status fields**: Use `1` for active, `0` for inactive
2. **Timestamps**: Use `created_at`, `updated_at`, `deleted_at` (SoftDeletes)
3. **Media**: Use Spatie Media Library for file uploads
4. **Audit**: Use `Auditable` trait for tracking changes
5. **Import**: Use `CsvImportTrait` for generic imports, custom for students

---

## User Roles

1. **Admin**: Full access to all features
2. **Teacher**: Limited to own data, attendance
3. **Student**: View own profile, batches, pay dues

---

## Student Import Flow

1. Upload CSV/Excel file
2. Parse and preview data
3. Select duplicate mode (skip/update/duplicate)
4. Process import:
   - Match duplicates by contact_number > id_no > email
   - Create/update User with admission_id
   - Create/update StudentBasicInfo
   - Create/update StudentDetailsInformation
   - Assign Student role

---

## Configuration

- Date format: `config('panel.date_format')` - typically Y-m-d
- Currency: BDT (Bangladesh Taka)
- File storage: `storage/app/public/`

---

## External Packages Used

- `spatie/laravel-permission` - RBAC
- `spatie/laravel-medialibrary` - File management
- `barryvdh/laravel-dompdf` - PDF generation
- `laravel/ui` - Auth scaffolding

---

## Development Notes

- Custom reset password notification: `App\Notifications\CustomResetPassword`
- Student profile routes: `/admin/student/profile`, `/admin/student/batches`
- Batch attendance routes: `/admin/batch-attendances/{batchId}/take`
- Due collection uses AJAX for student search and payment processing
