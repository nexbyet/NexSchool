<?php

// NexSchool - API School Setting Controller
// Mobile app માટે શાળા માહિતી API
// Singleton (id=1), ફક્ત એડમિન જ update કરી શકે

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolSettingController extends Controller
{
    // GET /api/school-settings - શાળાની માહિતી મેળવો (public: any user/device can read)
    public function show()
    {
        $setting = SchoolSetting::first();
        if (!$setting) {
            return response()->json(['message' => 'શાળાની માહિતી હજી ઉમેરાઈ નથી.'], 404);
        }
        return response()->json($setting);
    }

    // PUT /api/school-settings - શાળાની માહિતી અપડેટ કરો (only admin)
    public function update(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'ફક્ત એડમિન જ માહિતી બદલી શકે છે.'], 403);
        }

        $validated = $request->validate([
            'school_name_gu' => 'sometimes|string|max:255',
            'school_name_en' => 'sometimes|string|max:255',
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

        return response()->json($setting);
    }

    // POST /api/school-settings/logo - લોગો અપલોડ કરો (only admin)
    public function uploadLogo(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'ફક્ત એડમિન જ લોગો બદલી શકે છે.'], 403);
        }

        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $setting = SchoolSetting::firstOrNew(['id' => 1]);
        if ($setting->logo) Storage::disk('public')->delete($setting->logo);
        $setting->logo = $request->file('logo')->store('logos', 'public');
        $setting->save();

        return response()->json([
            'message' => 'લોગો સફળતાપૂર્વક અપલોડ થયો.',
            'logo' => $setting->logo,
            'logo_url' => asset('storage/' . $setting->logo),
        ]);
    }
}
