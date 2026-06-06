<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\RouteStop;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::with('vehicle', 'stops')->orderBy('route_name')->get();
        $vehicles = Vehicle::where('is_active', true)->orderBy('vehicle_no')->get();
        return view('transport.routes.index', compact('routes', 'vehicles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'route_name' => 'required|string|max:255',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'description' => 'nullable|string',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $route = Route::create($data);
        return response()->json(['success' => true, 'message' => 'રૂટ ઉમેરાયો', 'route' => $route->load('vehicle', 'stops')]);
    }

    public function show(Route $route)
    {
        return response()->json($route->load('vehicle', 'stops'));
    }

    public function update(Request $request, Route $route)
    {
        $data = $request->validate([
            'route_name' => 'required|string|max:255',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'description' => 'nullable|string',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $route->update($data);
        return response()->json(['success' => true, 'message' => 'રૂટ સુધારાયો', 'route' => $route->fresh()->load('vehicle', 'stops')]);
    }

    public function destroy(Route $route)
    {
        $route->delete();
        return response()->json(['success' => true, 'message' => 'રૂટ કાઢી નાખ્યો']);
    }

    // ---- Stops ----
    public function storeStop(Request $request, Route $route)
    {
        $data = $request->validate([
            'stop_name' => 'required|string|max:255',
            'stop_order' => 'nullable|integer|min:0',
            'pickup_time' => 'nullable',
            'drop_time' => 'nullable',
        ]);
        $data['stop_order'] = $data['stop_order'] ?? $route->stops()->count() + 1;
        $stop = $route->stops()->create($data);
        return response()->json(['success' => true, 'message' => 'સ્ટોપ ઉમેરાયો', 'stop' => $stop]);
    }

    public function updateStop(Request $request, $stopId)
    {
        $stop = RouteStop::findOrFail($stopId);
        $data = $request->validate([
            'stop_name' => 'required|string|max:255',
            'stop_order' => 'nullable|integer|min:0',
            'pickup_time' => 'nullable',
            'drop_time' => 'nullable',
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

    // ---- Timetable ----
    public function showTimetable(Request $request)
    {
        $routes = Route::where('is_active', true)->orderBy('route_name')->with('vehicle', 'stops')->get();
        $selectedRoute = null;
        if ($request->route_id) {
            $selectedRoute = Route::with('vehicle', 'stops')->find($request->route_id);
        }
        return view('transport.routes.timetable', compact('routes', 'selectedRoute'));
    }

    public function printTimetable(Request $request)
    {
        $data = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'lang' => 'nullable|in:gu,en',
        ]);

        $lang = $data['lang'] ?? 'gu';
        $route = Route::with('vehicle', 'stops')->findOrFail($data['route_id']);
        $school = \App\Models\SchoolSetting::find(1);

        return view('transport.routes.timetable-print', compact('route', 'lang', 'school'));
    }
}
