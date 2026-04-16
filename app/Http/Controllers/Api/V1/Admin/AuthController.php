<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'admission_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('admission_id', $request->admission_id)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'admission_id' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'admission_id' => $user->admission_id,
                    'is_admin' => $user->isAdmin(),
                    'is_teacher' => $user->isTeacher(),
                    'is_student' => $user->isStudent(),
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'admission_id' => $user->admission_id,
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
            'success' => true,
            'message' => 'User profile retrieved successfully',
            'data' => $userData,
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,'.$user->id,
        ]);

        $user->update($request->only(['name', 'email']));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'admission_id' => $user->admission_id,
            ],
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully. Please login again.',
        ]);
    }
}
