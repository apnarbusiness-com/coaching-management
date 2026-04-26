<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $loginValue = $request->username;

        $user = User::where('email', $loginValue)
            ->orWhere('user_name', $loginValue)
            ->orWhere('admission_id', $loginValue)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'code' => Response::HTTP_UNAUTHORIZED,
                'message' => 'Invalid credentials',
                'errors' => [
                    'username' => ['Invalid username or password'],
                ],
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        $roles = $user->roles()->pluck('title')->toArray();

        return response()->json([
            'code' => Response::HTTP_OK,
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'admission_id' => $user->admission_id,
                    'roles' => $roles,
                    'is_admin' => $user->isAdmin(),
                    'is_teacher' => $user->isTeacher(),
                    'is_student' => $user->isStudent(),
                ],
            ],
        ], JsonResponse::HTTP_OK);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'code' => Response::HTTP_OK,
            'message' => 'Logout successful',
        ], JsonResponse::HTTP_OK);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        $roles = $user->roles()->pluck('title')->toArray();

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'admission_id' => $user->admission_id,
            'roles' => $roles,
            'is_admin' => $user->isAdmin(),
            'is_teacher' => $user->isTeacher(),
            'is_student' => $user->isStudent(),
        ];

        if ($user->isStudent() && $user->student) {
            $userData['student'] = [
                'id' => $user->student->id,
                'first_name' => $user->student->first_name,
                'last_name' => $user->student->last_name,
                'id_no' => $user->student->id_no,
                'gender' => $user->student->gender,
                'contact_number' => $user->student->contact_number,
                'email' => $user->student->email,
                'dob' => $user->student->dob,
                'status' => $user->student->status,
                'joining_date' => $user->student->joining_date,
                'image' => $user->student->image,
            ];
        }

        if ($user->isTeacher() && $user->teacher) {
            $userData['teacher'] = [
                'id' => $user->teacher->id,
                'name' => $user->teacher->name,
                'emloyee_code' => $user->teacher->emloyee_code,
                'phone' => $user->teacher->phone,
                'email' => $user->teacher->email,
                'gender' => $user->teacher->gender,
                'joining_date' => $user->teacher->joining_date,
                'status' => $user->teacher->status,
                'profile_img' => $user->teacher->profile_img,
            ];
        }

        return response()->json([
            'code' => Response::HTTP_OK,
            'message' => 'User data retrieved successfully',
            'data' => $userData,
        ], JsonResponse::HTTP_OK);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only(['name', 'email']));

        return response()->json([
            'code' => Response::HTTP_OK,
            'message' => 'Profile updated successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'admission_id' => $user->admission_id,
            ],
        ], JsonResponse::HTTP_OK);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'code' => Response::HTTP_UNAUTHORIZED,
                'message' => 'The current password is incorrect.',
                'errors' => [
                    'current_password' => ['The current password is incorrect.'],
                ],
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        $user->tokens()->delete();

        return response()->json([
            'code' => Response::HTTP_OK,
            'message' => 'Password changed successfully. Please login again.',
        ], JsonResponse::HTTP_OK);
    }
}
