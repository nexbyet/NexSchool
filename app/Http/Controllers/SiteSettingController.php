<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    public function edit()
    {
        $setting = SchoolSetting::first() ?? new SchoolSetting();
        return view('settings.site', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'footer_credits' => 'nullable|string',
            'copyright_text' => 'nullable|string|max:500',
        ]);

        $setting = SchoolSetting::firstOrNew(['id' => 1]);
        $setting->fill($validated);
        $setting->save();

        return response()->json(['success' => true, 'message' => 'સાઇટ સેટિંગ્સ સફળતાપૂર્વક સાચવાઈ.']);
    }

    public function uploadFavicon(Request $request)
    {
        $request->validate([
            'favicon' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,ico|max:1024',
            'type' => 'required|in:frontend,backend',
        ]);

        $setting = SchoolSetting::firstOrNew(['id' => 1]);
        $field = $request->type === 'backend' ? 'backend_favicon' : 'favicon';

        if ($setting->$field) Storage::disk('public')->delete($setting->$field);
        $path = $request->file('favicon')->store('favicons', 'public');
        $setting->$field = $path;
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => 'ફેવિકોન સફળતાપૂર્વક અપલોડ થયો.',
            'url' => asset('storage/' . $path),
        ]);
    }

    public function deleteFavicon(Request $request)
    {
        $request->validate(['type' => 'required|in:frontend,backend']);
        $setting = SchoolSetting::firstOrNew(['id' => 1]);
        $field = $request->type === 'backend' ? 'backend_favicon' : 'favicon';

        if ($setting->$field) Storage::disk('public')->delete($setting->$field);
        $setting->$field = null;
        $setting->save();

        return response()->json(['success' => true, 'message' => 'ફેવિકોન દૂર કરવામાં આવ્યો.']);
    }
}
