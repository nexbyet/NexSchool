<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\RouteStop;
use App\Models\SchoolClass;
use App\Models\Standard;
use App\Models\Student;
use App\Models\StudentRoute;
use Illuminate\Http\Request;

class StudentRouteController extends Controller
{
    public function index(Request $request)
    {
        $standards = Standard::orderBy('sort_order')->get();
        $routes = Route::where('is_active', true)->orderBy('route_name')->get();

        $query = StudentRoute::with('student.currentStandard', 'student.currentClass', 'route', 'stop')
            ->where('is_active', true);

        if ($request->standard_id) {
            $query->whereHas('student', fn($q) => $q->where('current_standard_id', $request->standard_id));
        }
        if ($request->class_id) {
            $query->whereHas('student', fn($q) => $q->where('current_class_id', $request->class_id));
        }
        if ($request->route_id) {
            $query->where('route_id', $request->route_id);
        }

        $assignments = $query->orderBy('route_id')->get()
            ->groupBy(fn($a) => $a->route?->route_name ?? '—');

        $students = collect();
        if ($request->standard_id && $request->class_id) {
            $assignedIds = StudentRoute::where('is_active', true)->pluck('student_id');
            $students = Student::where('current_standard_id', $request->standard_id)
                ->where('current_class_id', $request->class_id)
                ->where('status', 'active')
                ->whereNotIn('id', $assignedIds)
                ->orderBy('gr_number')
                ->get();
        }

        $classes = collect();
        if ($request->standard_id) {
            $classes = SchoolClass::where('standard_id', $request->standard_id)
                ->where('status', 'active')->orderBy('sort_order')->get();
        }

        return view('transport.student-route.index', compact(
            'standards', 'routes', 'assignments', 'students', 'classes'
        ));
    }

    public function assign(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'route_id' => 'required|exists:routes,id',
            'stop_id' => 'nullable|exists:route_stops,id',
            'pickup' => 'nullable|boolean',
            'drop' => 'nullable|boolean',
        ]);
        $data['pickup'] = $request->boolean('pickup', true);
        $data['drop'] = $request->boolean('drop', true);
        StudentRoute::updateOrCreate(
            ['student_id' => $data['student_id']],
            $data
        );
        return response()->json(['success' => true, 'message' => 'વિદ્યાર્થીને રૂટ સોંપાયો']);
    }

    public function bulkAssign(Request $request)
    {
        $data = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'route_id' => 'required|exists:routes,id',
            'stop_id' => 'nullable|exists:route_stops,id',
        ]);

        foreach ($data['student_ids'] as $studentId) {
            StudentRoute::updateOrCreate(
                ['student_id' => $studentId],
                [
                    'route_id' => $data['route_id'],
                    'stop_id' => $data['stop_id'] ?? null,
                    'pickup' => true,
                    'drop' => true,
                    'is_active' => true,
                ]
            );
        }

        $count = count($data['student_ids']);
        return response()->json(['success' => true, 'message' => "$count વિદ્યાર્થીઓને રૂટ સોંપાયા"]);
    }

    public function destroy($id)
    {
        $assignment = StudentRoute::findOrFail($id);
        $assignment->delete();
        return response()->json(['success' => true, 'message' => 'રૂટ સોંપણી કાઢી નાખી']);
    }

    public function getRoutes($studentId)
    {
        $routes = Route::where('is_active', true)->orderBy('route_name')->get();
        $assignment = StudentRoute::where('student_id', $studentId)->where('is_active', true)->first();
        $stops = $assignment ? RouteStop::where('route_id', $assignment->route_id)->orderBy('stop_order')->get() : collect();
        return response()->json(compact('routes', 'assignment', 'stops'));
    }

    public function getStops($routeId)
    {
        $stops = RouteStop::where('route_id', $routeId)->orderBy('stop_order')->get();
        return response()->json($stops);
    }
}
