<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class GeneralSettingController extends Controller
{
    public function edit()
    {
        abort_if(Gate::denies('general_setting_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $settings = Setting::pluck('value', 'key')->toArray();

        return view('admin.generalSettings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        abort_if(Gate::denies('general_setting_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'site_title' => 'nullable|string|max:255',
            'institute_name' => 'nullable|string|max:255',
            'institute_address' => 'nullable|string|max:500',
            'institute_phone' => 'nullable|string|max:50',
            'site_logo' => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
            'site_favicon' => 'nullable|image|mimes:jpg,jpeg,png,svg,webp,ico|max:1024',
        ]);

        $textFields = ['site_title', 'institute_name', 'institute_address', 'institute_phone'];
        foreach ($textFields as $field) {
            if ($request->filled($field)) {
                Setting::updateOrCreate(['key' => $field], ['value' => $request->input($field)]);
            }
        }

        $uploadPath = public_path('uploads/settings');

        if ($request->hasFile('site_logo')) {
            $oldLogo = Setting::where('key', 'site_logo')->value('value');
            if ($oldLogo && file_exists($uploadPath . '/' . $oldLogo)) {
                @unlink($uploadPath . '/' . $oldLogo);
            }
            $file = $request->file('site_logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            Setting::updateOrCreate(['key' => 'site_logo'], ['value' => $filename]);
        }

        if ($request->hasFile('site_favicon')) {
            $oldFavicon = Setting::where('key', 'site_favicon')->value('value');
            if ($oldFavicon && file_exists($uploadPath . '/' . $oldFavicon)) {
                @unlink($uploadPath . '/' . $oldFavicon);
            }
            $file = $request->file('site_favicon');
            $filename = 'favicon_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            Setting::updateOrCreate(['key' => 'site_favicon'], ['value' => $filename]);
        }

        if ($request->has('remove_logo')) {
            $oldLogo = Setting::where('key', 'site_logo')->value('value');
            if ($oldLogo && file_exists($uploadPath . '/' . $oldLogo)) {
                @unlink($uploadPath . '/' . $oldLogo);
            }
            Setting::updateOrCreate(['key' => 'site_logo'], ['value' => null]);
        }

        if ($request->has('remove_favicon')) {
            $oldFavicon = Setting::where('key', 'site_favicon')->value('value');
            if ($oldFavicon && file_exists($uploadPath . '/' . $oldFavicon)) {
                @unlink($uploadPath . '/' . $oldFavicon);
            }
            Setting::updateOrCreate(['key' => 'site_favicon'], ['value' => null]);
        }

        return redirect()->back()->with('success', 'General settings updated successfully.');
    }
}
