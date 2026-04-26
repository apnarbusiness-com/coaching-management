# API Documentation

## Dev Coaching Management API

Base URL: `http://your-domain.com/api`

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

### 1. Login

Login to get access token.

**Endpoint:** `POST /api/v1/auth/login`

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

### 2. Logout

Logout and invalidate the current token.

**Endpoint:** `POST /api/v1/auth/logout`

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

### 3. Get Current User

Get the currently authenticated user profile.

**Endpoint:** `GET /api/v1/auth/me`

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

### 4. Update Profile

Update the authenticated user's profile information.

**Endpoint:** `PUT /api/v1/auth/profile`

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

### 5. Change Password

Change the authenticated user's password.

**Endpoint:** `POST /api/v1/auth/change-password`

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

## Authentication Flow

1. **Login**: Call `POST /api/v1/auth/login` with `username` and `password`
2. **Get Token**: Extract `access_token` from the response
3. **Use Token**: Include `Authorization: Bearer {access_token}` in all subsequent requests
4. **Logout**: Call `POST /api/v1/auth/logout` to invalidate the token

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