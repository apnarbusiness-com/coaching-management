# API Documentation

## Dev Coaching Management API

Base URL: `http://your-domain.com/api`

---

## Table of Contents

1. [Response Format](#response-format)
2. [Authentication](#authentication)
   - [1. Login](#1-login)
   - [2. Logout](#2-logout)
   - [3. Get Current User](#3-get-current-user)
   - [4. Update Profile](#4-update-profile)
   - [5. Change Password](#5-change-password)
3. [Teacher Attendance APIs](#teacher-attendance-apis)
   - [1. Get My Batches](#1-get-my-batches)
   - [2. Get Batch Students](#2-get-batch-students)
   - [3. Mark Attendance](#3-mark-attendance)
   - [4. View Attendance](#4-view-attendance)
4. [HTTP Status Codes](#http-status-codes)
5. [Authentication Flow](#authentication-flow)
6. [Notes](#notes)

---

## Response Format

### Success Response (200, 201)
```json
{
  "code": 200,
  "message": "Success message here",
  "access_token": "token",
  "token_type": "Bearer",
  "data": { }
}
```

### Error Response (400, 401, 422)
```json
{
  "code": 401,
  "message": "Error message here",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

---

## Authentication

### 1. Login {#login}

Login to get access token.

**Endpoint:** `POST /api/v1/auth/login`

[Back to top](#table-of-contents)

**Request Body:**

```json
{
  "username": "string (required) - email, user_name, or admission_id",
  "password": "string (required)"
}
```

**Example Request:**

```bash
curl -X POST http://your-domain.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "john@example.com",
    "password": "password123"
  }'
```

**Success Response (200):**

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
      "admission_id": "STU-001",
      "roles": ["Admin", "Student"],
      "is_admin": false,
      "is_teacher": false,
      "is_student": true
    }
  }
}
```

**Validation Error Response (422):**

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

**Error Response (401):**

```json
{
  "code": 401,
  "message": "Invalid credentials",
  "errors": {
    "username": ["Invalid username or password"]
  }
}
```

---

### 2. Logout {#logout}

Logout and invalidate the current token.

**Endpoint:** `POST /api/v1/auth/logout`

[Back to top](#table-of-contents)

**Headers:**

| Key | Value |
|-----|-------|
| Authorization | Bearer {access_token} |

**Example Request:**

```bash
curl -X POST http://your-domain.com/api/v1/auth/logout \
  -H "Authorization: Bearer 5|CMTDusyiSShAfdFb8RcZjSwSGEXXeYtfWKgsJzdP1957fbe7"
```

**Success Response (200):**

```json
{
  "code": 200,
  "message": "Logout successful"
}
```

---

### 3. Get Current User {#me}

Get the currently authenticated user profile.

**Endpoint:** `GET /api/v1/auth/me`

[Back to top](#table-of-contents)

**Headers:**

| Key | Value |
|-----|-------|
| Authorization | Bearer {access_token} |

**Example Request:**

```bash
curl -X GET http://your-domain.com/api/v1/auth/me \
  -H "Authorization: Bearer 5|CMTDusyiSShAfdFb8RcZjSwSGEXXeYtfWKgsJzdP1957fbe7"
```

**Success Response (200) - Student:**

```json
{
  "code": 200,
  "message": "User data retrieved successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "admission_id": "STU-001",
    "roles": ["Student"],
    "is_admin": false,
    "is_teacher": false,
    "is_student": true,
    "student": {
      "id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "id_no": "STU-001",
      "gender": "male",
      "contact_number": "+1234567890",
      "email": "john@example.com",
      "dob": "2010-01-15",
      "status": "1",
      "joining_date": "2025-01-15 10:00:00",
      "image": null
    }
  }
}
```

**Success Response (200) - Teacher:**

```json
{
  "code": 200,
  "message": "User data retrieved successfully",
  "data": {
    "id": 2,
    "name": "Jane Smith",
    "email": "jane@example.com",
    "admission_id": "TCH-001",
    "roles": ["Teacher"],
    "is_admin": false,
    "is_teacher": true,
    "is_student": false,
    "teacher": {
      "id": 1,
      "name": "Jane Smith",
      "emloyee_code": "TCH-001",
      "phone": "+1234567890",
      "email": "jane@example.com",
      "gender": "female",
      "joining_date": "2024-01-01 09:00:00",
      "status": "1",
      "profile_img": null
    }
  }
}
```

**Success Response (200) - Admin:**

```json
{
  "code": 200,
  "message": "User data retrieved successfully",
  "data": {
    "id": 3,
    "name": "Admin User",
    "email": "admin@example.com",
    "admission_id": null,
    "roles": ["Admin", "Manager"],
    "is_admin": true,
    "is_teacher": false,
    "is_student": false,
    "student": null,
    "teacher": null
  }
}
```

**Error Response (401):**

```json
{
  "code": 401,
  "message": "Unauthenticated",
  "errors": {}
}
```

---

### 4. Update Profile {#update-profile}

Update the authenticated user's profile information.

**Endpoint:** `PUT /api/v1/auth/profile`

[Back to top](#table-of-contents)

**Headers:**

| Key | Value |
|-----|-------|
| Authorization | Bearer {access_token} |
| Content-Type | application/json |

**Request Body:**

```json
{
  "name": "string (optional)",
  "email": "string (optional, unique)"
}
```

**Example Request:**

```bash
curl -X PUT http://your-domain.com/api/v1/auth/profile \
  -H "Authorization: Bearer 5|CMTDusyiSShAfdFb8RcZjSwSGEXXeYtfWKgsJzdP1957fbe7" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Updated",
    "email": "newemail@example.com"
  }'
```

**Success Response (200):**

```json
{
  "code": 200,
  "message": "Profile updated successfully",
  "data": {
    "id": 1,
    "name": "John Updated",
    "email": "newemail@example.com",
    "admission_id": "STU-001"
  }
}
```

**Error Response (422):**

```json
{
  "code": 422,
  "message": "The email has already been taken.",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

---

### 5. Change Password {#change-password}

Change the authenticated user's password.

**Endpoint:** `POST /api/v1/auth/change-password`

[Back to top](#table-of-contents)

**Headers:**

| Key | Value |
|-----|-------|
| Authorization | Bearer {access_token} |
| Content-Type | application/json |

**Request Body:**

```json
{
  "current_password": "string (required)",
  "new_password": "string (required, min 8)",
  "new_password_confirmation": "string (required, must match new_password)"
}
```

**Example Request:**

```bash
curl -X POST http://your-domain.com/api/v1/auth/change-password \
  -H "Authorization: Bearer 5|CMTDusyiSShAfdFb8RcZjSwSGEXXeYtfWKgsJzdP1957fbe7" \
  -H "Content-Type: application/json" \
  -d '{
    "current_password": "oldpassword123",
    "new_password": "newpassword456",
    "new_password_confirmation": "newpassword456"
  }'
```

**Success Response (200):**

```json
{
  "code": 200,
  "message": "Password changed successfully. Please login again."
}
```

**Error Response (401):**

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

## Authentication Flow {#authentication-flow}

1. **Login**: Call `POST /api/v1/auth/login` with `username` and `password`
2. **Get Token**: Extract `access_token` from the response
3. **Use Token**: Include `Authorization: Bearer {access_token}` in all subsequent requests
4. **Logout**: Call `POST /api/v1/auth/logout` to invalidate the token

---

[Back to top](#table-of-contents)

---

## HTTP Status Codes {#http-status-codes}

| Code | Description |
|------|-------------|
| 200 | OK - Success |
| 201 | Created - Resource created |
| 400 | Bad Request - Invalid input |
| 401 | Unauthorized - Invalid credentials |
| 403 | Forbidden - Access denied |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation failed |
| 500 | Server Error - Internal error |

---

## Teacher Attendance APIs {#teacher-attendance-apis}

These endpoints allow teachers to mark and view student attendance for their assigned batches.

### 1. Get My Batches {#get-my-batches}

Get the list of batches assigned to the logged-in teacher.

**Endpoint:** `GET /api/v1/teachers/me/batches`

**Headers:**
| Key | Value |
|-----|-------|
| Authorization | Bearer {access_token} |

**Example Request:**

```bash
curl -X GET http://your-domain.com/api/v1/teachers/me/batches \
  -H "Authorization: Bearer 5|CMTDusyiSShAfdFb8RcZjSwSGEXXeYtfWKgsJzdP1957fbe7"
```

**Success Response (200):**

```json
{
  "code": 200,
  "message": "Batches retrieved successfully",
  "data": [
    {
      "id": 1,
      "batch_name": "ICT-8",
      "subject": "ICT",
      "students_count": 20
    },
    {
      "id": 2,
      "batch_name": "ICT-9",
      "subject": "ICT",
      "students_count": 15
    }
  ]
}
```

**Error Response (403):**

```json
{
  "code": 403,
  "message": "Only teachers can access this resource",
  "errors": {}
}
```

---

### 2. Get Batch Students {#get-batch-students}

Get students in a specific batch for marking attendance.

**Endpoint:** `GET /api/v1/batches/{id}/students`

**Headers:**
| Key | Value |
|-----|-------|
| Authorization | Bearer {access_token} |

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| date | string | No | Date (default: today), format: Y-m-d |

**Example Request:**

```bash
curl -X GET "http://your-domain.com/api/v1/batches/1/students?date=2026-04-26" \
  -H "Authorization: Bearer 5|CMTDusyiSShAfdFb8RcZjSwSGEXXeYtfWKgsJzdP1957fbe7"
```

**Success Response (200):**

```json
{
  "code": 200,
  "message": "Students retrieved successfully",
  "data": {
    "batch": {
      "id": 1,
      "batch_name": "ICT-8"
    },
    "date": "2026-04-26",
    "students": [
      {
        "id": 1,
        "name": "Rahim",
        "roll": 1,
        "id_no": "STU-001",
        "image": null,
        "status": "present",
        "due_amount": 0,
        "has_due": false
      },
      {
        "id": 2,
        "name": "Karim",
        "roll": 2,
        "id_no": "STU-002",
        "image": null,
        "status": null,
        "due_amount": 500,
        "has_due": true
      }
    ]
  }
}
```

**Error Response (404):**

```json
{
  "code": 404,
  "message": "Batch not found or not assigned to you",
  "errors": {}
}
```

---

### 3. Mark Attendance {#mark-attendance}

Mark or update attendance for students in a batch.

**Endpoint:** `POST /api/v1/attendance/batch/{id}`

**Headers:**
| Key | Value |
|-----|-------|
| Authorization | Bearer {access_token} |
| Content-Type | application/json |

**Request Body:**

```json
{
  "date": "2026-04-26",
  "attendance": {
    "1": "present",
    "2": "absent",
    "3": "late"
  }
}
```

**Rules:**
- `date`: Required, must be today or up to 30 days back
- `attendance`: Required, object with student_id => status
- `status`: Must be one of: `present`, `absent`, `late`

**Example Request:**

```bash
curl -X POST http://your-domain.com/api/v1/attendance/batch/1 \
  -H "Authorization: Bearer 5|CMTDusyiSShAfdFb8RcZjSwSGEXXeYtfWKgsJzdP1957fbe7" \
  -H "Content-Type: application/json" \
  -d '{
    "date": "2026-04-26",
    "attendance": {
      "1": "present",
      "2": "absent",
      "3": "late"
    }
  }'
```

**Success Response (200):**

```json
{
  "code": 200,
  "message": "Attendance saved successfully",
  "data": {
    "marked_count": 3,
    "date": "2026-04-26"
  }
}
```

**Error Response (422) - Date Validation:**

```json
{
  "code": 422,
  "message": "Attendance can only be marked for today or up to 30 days back",
  "errors": {
    "date": ["Date must be within the last 30 days"]
  }
}
```

**Error Response (422) - Invalid Status:**

```json
{
  "code": 422,
  "message": "Validation failed",
  "errors": {
    "attendance.1": ["The selected attendance.1 is invalid."]
  }
}
```

---

### 4. View Attendance {#view-attendance}

View attendance records for a batch (single date or date range).

**Endpoint:** `GET /api/v1/attendance/batch/{id}`

**Headers:**
| Key | Value |
|-----|-------|
| Authorization | Bearer {access_token} |

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| date | string | No* | Single date, format: Y-m-d |
| start_date | string | No* | Start date for range |
| end_date | string | No* | End date for range |

*Either `date` OR `start_date` + `end_date` is required

**Example Request - Single Date:**

```bash
curl -X GET "http://your-domain.com/api/v1/attendance/batch/1?date=2026-04-26" \
  -H "Authorization: Bearer 5|CMTDusyiSShAfdFb8RcZjSwSGEXXeYtfWKgsJzdP1957fbe7"
```

**Success Response (200) - Single Date:**

```json
{
  "code": 200,
  "message": "Attendance retrieved successfully",
  "data": {
    "date": "2026-04-26",
    "attendances": [
      {
        "student_id": 1,
        "student_name": "Rahim",
        "status": "present",
        "attendance_date": "2026-04-26"
      },
      {
        "student_id": 2,
        "student_name": "Karim",
        "status": "absent",
        "attendance_date": "2026-04-26"
      }
    ]
  }
}
```

**Example Request - Date Range:**

```bash
curl -X GET "http://your-domain.com/api/v1/attendance/batch/1?start_date=2026-04-01&end_date=2026-04-30" \
  -H "Authorization: Bearer 5|CMTDusyiSShAfdFb8RcZjSwSGEXXeYtfWKgsJzdP1957fbe7"
```

**Success Response (200) - Date Range:**

```json
{
  "code": 200,
  "message": "Attendance retrieved successfully",
  "data": {
    "start_date": "2026-04-01",
    "end_date": "2026-04-30",
    "summary": [
      {
        "date": "2026-04-01",
        "present": 18,
        "absent": 2,
        "late": 0
      },
      {
        "date": "2026-04-02",
        "present": 15,
        "absent": 3,
        "late": 2
      }
    ]
  }
}
```

**Error Response (422) - Missing Parameters:**

```json
{
  "code": 422,
  "message": "Please provide date or date range",
  "errors": {
    "date": ["Required: ?date=2026-04-26"],
    "start_date": ["Optional: ?start_date=2026-04-01&end_date=2026-04-30"]
  }
}
```

---

## Notes {#notes}

- Teacher attendance APIs use the same `BatchAttendance` table as the web panel
- Teachers can only access batches assigned to them
- Same-day attendance can be updated (uses upsert)
- Only 30 days of backdated attendance allowed
- Student due information is included in the student list

---

## Roles

The API returns role information in two ways:

1. **roles array** - Contains all role titles
2. **boolean flags** - Quick role checks

| Field | Type | Description |
|-------|------|-------------|
| `roles` | array | All role titles e.g. ["Admin", "Teacher"] |
| `is_admin` | boolean | True if user has Admin role |
| `is_teacher` | boolean | True if user has Teacher role |
| `is_student` | boolean | True if user has Student role |

---

## User Types

Based on roles, user can have additional data:

| User Type | Additional Data |
|----------|----------------|
| Student | `student` object with profile details |
| Teacher | `teacher` object with profile details |
| Admin | No additional data |

---

## Notes

- All protected endpoints require a valid Bearer token in the Authorization header
- Token expiration is handled by Laravel Sanctum (default: no expiration, invalidated on logout)
- Login accepts any of: `email`, `user_name`, or `admission_id` as username
- The `roles` array can contain multiple roles for a user

---

[Back to top](#table-of-contents)