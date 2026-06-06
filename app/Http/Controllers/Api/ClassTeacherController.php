<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassTeacher;
use App\Models\Standard;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ClassTeacherController extends Controller
{
    public function index(Request $request)
    {
        $academicYearId = $request->get('academic_year_id', AcademicYear::getActive()?->id);

        $standards = Standard::with(['classes' => function ($q) use ($academicYearId) {
            $q->orderBy('sort_order')->with(['classTeacher' => function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId)->with('teacher:id,name,teacher_id');
            }]);
        }])->orderBy('sort_order')->get(['id', 'name']);

        $teachers = Teacher::orderBy('name')->get(['id', 'name', 'teacher_id', 'status']);

        return response()->json([
            'standards' => $standards,
            'teachers' => $teachers,
        ]);
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

        $teacherName = $ct->teacher ? $ct->teacher->name : null;
        return response()->json([
            'success' => true,
            'message' => 'વર્ગશિક્ષક સોંપાયો.',
            'teacher_name' => $teacherName,
        ]);
    }
}
