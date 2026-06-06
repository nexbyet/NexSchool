<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PublicHoliday;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class PublicHolidayController extends Controller
{
    public function index(Request $request)
    {
        $yearId = $request->get('academic_year_id', AcademicYear::getActive()?->id);
        return response()->json(
            PublicHoliday::where('academic_year_id', $yearId)->orderBy('date')->get()
        );
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'ફક્ત એડમિન જ રજા ઉમેરી શકે છે.'], 403);
        }

        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:jaher,sthanik',
            'date' => 'required|date',
        ]);

        $existing = PublicHoliday::where('academic_year_id', $validated['academic_year_id'])
            ->where('date', $validated['date'])->first();

        if ($existing) {
            return response()->json(['message' => 'આ તારીખે પહેલેથી રજા છે: ' . $existing->name], 422);
        }

        return response()->json(PublicHoliday::create($validated), 201);
    }

    public function show(PublicHoliday $publicHoliday)
    {
        return response()->json($publicHoliday);
    }

    public function update(Request $request, PublicHoliday $publicHoliday)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'ફક્ત એડમિન જ રજા બદલી શકે છે.'], 403);
        }

        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:jaher,sthanik',
            'date' => 'required|date',
        ]);

        $existing = PublicHoliday::where('academic_year_id', $validated['academic_year_id'])
            ->where('date', $validated['date'])
            ->where('id', '!=', $publicHoliday->id)->first();

        if ($existing) {
            return response()->json(['message' => 'આ તારીખે પહેલેથી રજા છે: ' . $existing->name], 422);
        }

        $publicHoliday->update($validated);
        return response()->json($publicHoliday);
    }

    public function destroy(Request $request, PublicHoliday $publicHoliday)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'ફક્ત એડમિન જ રજા કાઢી શકે છે.'], 403);
        }
        $publicHoliday->delete();
        return response()->json(null, 204);
    }
}
