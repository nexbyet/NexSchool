<?php

namespace App\Http\Controllers;

use App\Models\PublicHoliday;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class PublicHolidayController extends Controller
{
    public function index()
    {
        $activeYear = AcademicYear::getActive();
        $holidays = PublicHoliday::where('academic_year_id', $activeYear?->id)
            ->orderBy('date')
            ->get();
        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        return view('public-holidays.index', compact('holidays', 'activeYear', 'years'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:jaher,sthanik',
            'date' => 'required|date',
        ]);

        $existing = PublicHoliday::where('academic_year_id', $validated['academic_year_id'])
            ->where('date', $validated['date'])
            ->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'આ તારીખે પહેલેથી જ રજા છે: ' . $existing->name], 422);
        }

        $holiday = PublicHoliday::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'રજા ઉમેરાઈ.',
            'holiday' => $holiday,
        ]);
    }

    public function show(PublicHoliday $publicHoliday)
    {
        return response()->json($publicHoliday);
    }

    public function update(Request $request, PublicHoliday $publicHoliday)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:jaher,sthanik',
            'date' => 'required|date',
        ]);

        $existing = PublicHoliday::where('academic_year_id', $validated['academic_year_id'])
            ->where('date', $validated['date'])
            ->where('id', '!=', $publicHoliday->id)
            ->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'આ તારીખે પહેલેથી જ રજા છે: ' . $existing->name], 422);
        }

        $publicHoliday->update($validated);

        return response()->json(['success' => true, 'message' => 'રજા સુધારાઈ.', 'holiday' => $publicHoliday]);
    }

    public function destroy(PublicHoliday $publicHoliday)
    {
        $publicHoliday->delete();
        return response()->json(['success' => true, 'message' => 'રજા કાઢી નાખી.']);
    }

    public function byYear(Request $request)
    {
        $yearId = $request->get('academic_year_id', AcademicYear::getActive()?->id);
        $holidays = PublicHoliday::where('academic_year_id', $yearId)
            ->orderBy('date')
            ->get();
        $dates = $holidays->pluck('date')->map(fn($d) => $d->format('Y-m-d'))->toArray();
        return response()->json(['holidays' => $holidays, 'dates' => $dates]);
    }

    public function print()
    {
        $activeYear = AcademicYear::getActive();
        $holidays = PublicHoliday::where('academic_year_id', $activeYear?->id)
            ->orderBy('date')
            ->get();
        $schoolSetting = \App\Models\SchoolSetting::first();
        return view('public-holidays.print', compact('holidays', 'activeYear', 'schoolSetting'));
    }
}
