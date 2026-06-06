<?php

// NexSchool - API SchoolClass Controller
// RESTful CRUD for classes
// વર્ગોનું API સંચાલન

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    // GET /api/classes - બધા વર્ગો (with teacher + students)
    public function index()
    {
        return response()->json(SchoolClass::with('teacher', 'students')->get());
    }

    // POST /api/classes - નવો વર્ગ બનાવો
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'section' => 'nullable|string|max:50',
            'room_number' => 'nullable|string|max:20',
            'teacher_id' => 'nullable|exists:teachers,id',
            'academic_year' => 'nullable|string|max:20',
            'capacity' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $class = SchoolClass::create($validated);
        return response()->json($class, 201);
    }

    // GET /api/classes/{class} - એક વર્ગની વિગત
    public function show(SchoolClass $schoolClass)
    {
        return response()->json($schoolClass->load('teacher', 'students', 'subjects'));
    }

    // PUT/PATCH /api/classes/{class} - વર્ગ માહિતી સુધારો
    public function update(Request $request, SchoolClass $schoolClass)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'section' => 'nullable|string|max:50',
            'room_number' => 'nullable|string|max:20',
            'teacher_id' => 'nullable|exists:teachers,id',
            'academic_year' => 'nullable|string|max:20',
            'capacity' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $schoolClass->update($validated);
        return response()->json($schoolClass);
    }

    // DELETE /api/classes/{class} - વર્ગ કાઢી નાખો
    public function destroy(SchoolClass $schoolClass)
    {
        $schoolClass->delete();
        return response()->json(null, 204);
    }
}
