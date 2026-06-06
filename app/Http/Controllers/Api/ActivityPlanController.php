<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityPlan;
use App\Models\PublicHoliday;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class ActivityPlanController extends Controller
{
    public function index(Request $request)
    {
        $yearId = $request->get('academic_year_id', AcademicYear::getActive()?->id);
        return response()->json(
            ActivityPlan::where('academic_year_id', $yearId)->orderBy('sort_order')->orderBy('date')->get()
        );
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'ફક્ત એડમિન જ પ્રવૃત્તિ ઉમેરી શકે છે.'], 403);
        }

        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'sort_order' => 'required|integer|min:0',
            'activity_name' => 'required|string|max:255',
            'date' => 'required|date',
            'remarks' => 'nullable|string|max:500',
        ]);

        $holiday = PublicHoliday::where('academic_year_id', $validated['academic_year_id'])
            ->where('date', $validated['date'])->first();

        if ($holiday) {
            return response()->json([
                'message' => 'આ દિવસે રજા છે: ' . $holiday->name . ' (' . $holiday->date->format('d/m/Y') . ')',
                'holiday' => true,
            ], 422);
        }

        return response()->json(ActivityPlan::create($validated), 201);
    }

    public function show(ActivityPlan $activityPlan)
    {
        return response()->json($activityPlan);
    }

    public function update(Request $request, ActivityPlan $activityPlan)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'ફક્ત એડમિન જ પ્રવૃત્તિ બદલી શકે છે.'], 403);
        }

        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'sort_order' => 'required|integer|min:0',
            'activity_name' => 'required|string|max:255',
            'date' => 'required|date',
            'remarks' => 'nullable|string|max:500',
        ]);

        $holiday = PublicHoliday::where('academic_year_id', $validated['academic_year_id'])
            ->where('date', $validated['date'])->first();

        if ($holiday) {
            return response()->json([
                'message' => 'આ દિવસે રજા છે: ' . $holiday->name . ' (' . $holiday->date->format('d/m/Y') . ')',
                'holiday' => true,
            ], 422);
        }

        $activityPlan->update($validated);
        return response()->json($activityPlan);
    }

    public function destroy(Request $request, ActivityPlan $activityPlan)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'ફક્ત એડમિન જ પ્રવૃત્તિ કાઢી શકે છે.'], 403);
        }
        $activityPlan->delete();
        return response()->json(null, 204);
    }
}
