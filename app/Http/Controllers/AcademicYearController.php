<?php

// NexSchool - Academic Year Web Controller
// AJAX CRUD — alerts on success/error, confirm on delete

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        return view('academic-years.index', compact('years'));
    }

    public function show(AcademicYear $academicYear)
    {
        return response()->json([
            'id' => $academicYear->id,
            'year' => $academicYear->year,
            'start_date' => $academicYear->start_date->format('Y-m-d'),
            'end_date' => $academicYear->end_date->format('Y-m-d'),
            'session_start_date' => $academicYear->session_start_date?->format('Y-m-d') ?? '',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|string|max:20|unique:academic_years,year',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'session_start_date' => 'nullable|date',
        ]);

        $year = AcademicYear::create($validated);

        return response()->json(['success' => true, 'message' => 'શૈક્ષણિક વર્ષ ઉમેરાયું.', 'year' => $year]);
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'year' => 'required|string|max:20|unique:academic_years,year,' . $academicYear->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'session_start_date' => 'nullable|date',
        ]);

        $academicYear->update($validated);

        return response()->json(['success' => true, 'message' => 'શૈક્ષણિક વર્ષ સુધારાયું.', 'year' => $academicYear]);
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->is_active) {
            return response()->json(['success' => false, 'message' => 'સક્રિય વર્ષ કાઢી ન શકાય. પહેલા બીજું વર્ષ સક્રિય કરો.'], 422);
        }
        $academicYear->delete();
        return response()->json(['success' => true, 'message' => 'શૈક્ષણિક વર્ષ કાઢી નાખ્યું.']);
    }

    public function setActive(AcademicYear $academicYear)
    {
        AcademicYear::where('is_active', true)->update(['is_active' => false]);
        $academicYear->update(['is_active' => true]);
        return response()->json(['success' => true, 'message' => 'હવે ' . $academicYear->year . ' સક્રિય છે.']);
    }
}
