<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::orderBy('vehicle_no')->get();
        return view('transport.vehicles.index', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_no' => 'required|string|max:50',
            'vehicle_type' => 'required|string|max:50',
            'capacity' => 'nullable|integer|min:0',
            'driver_name' => 'nullable|string|max:255',
            'driver_mobile' => 'nullable|string|max:20',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $vehicle = Vehicle::create($data);
        return response()->json(['success' => true, 'message' => 'વાહન ઉમેરાયું', 'vehicle' => $vehicle]);
    }

    public function show(Vehicle $vehicle)
    {
        return response()->json($vehicle);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'vehicle_no' => 'required|string|max:50',
            'vehicle_type' => 'required|string|max:50',
            'capacity' => 'nullable|integer|min:0',
            'driver_name' => 'nullable|string|max:255',
            'driver_mobile' => 'nullable|string|max:20',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $vehicle->update($data);
        return response()->json(['success' => true, 'message' => 'વાહન સુધારાયું', 'vehicle' => $vehicle->fresh()]);
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return response()->json(['success' => true, 'message' => 'વાહન કાઢી નાખ્યું']);
    }
}
