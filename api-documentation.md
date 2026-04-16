# API Documentation

## Dev Coaching Management API

Base URL: `http://your-domain.com/api`

---

## Authentication

### 1. Login

Login to get access token.

**Endpoint:** `POST /api/v1/auth/login`

**Request Body:**

```json
{
  "admission_id": "string (required)",
  "password": "string (required)"
}
```

**Example Request:**

```bash
curl -X POST http://your-domain.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "admission_id": "STU-001",
    "password": "password123"
  }'
```

**Success Response (200):**

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "admission_id": "STU-001",
      "is_admin": false,
      "is_teacher": false,
      "is_student": true
    },
    "token": "1|ABCdefGHIjklMNOpQRstUVwxyz1234567890",
    "token_type": "Bearer"
  }
}
```

**Error Response (401):**

```json
{
  "success": false,
  "message": "The provided credentials are incorrect.",
  "errors": {
    "admission_id": [
      "The provided credentials are incorrect."
    ]
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
| Authorization | Bearer {token} |

**Example Request:**

```bash
curl -X POST http://your-domain.com/api/v1/auth/logout \
  -H "Authorization: Bearer 1|ABCdefGHIjklMNOpQRstUVwxyz1234567890"
```

**Success Response (200):**

```json
{
  "success": true,
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
| Authorization | Bearer {token} |

**Example Request:**

```bash
curl -X GET http://your-domain.com/api/v1/auth/me \
  -H "Authorization: Bearer 1|ABCdefGHIjklMNOpQRstUVwxyz1234567890"
```

**Success Response (200):**

```json
{
  "success": true,
  "message": "User profile retrieved successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "admission_id": "STU-001",
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
      "image": {
        "name": "photo.jpg",
        "url": "http://your-domain.com/media/1/photo.jpg",
        "thumbnail": "http://your-domain.com/media/1/conversions/photo-thumb.jpg",
        "preview": "http://your-domain.com/media/1/conversions/photo-preview.jpg"
      }
    }
  }
}
```

**Student Profile Response (example):**

```json
{
  "success": true,
  "message": "User profile retrieved successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "admission_id": "STU-001",
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

**Teacher Profile Response (example):**

```json
{
  "success": true,
  "message": "User profile retrieved successfully",
  "data": {
    "id": 2,
    "name": "Jane Smith",
    "email": "jane@example.com",
    "admission_id": "TCH-001",
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
      "profile_img": {
        "name": "profile.jpg",
        "url": "http://your-domain.com/media/1/profile.jpg",
        "thumbnail": "http://your-domain.com/media/1/conversions/profile-thumb.jpg",
        "preview": "http://your-domain.com/media/1/conversions/profile-preview.jpg"
      }
    }
  }
}
```

**Error Response (401):**

```json
{
  "success": false,
  "message": "Unauthenticated.",
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
| Authorization | Bearer {token} |
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
  -H "Authorization: Bearer 1|ABCdefGHIjklMNOpQRstUVwxyz1234567890" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Updated",
    "email": "newemail@example.com"
  }'
```

**Success Response (200):**

```json
{
  "success": true,
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
  "success": false,
  "message": "The email has already been taken.",
  "errors": {
    "email": [
      "The email has already been taken."
    ]
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
| Authorization | Bearer {token} |
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
  -H "Authorization: Bearer 1|ABCdefGHIjklMNOpQRstUVwxyz1234567890" \
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
  "success": true,
  "message": "Password changed successfully. Please login again."
}
```

**Error Response (422):**

```json
{
  "success": false,
  "message": "The current password is incorrect.",
  "errors": {
    "current_password": [
      "The current password is incorrect."
    ]
  }
}
```

---

## Response Formats

### Success Response

```json
{
  "success": true,
  "message": "Success message here",
  "data": { }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Error message here",
  "errors": {
    "field_name": [
      "Validation error message"
    ]
  }
}
```

---

## Authentication Flow

1. **Login**: Call `POST /api/v1/auth/login` with `admission_id` and `password`
2. **Get Token**: Extract `token` from the response
3. **Use Token**: Include `Authorization: Bearer {token}` in all subsequent requests
4. **Logout**: Call `POST /api/v1/auth/logout` to invalidate the token

---

## Roles

The API returns role information in the user object:

| Field | Type | Description |
|-------|------|-------------|
| `is_admin` | boolean | True if user has Admin role |
| `is_teacher` | boolean | True if user has Teacher role |
| `is_student` | boolean | True if user has Student role |

---

## Notes

- All protected endpoints require a valid Bearer token in the Authorization header
- Token expiration is handled by Laravel Sanctum (default: no expiration, invalidated on logout)
- Student login uses `admission_id` from `student_basic_infos` table
- Admin login uses `admission_id` from `users` table