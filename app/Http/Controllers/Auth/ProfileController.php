<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    public function edit()
    {
        abort_if(Gate::denies('profile_password_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = auth()->user();

        return view('auth.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        abort_if(Gate::denies('profile_password_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = auth()->user();
        $user->update($request->only('name', 'email'));

        if ($request->hasFile('photo')) {
            $user->clearMediaCollection('profile_img');
            $user->addMedia($request->file('photo'))->preservingOriginal()->toMediaCollection('profile_img');

            if ($user->teacher) {
                $user->teacher->clearMediaCollection('profile_img');
                $user->teacher->addMedia($request->file('photo'))->toMediaCollection('profile_img');
            }

            if ($user->student) {
                $user->student->clearMediaCollection('image');
                $user->student->addMedia($request->file('photo'))->toMediaCollection('image');
            }
        }

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
}
