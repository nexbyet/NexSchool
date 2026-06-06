<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Standard;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TimetableSlot;
use App\Models\TimetableEntry;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $academicYearId = $request->get('academic_year_id', AcademicYear::getActive()?->id);
        $standardId = $request->get('standard_id');
        $classId = $request->get('class_id');

        $slots = TimetableSlot::where('academic_year_id', $academicYearId)
            ->orderBy('sort_order')->get();
        $standards = Standard::orderBy('sort_order')->get(['id', 'name']);
        $subjects = Subject::orderBy('name')->get(['id', 'name', 'name_gu']);
        $teachers = Teacher::orderBy('name')->get(['id', 'name', 'teacher_id', 'status']);

        $entries = collect();
        $classes = collect();

        if ($standardId) {
            $standard = Standard::with('classes')->find($standardId);
            $classes = $standard?->classes()->orderBy('sort_order')->get(['id', 'name']);

            if ($classId) {
                $entries = TimetableEntry::where('academic_year_id', $academicYearId)
                    ->where('standard_id', $standardId)
                    ->where('school_class_id', $classId)
                    ->with(['subject:id,name,name_gu', 'teacher:id,name,teacher_id'])
                    ->get();
            }
        }

        return response()->json([
            'slots' => $slots,
            'standards' => $standards,
            'classes' => $classes,
            'subjects' => $subjects,
            'teachers' => $teachers,
            'entries' => $entries,
        ]);
    }

    public function updateEntry(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'timetable_slot_id' => 'required|exists:timetable_slots,id',
            'standard_id' => 'required|exists:standards,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'day_of_week' => 'required|integer|between:1,6',
            'subject_id' => 'nullable|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $entry = TimetableEntry::updateOrCreate(
            [
                'academic_year_id' => $validated['academic_year_id'],
                'timetable_slot_id' => $validated['timetable_slot_id'],
                'standard_id' => $validated['standard_id'],
                'school_class_id' => $validated['school_class_id'],
                'day_of_week' => $validated['day_of_week'],
            ],
            [
                'subject_id' => $validated['subject_id'],
                'teacher_id' => $validated['teacher_id'],
            ]
        );

        $entry->load(['subject:id,name,name_gu', 'teacher:id,name,teacher_id']);

        return response()->json([
            'success' => true,
            'message' => 'ટાઇમટેબલ અપડેટ થયું.',
            'entry' => $entry,
        ]);
    }

    public function copyToAllDays(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_id' => 'required|exists:standards,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'from_day' => 'required|integer|between:1,6',
        ]);

        $sourceEntries = TimetableEntry::where('academic_year_id', $validated['academic_year_id'])
            ->where('standard_id', $validated['standard_id'])
            ->where('school_class_id', $validated['school_class_id'])
            ->where('day_of_week', $validated['from_day'])
            ->get();

        $targetDays = [1, 2, 3, 4, 5, 6];

        foreach ($targetDays as $day) {
            if ($day === $validated['from_day']) continue;

            foreach ($sourceEntries as $entry) {
                TimetableEntry::updateOrCreate(
                    [
                        'academic_year_id' => $entry->academic_year_id,
                        'timetable_slot_id' => $entry->timetable_slot_id,
                        'standard_id' => $entry->standard_id,
                        'school_class_id' => $entry->school_class_id,
                        'day_of_week' => $day,
                    ],
                    [
                        'subject_id' => $entry->subject_id,
                        'teacher_id' => $entry->teacher_id,
                    ]
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'બધા દિવસોમાં કોપી થઈ ગયું.',
        ]);
    }

    public function getSlots(Request $request)
    {
        $academicYearId = $request->get('academic_year_id', AcademicYear::getActive()?->id);
        $slots = TimetableSlot::where('academic_year_id', $academicYearId)
            ->orderBy('sort_order')->get();
        return response()->json(['slots' => $slots]);
    }
}
