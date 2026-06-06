<?php

namespace App\Http\Controllers;

use App\Models\AdmissionInquiry;
use App\Models\SchoolSetting;
use App\Models\Setting;
use App\Models\Standard;
use Illuminate\Http\Request;

class FrontsiteAdmissionController extends Controller
{
    public function form()
    {
        $school = SchoolSetting::find(1);
        $standards = Standard::orderBy('sort_order')->get();
        $genders = json_decode(Setting::getValue('gender_options', '[]'), true);
        return view('frontsite.admission-form', compact('school', 'standards', 'genders'));
    }

    public function submit(Request $request)
    {
        $genderValues = collect(json_decode(Setting::getValue('gender_options', '[]'), true))->pluck('value')->implode(',');
        $genderRule = 'required|in:' . ($genderValues ?: 'kumar,kumari');

        $data = $request->validate([
            'first_name_gu' => 'required|string|max:255',
            'father_name_gu' => 'required|string|max:255',
            'surname_gu' => 'nullable|string|max:255',
            'first_name_en' => 'nullable|string|max:255',
            'father_name_en' => 'nullable|string|max:255',
            'surname_en' => 'nullable|string|max:255',
            'gender' => $genderRule,
            'date_of_birth' => 'required|date|before:today',
            'standard_applied_for' => 'required|string|max:50',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'previous_school' => 'nullable|string|max:255',
        ]);

        $inquiry = AdmissionInquiry::create($data);

        return response()->json([
            'success' => true,
            'message' => 'તમારી અરજી સફળતાપૂર્વક સબમિટ થઈ ગઈ છે. શાળા તરફથી જલ્દી જ સંપર્ક કરવામાં આવશે.',
        ]);
    }
}
