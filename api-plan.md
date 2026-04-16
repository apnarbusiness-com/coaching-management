# Mobile App API Plan

## Project Overview
- **Framework**: Laravel 10
- **Purpose**: Coaching center management for mobile app
- **Auth**: Token-based (Sanctum)

---

## 1. Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/login` | Login (admission_id + password) |
| POST | `/api/auth/logout` | Logout |
| GET | `/api/auth/me` | Get current user profile |
| PUT | `/api/auth/profile` | Update profile |
| POST | `/api/auth/change-password` | Change password |

---

## 2. Students (Student Role)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/students/me` | Current student profile |
| GET | `/api/students/me/batches` | Enrolled batches |
| GET | `/api/students/me/due` | Monthly dues & payment history |
| GET | `/api/students/me/attendance` | Attendance records |
| GET | `/api/students/me/payments` | Payment history |

---

## 3. Teachers (Teacher Role)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/teachers/me` | Current teacher profile |
| GET | `/api/teachers/me/batches` | Assigned batches |
| GET | `/api/teachers/me/salary` | Salary & payment history |
| GET | `/api/teachers/me/attendance` | Teacher attendance |

---

## 4. Batches

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/batches` | List all batches |
| GET | `/api/batches/{id}` | Batch details |
| GET | `/api/batches/{id}/students` | Students in batch |
| GET | `/api/batches/{id}/schedule` | Batch schedule |
| GET | `/api/batches/{id}/attendance` | Batch attendance records |

---

## 5. Attendance

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/attendance/batch/{batchId}` | Batch attendance |
| POST | `/api/attendance/batch/{batchId}` | Take attendance (Teacher only) |
| GET | `/api/attendance/student/{studentId}` | Student attendance history |

---

## 6. Payments & Dues

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/dues` | All dues (filter by student, month) |
| GET | `/api/dues/{id}` | Due details |
| POST | `/api/dues/{id}/pay` | Record payment |
| GET | `/api/earnings` | Payment records |
| GET | `/api/earnings/student/{studentId}` | Student payments |

---

## 7. Teachers Payments (Admin)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/teachers-payments` | List all payments |
| GET | `/api/teachers-payments/{id}` | Payment details |
| POST | `/api/teachers-payments/generate` | Generate monthly payments |
| POST | `/api/teachers-payments/{id}/pay` | Record salary payment |

---

## 8. Academic Data

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/classes` | List academic classes |
| GET | `/api/sections` | List sections |
| GET | `/api/shifts` | List shifts |
| GET | `/api/subjects` | List subjects |
| GET | `/api/rooms` | List classrooms |

---

## 9. Dashboard (Admin)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/dashboard/stats` | Total students, teachers, earnings |
| GET | `/api/dashboard/recent-payments` | Recent payments |
| GET | `/api/dashboard/due-summary` | Due summary |

---

## 10. Reports

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/reports/students` | Student report |
| GET | `/api/reports/attendance` | Attendance report |
| GET | `/api/reports/earnings` | Earnings report |
| GET | `/api/reports/expenses` | Expenses report |
| GET | `/api/reports/profit-loss` | Profit/Loss report |

---

## 11. Public Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/admission/apply` | Submit admission application |
| GET | `/api/admission/status/{id}` | Check application status |

---

## Authentication Flow

1. **Login**: `POST /api/auth/login` with `admission_id` and `password`
2. **Response**: Returns access token with user role (Student/Teacher/Admin)
3. **Protected Routes**: Use `Authorization: Bearer {token}` header

---

## Response Format

```json
{
  "success": true,
  "data": {},
  "message": "Success"
}
```

```json
{
  "success": false,
  "errors": {}
}
```

---

## Pagination

All list endpoints support:
- `?page=1` - Page number
- `?per_page=15` - Items per page

---

## Filters

Common filters:
- `?status=1` - Active only
- `?month=2026-04` - Filter by month
- `?batch_id=1` - Filter by batch
- `?from_date=2026-01-01&to_date=2026-04-30` - Date range