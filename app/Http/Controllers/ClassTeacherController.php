<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassTeacher;
use App\Models\SchoolClass;
use App\Models\Standard;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ClassTeacherController extends Controller
{
    public function index(Request $request)
    {
        $academicYearId = $request->get('academic_year_id', AcademicYear::getActive()?->id);
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $activeYear = AcademicYear::find($academicYearId);

        $standards = Standard::with(['classes' => function ($q) use ($academicYearId) {
            $q->orderBy('sort_order')->with(['classTeacher' => function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId)->with('teacher');
            }]);
        }])->orderBy('sort_order')->get();

        $teachers = Teacher::orderBy('name')->get(['id', 'name', 'teacher_id', 'status']);

        $totalClasses = 0;
        $assignedCount = 0;
        foreach ($standards as $std) {
            $totalClasses += $std->classes->count();
            $assignedCount += $std->classes->filter(fn($c) => $c->classTeacher && $c->classTeacher->teacher_id)->count();
        }

        return view('class-teachers.index', compact('standards', 'teachers', 'academicYears', 'academicYearId', 'activeYear', 'totalClasses', 'assignedCount'));
    }

    public function assign(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $ct = ClassTeacher::updateOrCreate(
            ['school_class_id' => $validated['class_id'], 'academic_year_id' => $validated['academic_year_id']],
            ['teacher_id' => $validated['teacher_id']],
        );

        $teacherName = $ct->teacher ? $ct->teacher->name : '—';
        return response()->json([
            'success' => true,
            'message' => 'વર્ગશિક્ષક સોંપાયો: ' . $teacherName,
            'teacher_name' => $teacherName,
        ]);
    }
}
