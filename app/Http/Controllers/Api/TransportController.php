<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusAttendance;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\StudentRoute;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    // ─── VEHICLES ─────────────────────────────────────────────

    public function vehicles()
    {
        return response()->json(Vehicle::orderBy('vehicle_no')->get());
    }

    public function storeVehicle(Request $request)
    {
        $data = $request->validate([
            'vehicle_no'   => 'required|string|max:50',
            'vehicle_type' => 'required|string|max:50',
            'capacity'     => 'nullable|integer|min:0',
            'driver_name'  => 'nullable|string|max:255',
            'driver_mobile'=> 'nullable|string|max:20',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $vehicle = Vehicle::create($data);
        return response()->json(['success' => true, 'message' => 'વાહન ઉમેરાયું', 'vehicle' => $vehicle], 201);
    }

    public function showVehicle(Vehicle $vehicle)
    {
        return response()->json($vehicle);
    }

    public function updateVehicle(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'vehicle_no'   => 'required|string|max:50',
            'vehicle_type' => 'required|string|max:50',
            'capacity'     => 'nullable|integer|min:0',
            'driver_name'  => 'nullable|string|max:255',
            'driver_mobile'=> 'nullable|string|max:20',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $vehicle->update($data);
        return response()->json(['success' => true, 'message' => 'વાહન સુધારાયું', 'vehicle' => $vehicle->fresh()]);
    }

    public function destroyVehicle(Vehicle $vehicle)
    {
        $vehicle->delete();
        return response()->json(['success' => true, 'message' => 'વાહન કાઢી નાખ્યું']);
    }

    // ─── ROUTES ───────────────────────────────────────────────

    public function routes()
    {
        return response()->json(Route::with('vehicle', 'stops')->orderBy('route_name')->get());
    }

    public function storeRoute(Request $request)
    {
        $data = $request->validate([
            'route_name'  => 'required|string|max:255',
            'vehicle_id'  => 'nullable|exists:vehicles,id',
            'description' => 'nullable|string',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $route = Route::create($data)->load('vehicle', 'stops');
        return response()->json(['success' => true, 'message' => 'રૂટ ઉમેરાયો', 'route' => $route], 201);
    }

    public function showRoute(Route $route)
    {
        return response()->json($route->load('vehicle', 'stops'));
    }

    public function updateRoute(Request $request, Route $route)
    {
        $data = $request->validate([
            'route_name'  => 'required|string|max:255',
            'vehicle_id'  => 'nullable|exists:vehicles,id',
            'description' => 'nullable|string',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $route->update($data);
        return response()->json(['success' => true, 'message' => 'રૂટ સુધારાયો', 'route' => $route->fresh()->load('vehicle', 'stops')]);
    }

    public function destroyRoute(Route $route)
    {
        $route->delete();
        return response()->json(['success' => true, 'message' => 'રૂટ કાઢી નાખ્યો']);
    }

    // ─── ROUTE STOPS ──────────────────────────────────────────

    public function stops($routeId)
    {
        return response()->json(RouteStop::where('route_id', $routeId)->orderBy('stop_order')->get());
    }

    public function storeStop(Request $request, Route $route)
    {
        $data = $request->validate([
            'stop_name'   => 'required|string|max:255',
            'stop_order'  => 'nullable|integer|min:0',
            'pickup_time' => 'nullable|string|max:20',
            'drop_time'   => 'nullable|string|max:20',
        ]);
        $data['route_id'] = $route->id;
        $data['stop_order'] ??= $route->stops()->count() + 1;
        $stop = RouteStop::create($data);
        return response()->json(['success' => true, 'message' => 'સ્ટોપ ઉમેરાયો', 'stop' => $stop], 201);
    }

    public function updateStop(Request $request, $stopId)
    {
        $stop = RouteStop::findOrFail($stopId);
        $data = $request->validate([
            'stop_name'   => 'required|string|max:255',
            'stop_order'  => 'nullable|integer|min:0',
            'pickup_time' => 'nullable|string|max:20',
            'drop_time'   => 'nullable|string|max:20',
        ]);
        $stop->update($data);
        return response()->json(['success' => true, 'message' => 'સ્ટોપ સુધારાયો', 'stop' => $stop->fresh()]);
    }

    public function destroyStop($stopId)
    {
        $stop = RouteStop::findOrFail($stopId);
        $stop->delete();
        return response()->json(['success' => true, 'message' => 'સ્ટોપ કાઢી નાખ્યો']);
    }

    // ─── STUDENT ROUTE ASSIGNMENTS ────────────────────────────

    public function studentRoutes(Request $request)
    {
        $query = StudentRoute::with('student.currentStandard', 'student.currentClass', 'route', 'stop');
        if ($request->has('route_id')) {
            $query->where('route_id', $request->route_id);
        }
        return response()->json($query->get());
    }

    public function assignStudentRoute(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'route_id'   => 'required|exists:routes,id',
            'stop_id'    => 'nullable|exists:route_stops,id',
            'pickup'     => 'nullable|boolean',
            'drop'       => 'nullable|boolean',
        ]);
        $data['pickup'] = $request->boolean('pickup', true);
        $data['drop'] = $request->boolean('drop', true);
        StudentRoute::updateOrCreate(['student_id' => $data['student_id']], $data);
        return response()->json(['success' => true, 'message' => 'વિદ્યાર્થીને રૂટ સોંપાયો']);
    }

    public function bulkAssignStudentRoute(Request $request)
    {
        $data = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'route_id'    => 'required|exists:routes,id',
            'stop_id'     => 'nullable|exists:route_stops,id',
        ]);
        $count = 0;
        foreach ($data['student_ids'] as $sid) {
            StudentRoute::updateOrCreate(
                ['student_id' => $sid],
                ['route_id' => $data['route_id'], 'stop_id' => $data['stop_id'] ?? null, 'pickup' => true, 'drop' => true, 'is_active' => true]
            );
            $count++;
        }
        return response()->json(['success' => true, 'message' => "{$count} વિદ્યાર્થીઓને રૂટ સોંપાયા"]);
    }

    public function destroyStudentRoute($id)
    {
        StudentRoute::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'રૂટ સોંપણી કાઢી નાખી']);
    }

    public function studentRouteByStudent($studentId)
    {
        $assignment = StudentRoute::where('student_id', $studentId)->with('route', 'stop')->first();
        $routes = Route::where('is_active', true)->with('vehicle', 'stops')->get();
        $stops = $assignment ? RouteStop::where('route_id', $assignment->route_id)->orderBy('stop_order')->get() : collect();
        return response()->json(compact('routes', 'assignment', 'stops'));
    }

    // ─── BUS ATTENDANCE ───────────────────────────────────────

    public function busAttendance(Request $request)
    {
        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'date'     => 'required|date',
        ]);
        $students = StudentRoute::where('route_id', $request->route_id)
            ->where('is_active', true)
            ->with('student.currentStandard', 'student.currentClass')
            ->get()
            ->pluck('student');
        $attendances = BusAttendance::where('route_id', $request->route_id)
            ->where('date', $request->date)
            ->get()
            ->keyBy('student_id');
        return response()->json(compact('students', 'attendances'));
    }

    public function markBusAttendance(Request $request)
    {
        $data = $request->validate([
            'student_id'      => 'required|exists:students,id',
            'route_id'        => 'required|exists:routes,id',
            'date'            => 'required|date',
            'morning_status'  => 'nullable|in:present,absent,leave',
            'evening_status'  => 'nullable|in:present,absent,leave',
            'notes'           => 'nullable|string|max:500',
        ]);
        BusAttendance::updateOrCreate(
            ['student_id' => $data['student_id'], 'route_id' => $data['route_id'], 'date' => $data['date']],
            $data
        );
        return response()->json(['success' => true, 'message' => 'હાજરી સેવ થઈ']);
    }

    public function printBusAttendance(Request $request)
    {
        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'month'    => 'required|integer|between:1,12',
            'year'     => 'required|integer|min:2020|max:2100',
            'lang'     => 'nullable|in:gu,en',
            'type'     => 'nullable|in:blank,filled',
        ]);
        $route = Route::with('vehicle', 'stops')->findOrFail($request->route_id);
        $students = StudentRoute::where('route_id', $route->id)->where('is_active', true)
            ->with('student')->get()->pluck('student');
        $monthStart = now()->setYear($request->year)->setMonth($request->month)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $workingDates = [];
        for ($d = $monthStart->copy(); $d->lte($monthEnd); $d->addDay()) {
            if ($d->dayOfWeek !== 0) {
                $workingDates[] = $d->format('Y-m-d');
            }
        }
        $workingDays = count($workingDates);
        $attendances = collect();
        if ($request->type === 'filled') {
            $attendances = BusAttendance::where('route_id', $route->id)
                ->whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
                ->get()->groupBy('student_id');
        }
        $lang = $request->lang ?? 'gu';
        $months = ['જાન્યુઆરી','ફેબ્રુઆરી','માર્ચ','એપ્રિલ','મે','જૂન','જુલાઈ','ઓગસ્ટ','સપ્ટેમ્બર','ઓક્ટોબર','નવેમ્બર','ડિસેમ્બર'];
        $monthName = $months[$request->month - 1];
        return response()->json(compact('route', 'students', 'workingDates', 'workingDays', 'monthName', 'attendances', 'lang', 'monthStart', 'monthEnd', 'type'));
    }
}
