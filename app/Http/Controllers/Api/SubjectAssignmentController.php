<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Standard;
use App\Models\SubjectTeacherAssignment;
use App\Models\Teacher;
use Illuminate\Http\Request;

class SubjectAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $academicYearId = $request->get('academic_year_id', AcademicYear::getActive()?->id);
        $standardId = $request->get('standard_id');

        $standards = Standard::orderBy('sort_order')->get(['id', 'name']);
        $allTeachers = Teacher::orderBy('name')->get(['id', 'name', 'teacher_id', 'status']);

        $classes = collect();
        $subjects = collect();
        $assignments = collect();

        if ($standardId && $academicYearId) {
            $standard = Standard::with('subjects')->find($standardId);
            $subjects = $standard?->subjects ?? collect();
            $classes = $standard?->classes()->orderBy('sort_order')->get(['id', 'name']) ?? collect();

            $assignments = SubjectTeacherAssignment::where('academic_year_id', $academicYearId)
                ->where('standard_id', $standardId)
                ->with('teacher:id,name,teacher_id')
                ->get();
        }

        return response()->json([
            'standards' => $standards,
            'teachers' => $allTeachers,
            'classes' => $classes,
            'subjects' => $subjects,
            'assignments' => $assignments,
        ]);
    }

    public function assign(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'standard_id' => 'required|exists:standards,id',
            'class_id' => 'nullable|exists:school_classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $sta = SubjectTeacherAssignment::updateOrCreate(
            [
                'subject_id' => $validated['subject_id'],
                'standard_id' => $validated['standard_id'],
                'class_id' => $validated['class_id'],
                'academic_year_id' => $validated['academic_year_id'],
            ],
            ['teacher_id' => $validated['teacher_id']],
        );

        return response()->json([
            'success' => true,
            'message' => 'વિષય શિક્ષક સોંપાયો.',
            'assignment' => $sta->load('teacher:id,name,teacher_id'),
        ]);
    }
}
