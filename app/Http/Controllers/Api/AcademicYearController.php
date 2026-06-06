<?php

// NexSchool - API Academic Year Controller
// Mobile app માટે શૈક્ષણિક વર્ષ CRUD

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        return response()->json(AcademicYear::orderBy('start_date', 'desc')->get());
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'ફક્ત એડમિન જ વર્ષ ઉમેરી શકે છે.'], 403);
        }

        $validated = $request->validate([
            'year' => 'required|string|max:20|unique:academic_years,year',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'session_start_date' => 'nullable|date',
        ]);

        return response()->json(AcademicYear::create($validated), 201);
    }

    public function show(AcademicYear $academicYear)
    {
        return response()->json($academicYear);
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'ફક્ત એડમિન જ વર્ષ બદલી શકે છે.'], 403);
        }

        $validated = $request->validate([
            'year' => 'required|string|max:20|unique:academic_years,year,' . $academicYear->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'session_start_date' => 'nullable|date',
        ]);

        $academicYear->update($validated);
        return response()->json($academicYear);
    }

    public function destroy(Request $request, AcademicYear $academicYear)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'ફક્ત એડમિન જ વર્ષ કાઢી શકે છે.'], 403);
        }
        if ($academicYear->is_active) {
            return response()->json(['message' => 'સક્રિય વર્ષ કાઢી ન શકાય.'], 422);
        }
        $academicYear->delete();
        return response()->json(null, 204);
    }

    public function setActive(Request $request, AcademicYear $academicYear)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'ફક્ત એડમિન જ વર્ષ સક્રિય કરી શકે છે.'], 403);
        }
        AcademicYear::where('is_active', true)->update(['is_active' => false]);
        $academicYear->update(['is_active' => true]);
        return response()->json(['message' => $academicYear->year . ' સક્રિય થયું.']);
    }

    public function active()
    {
        $year = AcademicYear::getActive();
        if (!$year) {
            return response()->json(['message' => 'કોઈ સક્રિય વર્ષ નથી.'], 404);
        }
        return response()->json($year);
    }
}
