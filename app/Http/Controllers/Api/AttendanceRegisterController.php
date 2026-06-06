<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassTeacher;
use App\Models\PublicHoliday;
use App\Models\SchoolClass;
use App\Models\Standard;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceRegisterController extends Controller
{
    public function standards()
    {
        $standards = Standard::with(['classes' => function ($q) {
            $q->where('status', 'active')->orderBy('sort_order');
        }])->orderBy('sort_order')->get();

        return response()->json($standards);
    }

    public function classes($standardId)
    {
        $classes = SchoolClass::where('standard_id', $standardId)
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get(['id', 'name']);

        return response()->json($classes);
    }

    public function show(Request $request)
    {
        $data = $request->validate([
            'standard_id' => 'required|exists:standards,id',
            'class_id' => 'required|exists:school_classes,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2100',
            'academic_year_id' => 'required|exists:academic_years,id',
            'lang' => 'nullable|in:gu,en',
            'type' => 'nullable|in:blank,filled',
        ]);

        $lang = $data['lang'] ?? 'gu';
        $type = $data['type'] ?? 'blank';
        $standard = Standard::findOrFail($data['standard_id']);
        $class = SchoolClass::findOrFail($data['class_id']);
        $academicYear = AcademicYear::findOrFail($data['academic_year_id']);

        $classTeacher = ClassTeacher::where('school_class_id', $class->id)
            ->where('academic_year_id', $academicYear->id)
            ->with('teacher')
            ->first();

        $monthStart = Carbon::create($data['year'], $data['month'], 1);
        $monthEnd = $monthStart->copy()->endOfMonth();

        $students = Student::where('current_standard_id', $standard->id)
            ->where('current_class_id', $class->id)
            ->where('status', 'active')
            ->where('date_of_admission', '<=', $monthEnd)
            ->where(function ($q) use ($monthStart) {
                $q->whereNull('leaving_date')->orWhere('leaving_date', '>=', $monthStart);
            })
            ->orderBy('gr_number')
            ->get(['id', 'gr_number', 'student_name_gu', 'student_name_en',
                'sharirik_jaati', 'date_of_birth', 'category_gu', 'category_en']);

        $holidays = PublicHoliday::where('academic_year_id', $academicYear->id)
            ->whereYear('date', $data['year'])
            ->whereMonth('date', $data['month'])
            ->get(['name', 'type', 'date'])
            ->keyBy(fn($h) => $h->date->format('Y-m-d'));

        $daysInMonth = $monthStart->daysInMonth;
        $dates = [];
        $dayNamesGu = ['રવિ', 'સોમ', 'મંગળ', 'બુધ', 'ગુરુ', 'શુક્ર', 'શનિ'];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::create($data['year'], $data['month'], $d);
            $dow = $date->dayOfWeek;
            $dateKey = $date->format('Y-m-d');
            $dates[] = [
                'day' => $d,
                'date' => $dateKey,
                'dayName' => $lang === 'gu' ? $dayNamesGu[$dow] : $date->format('D'),
                'isSunday' => $dow === 0,
                'isHoliday' => isset($holidays[$dateKey]),
                'holidayName' => $holidays[$dateKey]->name ?? null,
                'holidayType' => $holidays[$dateKey]->type ?? null,
            ];
        }

        $monthNamesGu = [
            1 => 'જાન્યુઆરી', 2 => 'ફેબ્રુઆરી', 3 => 'માર્ચ',
            4 => 'એપ્રિલ', 5 => 'મે', 6 => 'જૂન',
            7 => 'જુલાઈ', 8 => 'ઑગસ્ટ', 9 => 'સપ્ટેમ્બર',
            10 => 'ઑક્ટોબર', 11 => 'નવેમ્બર', 12 => 'ડિસેમ્બર',
        ];
        $monthName = $lang === 'gu' ? ($monthNamesGu[$data['month']] ?? '') : $monthStart->format('F');

        $students->transform(function ($s) use ($lang) {
            return [
                'id' => $s->id,
                'gr_number' => $s->gr_number,
                'name' => $lang === 'gu' ? $s->student_name_gu : $s->student_name_en,
                'sharirik_jaati' => $s->sharirik_jaati,
                'date_of_birth' => $s->date_of_birth ? Carbon::parse($s->date_of_birth)->format('d/m/Y') : null,
                'category' => $lang === 'gu' ? $s->category_gu : $s->category_en,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'standard' => $standard->only(['id', 'name']),
                'class' => $class->only(['id', 'name']),
                'class_teacher' => $classTeacher?->teacher?->only(['id', 'name']),
                'academic_year' => $academicYear->only(['id', 'year']),
                'month' => $data['month'],
                'year' => $data['year'],
                'month_name' => $monthName,
                'type' => $type,
                'days_in_month' => $daysInMonth,
                'total_students' => $students->count(),
                'blank_rows' => 5,
                'students' => $students,
                'dates' => $dates,
            ],
        ]);
    }
}
