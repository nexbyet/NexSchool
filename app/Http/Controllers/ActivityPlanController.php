<?php

namespace App\Http\Controllers;

use App\Models\ActivityPlan;
use App\Models\PublicHoliday;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class ActivityPlanController extends Controller
{
    public function index()
    {
        $activeYear = AcademicYear::getActive();
        $plans = ActivityPlan::where('academic_year_id', $activeYear?->id)
            ->orderBy('sort_order')
            ->orderBy('date')
            ->get();
        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        $holidayDates = PublicHoliday::where('academic_year_id', $activeYear?->id)
            ->pluck('name', 'date')
            ->map(fn($n, $d) => ['date' => $d, 'name' => $n])
            ->values();
        return view('activity-plans.index', compact('plans', 'activeYear', 'years', 'holidayDates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'sort_order' => 'required|integer|min:0',
            'activity_name' => 'required|string|max:255',
            'date' => 'required|date',
            'remarks' => 'nullable|string|max:500',
        ]);

        // Check if date is a public holiday
        $holiday = PublicHoliday::where('academic_year_id', $validated['academic_year_id'])
            ->where('date', $validated['date'])
            ->first();

        if ($holiday) {
            return response()->json([
                'success' => false,
                'holiday' => true,
                'message' => 'આ દિવસે રજા છે: ' . $holiday->name . ' (' . $holiday->date->format('d/m/Y') . ')',
            ], 422);
        }

        $plan = ActivityPlan::create($validated);

        return response()->json(['success' => true, 'message' => 'પ્રવૃત્તિ ઉમેરાઈ.', 'plan' => $plan]);
    }

    public function show(ActivityPlan $activityPlan)
    {
        return response()->json($activityPlan);
    }

    public function update(Request $request, ActivityPlan $activityPlan)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'sort_order' => 'required|integer|min:0',
            'activity_name' => 'required|string|max:255',
            'date' => 'required|date',
            'remarks' => 'nullable|string|max:500',
        ]);

        $holiday = PublicHoliday::where('academic_year_id', $validated['academic_year_id'])
            ->where('date', $validated['date'])
            ->first();

        if ($holiday) {
            return response()->json([
                'success' => false,
                'holiday' => true,
                'message' => 'આ દિવસે રજા છે: ' . $holiday->name . ' (' . $holiday->date->format('d/m/Y') . ')',
            ], 422);
        }

        $activityPlan->update($validated);

        return response()->json(['success' => true, 'message' => 'પ્રવૃત્તિ સુધારાઈ.', 'plan' => $activityPlan]);
    }

    public function destroy(ActivityPlan $activityPlan)
    {
        $activityPlan->delete();
        return response()->json(['success' => true, 'message' => 'પ્રવૃત્તિ કાઢી નાખી.']);
    }

    public function byYear(Request $request)
    {
        $yearId = $request->get('academic_year_id', AcademicYear::getActive()?->id);
        $plans = ActivityPlan::where('academic_year_id', $yearId)
            ->orderBy('sort_order')
            ->orderBy('date')
            ->get();
        return response()->json(['plans' => $plans]);
    }

    public function print()
    {
        $activeYear = AcademicYear::getActive();
        $plans = ActivityPlan::where('academic_year_id', $activeYear?->id)
            ->orderBy('sort_order')
            ->orderBy('date')
            ->get();
        $schoolSetting = \App\Models\SchoolSetting::first();
        return view('activity-plans.print', compact('plans', 'activeYear', 'schoolSetting'));
    }
}
