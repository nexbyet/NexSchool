<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Illuminate\Http\Request;

class RollNumberSortController extends Controller
{
    public function index()
    {
        $sortFields = SchoolSetting::getDefaultSort();
        $availableFields = [
            ['key' => 'name_gu', 'label_gu' => 'નામ (ગુજરાતી)', 'label_en' => 'Name (Gujarati)'],
            ['key' => 'name_en', 'label_gu' => 'નામ (English)', 'label_en' => 'Name (English)'],
            ['key' => 'father_name_gu', 'label_gu' => 'પિતાનું નામ (ગુજરાતી)', 'label_en' => "Father's Name (Gujarati)"],
            ['key' => 'father_name_en', 'label_gu' => 'પિતાનું નામ (English)', 'label_en' => "Father's Name (English)"],
            ['key' => 'surname_gu', 'label_gu' => 'અટક (ગુજરાતી)', 'label_en' => 'Surname (Gujarati)'],
            ['key' => 'surname_en', 'label_gu' => 'અટક (English)', 'label_en' => 'Surname (English)'],
            ['key' => 'sharirik_jaati', 'label_gu' => 'કુમાર/કન્યા', 'label_en' => 'Gender'],
            ['key' => 'gr_number', 'label_gu' => 'GR નંબર', 'label_en' => 'GR Number'],
            ['key' => 'date_of_birth', 'label_gu' => 'જન્મ તારીખ', 'label_en' => 'Date of Birth'],
        ];
        return view('roll-number-sort.index', compact('sortFields', 'availableFields'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'fields' => 'required|array|min:1',
            'fields.*' => 'required|string',
        ]);

        $setting = SchoolSetting::first();
        if (!$setting) {
            return response()->json(['success' => false, 'message' => 'શાળા સેટિંગ મળી નથી.'], 404);
        }

        $setting->student_default_sort = $request->input('fields');
        $setting->save();

        return response()->json(['success' => true, 'message' => 'રોલ નંબર ગોઠવણી સાચવાઈ.']);
    }
}