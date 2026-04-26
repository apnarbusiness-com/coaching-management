# Coaching Management - Features List

## Project Overview
- **Framework:** Laravel 10
- **Purpose:** Coaching Center Management System
- **Frontend:** Admin Panel (Web)
- **Mobile API:** Laravel Sanctum (Token-based)

---

## 1. User Management

### 1.1 Authentication
- [x] Admin Login (email/password)
- [x] Role-based Access Control (RBAC)
- [x] Permission Management
- [x] Password Change

### 1.2 Users
- [x] User Listing
- [x] Create/Edit/Delete Users
- [x] Role Assignment
- [x] User Status Management

### 1.3 Roles & Permissions
- [x] Pre-defined Roles: Admin, Manager, Teacher, Student
- [x] Permission Management
- [x] Role-based Access Gates

---

## 2. Academic Management

### 2.1 Academic Classes
- [x] Class Listing (Class 6-10)
- [x] Create/Edit/Delete Classes
- [x] Class-based Sections
- [x] Class-based Shifts

### 2.2 Sections
- [x] Section Management
- [x] Class-Section Mapping

### 2.3 Shifts
- [x] Morning/Evening Shifts
- [x] Time Slot Management

### 2.4 Subjects
- [x] Subject Listing
- [x] Subject-Teacher Assignment
- [x] Subject-Class Mapping

---

## 3. Student Management

### 3.1 Student Admission
- [x] Student Basic Information
- [x] Student Details Information
- [x] Academic Background
- [x] Photo Upload
- [x] Batch Assignment
- [x] Student Import (Excel)

### 3.2 Student Profiles
- [x] View Student Details
- [x] Edit Student Information
- [x] Student Status (Active/Inactive)
- [x] Assigned Batches View

### 3.3 Student Flags
- [x] Custom Flag Creation
- [x] Flag Assignment to Students
- [x] Flag-based Filtering

---

## 4. Teacher Management

### 4.1 Teacher Records
- [x] Teacher Profile
- [x] Employee Code
- [x] Contact Information
- [x] Gender & Joining Date
- [x] Profile Image

### 4.2 Teacher Assignment
- [x] Batch-Teacher Assignment
- [x] Subject-Teacher Assignment
- [x] Salary Type (Fixed/Percentage)
- [x] Salary Amount

---

## 5. Batch Management

### 5.1 Batches
- [x] Batch Creation (Name, Subject, Class, Section, Shift)
- [x] Class Schedule (Days, Time)
- [x] Room Assignment
- [x] Capacity Management
- [x] Batch Status (Active/Inactive)

### 5.2 Batch Students
- [x] Student Enrollment
- [x] One-time Discount
- [x] Batch Transfer
- [x] Enrollment History

### 5.3 Batch Teachers
- [x] Teacher Assignment
- [x] Salary Configuration

---

## 6. Attendance Management

### 6.1 Batch Attendance
- [x] Daily Attendance Taking
- [x] Student Status (Present/Absent)
- [x] Attendance Date
- [x] Batch-wise Attendance View

### 6.2 Attendance Reports
- [x] Monthly Attendance
- [x] Student-wise History

---

## 7. Payment & Fees

### 7.1 Monthly Dues
- [x] Monthly Due Generation
- [x] Due Amount Configuration
- [x] Discount Application
- [x] Due Status (Paid/Unpaid)

### 7.2 Payments (Student)
- [x] Payment Collection
- [x] Payment History
- [x] Discount Tracking
- [x] Payment Method (Cash/Bank)

### 7.3 Teacher Salary
- [x] Salary Calculation
- [x] Monthly Salary Generation
- [x] Salary Payment Processing
- [x] Payment History

---

## 8. Financial Management

### 8.1 Earnings (Income)
- [x] Student Payments
- [x] Payment Recording
- [x] Category Management
- [x] Date-wise Tracking

### 8.2 Expenses
- [x] Expense Entry
- [x] Teacher Salary Expenses
- [x] Other Expenses
- [x] Category Management

### 8.3 Financial Ledger
- [x] Monthly Earnings (Batch-wise)
- [x] Monthly Expenses (Batch-wise)
- [x] Year-wise Overview
- [x] Profit/Loss Calculation

### 8.4 Cash Book
- [x] Transaction Recording
- [x] Cash/Bank Accounts
- [x] Transaction History
- [x] Balance Tracking

---

## 9. Academic Background

### 9.1 Student Background
- [x] School Information
- [x] Previous Results
- [x] Academic History

---

## 10. Reports & Analytics

### 10.1 Dashboard
- [x] Total Students
- [x] Total Teachers
- [x] Total Earnings
- [x] Due Summary
- [x] Recent Payments

### 10.2 Reports
- [x] Student Reports
- [x] Attendance Reports
- [x] Earnings Reports
- [x] Expense Reports
- [x] Profit/Loss Reports
- [x] Export Options

---

## 11. Admission Applications

### 11.1 Public Admission
- [x] Online Application Form
- [x] Application Status Check
- [x] Admin Review

---

## 12. Classrooms

### 12.1 Room Management
- [x] Room Creation
- [x] Room Capacity
- [x] Room Availability

---

## 13. Audit Logs

### 13.1 Activity Tracking
- [x] User Login History
- [x] Data Modification Logs
- [x] Action Timestamps

---

## 14. Mobile API (Development)

### 14.1 Authentication
- [x] Login (username/email/admission_id)
- [x] Logout
- [x] Profile Update
- [x] Change Password
- [x] Token-based Auth (Sanctum)

### 14.2 Student APIs (Planned)
- [ ] Student Profile
- [ ] Enrolled Batches
- [ ] Monthly Dues
- [ ] Attendance Records
- [ ] Payment History

### 14.3 Teacher APIs (Planned)
- [ ] Teacher Profile
- [ ] Assigned Batches
- [ ] Salary Information
- [ ] Attendance

### 14.4 Batch APIs (Planned)
- [ ] Batch Listing
- [ ] Batch Details
- [ ] Students in Batch
- [ ] Batch Schedule

### 14.5 Other APIs (Planned)
- [ ] Attendance Management
- [ ] Payment Processing
- [ ] Dashboard Stats
- [ ] Reports

---

## 15. Additional Features

### 15.1 Multi-language Support
- [x] Language Switching
- [x] Localization

### 15.2 Media Management
- [x] Image Upload
- [x] File Storage
- [x] Image Optimization

### 15.3 Dashboard Widgets
- [x] Custom Widget Configuration
- [x] Widget Visibility Settings

---

## Feature Statistics

| Category | Total Features | Completed | Pending |
|----------|-------------|-----------|----------|
| User Management | 12 | 12 | 0 |
| Academic | 15 | 15 | 0 |
| Student | 14 | 14 | 0 |
| Teacher | 8 | 8 | 0 |
| Batch | 12 | 12 | 0 |
| Attendance | 6 | 6 | 0 |
| Payment | 12 | 12 | 0 |
| Financial | 16 | 16 | 0 |
| Reports | 8 | 8 | 0 |
| API | 50+ | 5 | 45+ |
| **Total** | **153+** | **108+** | **45+** |

---

## Technology Stack

- **Backend:** Laravel 10
- **Frontend:** Bootstrap 5 + Custom CSS
- **Database:** MySQL/MariaDB
- **Auth:** Laravel Breeze/ Sanctum
- **API:** Laravel Sanctum
- **File Storage:** Laravel File Storage

---

## License

MIT License