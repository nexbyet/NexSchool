<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\GradeScale;
use App\Models\GradeSlab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $scales = GradeScale::with(['academicYear', 'slabs'])->orderByDesc('id')->get();

        return view('exams.grades.index', compact('academicYears', 'scales'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        $scale = DB::transaction(function () use ($data) {
            $scale = GradeScale::create([
                'academic_year_id' => $data['academic_year_id'],
                'name' => $data['name'],
                'is_active' => $data['is_active'] ?? true,
            ]);
            $this->syncSlabs($scale, $data['slabs']);
            return $scale;
        });

        return response()->json([
            'success' => true,
            'message' => 'ગ્રેડ સ્કેલ બનાવ્યો.',
            'scale_id' => $scale->id,
        ]);
    }

    public function show($id)
    {
        $scale = GradeScale::with(['academicYear', 'slabs'])->findOrFail($id);

        return response()->json(['success' => true, 'scale' => $scale]);
    }

    public function update(Request $request, $id)
    {
        $scale = GradeScale::findOrFail($id);
        $data = $this->validatePayload($request);

        DB::transaction(function () use ($scale, $data) {
            $scale->update([
                'academic_year_id' => $data['academic_year_id'],
                'name' => $data['name'],
                'is_active' => $data['is_active'] ?? true,
            ]);
            $scale->slabs()->delete();
            $this->syncSlabs($scale, $data['slabs']);
        });

        return response()->json(['success' => true, 'message' => 'ગ્રેડ સ્કેલ સુધાર્યો.']);
    }

    public function destroy($id)
    {
        $scale = GradeScale::findOrFail($id);
        $name = $scale->name;
        $scale->delete();

        return response()->json([
            'success' => true,
            'message' => "ગ્રેડ સ્કેલ \"{$name}\" કાઢી નાખ્યો.",
        ]);
    }

    private function validatePayload(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'slabs' => 'required|array|min:1',
            'slabs.*.grade' => 'required|string|max:10',
            'slabs.*.min_percent' => 'required|numeric|min:0|max:100',
            'slabs.*.max_percent' => 'required|numeric|min:0|max:100',
            'slabs.*.is_pass' => 'boolean',
        ]);

        // slabs ne min mujab gothvo + 0-100 coverage + overlap check
        $slabs = collect($data['slabs'])->map(function ($s) {
            return [
                'grade' => trim($s['grade']),
                'min_percent' => round((float) $s['min_percent'], 2),
                'max_percent' => round((float) $s['max_percent'], 2),
                'is_pass' => $s['is_pass'] ?? true,
            ];
        })->sortBy('min_percent')->values();

        foreach ($slabs as $s) {
            if ($s['min_percent'] > $s['max_percent']) {
                abort(response()->json(['success' => false, 'message' => "\"{$s['grade']}\" માં શરૂઆત અંત કરતાં વધારે છે."], 422));
            }
            if ($s['grade'] === '') {
                abort(response()->json(['success' => false, 'message' => 'દરેક slab માં ગ્રેડ લખવો જરૂરી છે.'], 422));
            }
        }

        if ($slabs->first()['min_percent'] != 0) {
            abort(response()->json(['success' => false, 'message' => 'પહેલો slab 0% થી શરૂ થવો જોઈએ.'], 422));
        }
        if ($slabs->last()['max_percent'] != 100) {
            abort(response()->json(['success' => false, 'message' => 'છેલ્લો slab 100% સુધી જવો જોઈએ.'], 422));
        }
        for ($i = 1; $i < $slabs->count(); $i++) {
            $prev = $slabs[$i - 1];
            $cur = $slabs[$i];
            $gap = round($cur['min_percent'] - $prev['max_percent'], 2);
            if ($gap < 0) {
                abort(response()->json(['success' => false, 'message' => "\"{$cur['grade']}\" અને \"{$prev['grade']}\" ની રેન્જ અથડાય છે."], 422));
            }
            // 0 (સળંગ, દા.ત. 20→20) ke 1 (integer style, દા.ત. 20→21) — 1% thi vadhu gap nahi
            if ($gap > 1) {
                abort(response()->json(['success' => false, 'message' => "\"{$prev['grade']}\" પછી {$gap}% ખાલી જગ્યા છે — slabs સળંગ (0 કે 1 નો તફાવત) હોવા જોઈએ."], 422));
            }
        }

        $data['slabs'] = $slabs->all();
        return $data;
    }

    private function syncSlabs(GradeScale $scale, array $slabs)
    {
        $order = 0;
        foreach ($slabs as $s) {
            $order++;
            GradeSlab::create([
                'grade_scale_id' => $scale->id,
                'grade' => $s['grade'],
                'min_percent' => $s['min_percent'],
                'max_percent' => $s['max_percent'],
                'is_pass' => $s['is_pass'] ?? true,
                'sort_order' => $order,
            ]);
        }
    }
}
