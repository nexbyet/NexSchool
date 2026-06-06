<?php

namespace App\Http\Controllers;

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

        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $activeYear = AcademicYear::find($academicYearId);
        $standards = Standard::orderBy('sort_order')->get();
        $allTeachers = Teacher::orderBy('name')->get(['id', 'name', 'teacher_id', 'status']);

        $stdName = null;
        $classes = collect();
        $subjects = collect();
        $assignments = collect();
        $assignedCount = 0;
        $totalSlots = 0;

        if ($standardId && $academicYearId) {
            $standard = Standard::with('subjects')->find($standardId);
            $stdName = $standard?->name;
            $subjects = $standard?->subjects ?? collect();
            $classes = $standard?->classes()->orderBy('sort_order')->get() ?? collect();

            $assignments = SubjectTeacherAssignment::where('academic_year_id', $academicYearId)
                ->where('standard_id', $standardId)
                ->with('teacher:id,name,teacher_id')
                ->get()
                ->keyBy(fn($a) => $a->subject_id . '-' . ($a->class_id ?? '0'));

            $totalSlots = $subjects->count() * $classes->count();
            $assignedCount = $assignments->filter(fn($a) => $a->teacher_id)->count();
        }

        return view('subject-assignments.index', compact(
            'academicYears', 'academicYearId', 'activeYear',
            'standards', 'standardId', 'stdName', 'classes', 'subjects', 'assignments', 'allTeachers',
            'assignedCount', 'totalSlots'
        ));
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

        $sta->load('teacher:id,name,teacher_id');

        return response()->json([
            'success' => true,
            'message' => $validated['teacher_id']
                ? ($sta->subject->name . ' → ' . $sta->teacher->name)
                : ($sta->subject->name . ' — શિક્ષક દૂર કર્યો'),
            'teacher_name' => $sta->teacher?->name ?? '—',
            'teacher_id' => $sta->teacher_id,
        ]);
    }
}
