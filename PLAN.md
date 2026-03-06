## Student Import Implementation Plan With `PLAN.md` Check Tracking

### Summary
`StudentBasicInfo` import কে generic bulk insert থেকে custom store-compatible flow-এ আনা হয়েছে, যাতে import করলে:
- `StudentBasicInfo` create/update হয়
- `StudentDetailsInformation` create/update হয়
- `User` create/update + `Student` role assign হয়
- student login-ready থাকে (`admission_id` + password)

---

### Public API / Interface Changes
- Student import upload mime: `csv,xls,xlsx,txt`
- New request field: `duplicate_mode` (`skip|update|duplicate`)
- Student import process summary: `created/updated/skipped/failed` counts

---

### Implementation Checklist
- [x] Add student-specific parse/process methods in `StudentBasicInfoController`
- [x] Stop using generic `CsvImportTrait::processCsvImport` for Student module only
- [x] Update student import routes to hit new student-specific methods
- [x] Accept Excel files (`.xls/.xlsx`) in parse validation
- [x] Add header auto-detection for files where row-1 is summary and row-2 is actual header
- [x] Add duplicate strategy selector in import UI (`skip/update/duplicate`)
- [x] Build row normalizer (Excel columns -> internal fields)
- [x] Apply safe defaults for missing required fields:
  - `last_name=N/A`
  - `gender=others`
  - `dob=2000-01-01`
  - `guardian_relation=Other`
  - `guardian_contact_number=contact_number` fallback
- [x] Implement duplicate matching priority:
  - `contact_number` -> `id_no` -> `email`
- [x] Create service `app/Services/StudentImportService.php` to centralize store-compatible create/update logic
- [x] Ensure user creation/update policy:
  - `admission_id=id_no` (fallback generated)
  - password: Excel password থাকলে সেটি, না থাকলে `admission_id`
  - role sync to `Student/student`
- [x] Ensure `student_basic_infos.user_id` link set হয়
- [x] Create/update `StudentDetailsInformation` in same import transaction
- [x] Add DB transaction handling per row + error capture
- [x] Add import summary flash message with counts
- [x] Fix `StudentDetailsInformation::$fillable` by adding `guardian_email`
- [x] Keep non-student modules on existing generic CSV trait
- [x] Add feature tests for parse + process + duplicate modes + login credentials
- [x] Add regression test: manual `store()` flow unchanged

---

### Changed Files
- `app/Http/Controllers/Admin/StudentBasicInfoController.php`
- `app/Models/StudentDetailsInformation.php`
- `app/Services/StudentImportService.php`
- `resources/views/admin/studentBasicInfos/index.blade.php`
- `resources/views/admin/studentBasicInfos/parseImport.blade.php`
- `routes/web.php`
- `tests/Feature/StudentImportServiceTest.php`
- `tests/Feature/StudentImportControllerTest.php`

---

### Notes
- এই phase-এ import scope intentionally রাখা হয়েছে: `Student + User + Details only`.
- Payment/course columns (`Che`, `Phy`, etc.) parse করা হয় না।
- `Name` full value `first_name`-এ যায়, `last_name` default `N/A`.

