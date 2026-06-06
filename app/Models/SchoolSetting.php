<?php

// NexSchool - SchoolSetting Model
// શાળાની માહિતી (Singleton: only one row)
// Settings: logo, name (gu/en), management, address, UID, contact, social

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = [
        'school_name_gu', 'school_name_en',
        'management_name_gu', 'management_name_en',
        'address', 'grant_number', 'grant_date',
        'uid_number', 'email', 'mobile', 'whatsapp',
        'facebook', 'instagram', 'youtube', 'website',
        'logo', 'student_default_sort',
        'favicon', 'backend_favicon', 'footer_credits', 'copyright_text',
        'license_key', 'licensee_name', 'licensed_until', 'license_status', 'last_license_ping',
        'app_version',
    ];

    protected function casts(): array
    {
        return [
            'student_default_sort' => 'array',
        ];
    }

    public static function getDefaultSort(): array
    {
        $setting = static::find(1);
        return $setting?->student_default_sort ?? ['name_en', 'father_name_en', 'surname_en'];
    }

    public static function getSortColumns(): array
    {
        $fields = static::getDefaultSort();
        $map = [
            'name_gu'       => 'student_name_gu',
            'name_en'       => 'student_name_en',
            'father_name_gu' => 'father_name_gu',
            'father_name_en' => 'father_name_en',
            'surname_gu'    => 'surname_gu',
            'surname_en'    => 'surname_en',
            'sharirik_jaati' => 'sharirik_jaati',
            'gr_number'     => 'gr_number',
            'date_of_birth' => 'date_of_birth',
        ];
        return array_map(fn($f) => $map[$f] ?? $f, $fields);
    }
}
