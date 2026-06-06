<?php

// NexSchool - School Setting Web Controller
// શાળા માહિતી ફોર્મ: ફક્ત એડમિન જ જોઈ/બદલી શકે
// Singleton: એક જ રેકોર્ડ (id=1)

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolSettingController extends Controller
{
    // GET: સેટિંગ્સ ફોર્મ બતાવો
    public function edit()
    {
        $setting = SchoolSetting::first() ?? new SchoolSetting();
        return view('settings.school-info', compact('setting'));
    }

    // POST/PUT: સેટિંગ્સ સાચવો/અપડેટ કરો (text only — logo is separate)
    public function update(Request $request)
    {
        $validated = $request->validate([
            'school_name_gu' => 'required|string|max:255',
            'school_name_en' => 'required|string|max:255',
            'management_name_gu' => 'nullable|string|max:255',
            'management_name_en' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'grant_number' => 'nullable|string|max:50',
            'grant_date' => 'nullable|date',
            'uid_number' => 'nullable|string|size:11',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
        ]);

        $setting = SchoolSetting::firstOrNew(['id' => 1]);
        $setting->fill($validated);
        $setting->save();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'શાળાની માહિતી સફળતાપૂર્વક સાચવાઈ.']);
        }

        return redirect()->route('settings.school-info')
            ->with('success', 'શાળાની માહિતી સફળતાપૂર્વક સાચવાઈ.');
    }

    // POST: લોગો અલગથી અપલોડ કરો (AJAX — direct upload on file select)
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $setting = SchoolSetting::firstOrNew(['id' => 1]);
        if ($setting->logo) Storage::disk('public')->delete($setting->logo);
        $setting->logo = $request->file('logo')->store('logos', 'public');
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => 'લોગો સફળતાપૂર્વક અપલોડ થયો.',
            'logo_url' => asset('storage/' . $setting->logo),
        ]);
    }
}
