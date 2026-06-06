<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class DropdownOptionController extends Controller
{
    public function index()
    {
        $groups = [
            'gender_options' => 'લિંગ વિકલ્પો',
            'blood_group_options' => 'રક્ત જૂથ વિકલ્પો',
        ];
        $settings = [];
        foreach ($groups as $key => $label) {
            $val = Setting::getValue($key);
            $settings[$key] = ['label' => $label, 'value' => $val ? json_decode($val, true) : []];
        }
        return view('settings.dropdowns', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'key' => 'required|string',
            'options' => 'required|array',
            'options.*.label' => 'required|string|max:255',
            'options.*.value' => 'required|string|max:255',
        ]);

        Setting::setValue($data['key'], json_encode($data['options'], JSON_UNESCAPED_UNICODE), 'admission');

        return response()->json(['success' => true, 'message' => 'વિકલ્પો સચવાયા.']);
    }
}
