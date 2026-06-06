<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;

class RollNumberSortController extends Controller
{
    public function index()
    {
        $setting = SchoolSetting::find(1);
        return response()->json([
            'sort_fields' => $setting?->student_default_sort ?? ['name_en', 'father_name_en', 'surname_en'],
            'available_fields' => [
                ['key' => 'name_gu', 'label_gu' => 'નામ (ગુજરાતી)', 'label_en' => 'Name (Gujarati)'],
                ['key' => 'name_en', 'label_gu' => 'નામ (English)', 'label_en' => 'Name (English)'],
                ['key' => 'father_name_gu', 'label_gu' => 'પિતાનું નામ (ગુજરાતી)', 'label_en' => "Father's Name (Gujarati)"],
                ['key' => 'father_name_en', 'label_gu' => 'પિતાનું નામ (English)', 'label_en' => "Father's Name (English)"],
                ['key' => 'surname_gu', 'label_gu' => 'અટક (ગુજરાતી)', 'label_en' => 'Surname (Gujarati)'],
                ['key' => 'surname_en', 'label_gu' => 'અટક (English)', 'label_en' => 'Surname (English)'],
                ['key' => 'sharirik_jaati', 'label_gu' => 'કુમાર/કન્યા', 'label_en' => 'Gender'],
                ['key' => 'gr_number', 'label_gu' => 'GR નંબર', 'label_en' => 'GR Number'],
                ['key' => 'date_of_birth', 'label_gu' => 'જન્મ તારીખ', 'label_en' => 'Date of Birth'],
            ],
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'fields' => 'required|array|min:1',
            'fields.*' => 'required|string|in:name_gu,name_en,father_name_gu,father_name_en,surname_gu,surname_en,sharirik_jaati,gr_number,date_of_birth',
        ]);

        $setting = SchoolSetting::find(1);
        if (!$setting) {
            return response()->json(['success' => false, 'message' => 'School settings not found.'], 404);
        }

        $setting->student_default_sort = $request->fields;
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => 'Roll number sort order updated.',
            'sort_fields' => $setting->student_default_sort,
        ]);
    }
}