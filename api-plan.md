# Mobile App API Plan

## Project Overview
- **Framework**: Laravel 10
- **Purpose**: Coaching center management for mobile app
- **Auth**: Token-based (Laravel Sanctum)

---

## Response Format (REST API Standard)

### Success Response (200, 201)
```json
{
  "code": 200,
  "message": "Login successful",
  "access_token": "token",
  "token_type": "Bearer",
  "data": {}
}
```

### Error Response (400, 401, 422)
```json
{
  "code": 401,
  "message": "Error description",
  "errors": {}
}
```

### Not Found (404)
```json
{
  "code": 404,
  "message": "Resource not found",
  "errors": {}
}
```

---

## 1. Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/auth/login` | Login (username + password) |
| POST | `/api/v1/auth/logout` | Logout |
| GET | `/api/v1/auth/me` | Get current user profile |
| PUT | `/api/v1/auth/profile` | Update profile |
| POST | `/api/v1/auth/change-password` | Change password |

### Login Request
```json
{
  "username": "email or user_name or admission_id",
  "password": "password"
}
```

### Login Validation Error (422)
```json
{
  "code": 422,
  "message": "Validation failed",
  "errors": {
    "username": ["The username field is required."],
    "password": ["The password field is required."]
  }
}
```

### Login Response (200)
```json
{
  "code": 200,
  "message": "Login successful",
  "access_token": "5|CMTDusyiSShAfdFb8RcZjSwSGEXXeYtfWKgsJzdP1957fbe7",
  "token_type": "Bearer",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "admission_id": "AD001",
      "roles": ["Admin", "Teacher"],
      "is_admin": true,
      "is_teacher": true,
      "is_student": false
    }
  }
}
```

### Login Error Response (401)
```json
{
  "code": 401,
  "message": "Invalid credentials",
  "errors": {
    "username": ["Invalid username or password"]
  }
}
```

### Logout Response (200)
```json
{
  "code": 200,
  "message": "Logout successful"
}
```

### Me Response (200)
```json
{
  "code": 200,
  "message": "User data retrieved successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "admission_id": "AD001",
    "roles": ["Admin"],
    "is_admin": true,
    "is_teacher": false,
    "is_student": false,
    "student": null,
    "teacher": null
  }
}
```

### Update Profile Response (200)
```json
{
  "code": 200,
  "message": "Profile updated successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "admission_id": "AD001"
  }
}
```

### Change Password Error Response (401)
```json
{
  "code": 401,
  "message": "The current password is incorrect.",
  "errors": {
    "current_password": ["The current password is incorrect."]
  }
}
```

---

## 2. Students (Student Role)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/students/me` | Current student profile |
| GET | `/api/v1/students/me/batches` | Enrolled batches |
| GET | `/api/v1/students/me/dues` | Monthly dues & payment history |
| GET | `/api/v1/students/me/attendance` | Attendance records |
| GET | `/api/v1/students/me/payments` | Payment history |

---

## 3. Teachers (Teacher Role)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/teachers/me` | Current teacher profile |
| GET | `/api/v1/teachers/me/batches` | Assigned batches |
| GET | `/api/v1/teachers/me/salary` | Salary & payment history |
| GET | `/api/v1/teachers/me/attendance` | Teacher attendance |

---

## 4. Batches

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/batches` | List all batches |
| GET | `/api/v1/batches/{id}` | Batch details |
| GET | `/api/v1/batches/{id}/students` | Students in batch |
| GET | `/api/v1/batches/{id}/schedule` | Batch schedule |
| GET | `/api/v1/batches/{id}/attendance` | Batch attendance records |

---

## 5. Attendance

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/attendance/batch/{batchId}` | Batch attendance |
| POST | `/api/v1/attendance/batch/{batchId}` | Take attendance (Teacher only) |
| GET | `/api/v1/attendance/student/{studentId}` | Student attendance history |

---

## 6. Payments & Dues

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/dues` | All dues (filter by student, month) |
| GET | `/api/v1/dues/{id}` | Due details |
| POST | `/api/v1/dues/{id}/pay` | Record payment |
| GET | `/api/v1/earnings` | Payment records |
| GET | `/api/v1/earnings/student/{studentId}` | Student payments |

---

## 7. Teachers Payments (Admin)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/teachers-payments` | List all payments |
| GET | `/api/v1/teachers-payments/{id}` | Payment details |
| POST | `/api/v1/teachers-payments/generate` | Generate monthly payments |
| POST | `/api/v1/teachers-payments/{id}/pay` | Record salary payment |

---

## 8. Academic Data

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/classes` | List academic classes |
| GET | `/api/v1/sections` | List sections |
| GET | `/api/v1/shifts` | List shifts |
| GET | `/api/v1/subjects` | List subjects |
| GET | `/api/v1/rooms` | List classrooms |

---

## 9. Dashboard (Admin)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/dashboard/stats` | Total students, teachers, earnings |
| GET | `/api/v1/dashboard/recent-payments` | Recent payments |
| GET | `/api/v1/dashboard/due-summary` | Due summary |

---

## 10. Reports

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/reports/students` | Student report |
| GET | `/api/v1/reports/attendance` | Attendance report |
| GET | `/api/v1/reports/earnings` | Earnings report |
| GET | `/api/v1/reports/expenses` | Expenses report |
| GET | `/api/v1/reports/profit-loss` | Profit/Loss report |

---

## 11. Public Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/admission/apply` | Submit admission application |
| GET | `/api/v1/admission/status/{id}` | Check application status |

---

## Authentication Flow

1. **Login**: `POST /api/v1/auth/login` with `username` and `password`
2. **Response**: Returns access token with user data and roles
3. **Protected Routes**: Use `Authorization: Bearer {access_token}` header

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

---

## HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | OK - Success |
| 201 | Created - Resource created |
| 400 | Bad Request - Invalid input |
| 401 | Unauthorized - Invalid credentials |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation failed |
| 500 | Server Error - Internal error |