<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\FeeStructure;
use App\Models\FeeStructureDetail;
use App\Models\Standard;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $standards = Standard::orderBy('sort_order')->get();
        $structures = FeeStructure::with('details.feeHead', 'standards', 'academicYear')->get();

        return view('fees.structures.index', compact('academicYears', 'standards', 'structures'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_ids' => 'required|array',
            'standard_ids.*' => 'exists:standards,id',
            'semester' => 'nullable|in:1,2',
            'type' => 'required|in:tuition,transport,other',
            'frequency' => 'required|in:monthly,semesterly,yearly',
            'late_fee_type' => 'required|in:none,fixed,per_month',
            'late_fee_amount' => 'nullable|numeric|min:0',
            'late_fee_after_days' => 'nullable|integer|min:0',
            'heads' => 'nullable|array',
            'heads.*.fee_head_id' => 'required|exists:fee_heads,id',
            'heads.*.amount' => 'required|numeric|min:0',
        ]);

        $overlap = $this->checkStandardOverlap($data['academic_year_id'], $data['type'], $data['semester'] ?? null, $data['standard_ids'], null);
        if ($overlap) {
            return response()->json(['success' => false, 'message' => $overlap], 422);
        }

        $structure = FeeStructure::create([
            'academic_year_id' => $data['academic_year_id'],
            'semester' => $data['semester'] ?? null,
            'type' => $data['type'],
            'frequency' => $data['frequency'],
            'late_fee_type' => $data['late_fee_type'],
            'late_fee_amount' => $data['late_fee_amount'] ?? 0,
            'late_fee_after_days' => $data['late_fee_after_days'] ?? 0,
        ]);

        $structure->standards()->attach($data['standard_ids']);

        if (!empty($data['heads'])) {
            foreach ($data['heads'] as $head) {
                FeeStructureDetail::create([
                    'fee_structure_id' => $structure->id,
                    'fee_head_id' => $head['fee_head_id'],
                    'amount' => $head['amount'],
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Fee structure created',
            'structure' => $structure->load('details.feeHead', 'standards', 'academicYear'),
        ]);
    }

    public function show($id)
    {
        return response()->json(
            FeeStructure::with('details.feeHead', 'standards', 'academicYear')->findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $structure = FeeStructure::findOrFail($id);

        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_ids' => 'required|array',
            'standard_ids.*' => 'exists:standards,id',
            'semester' => 'nullable|in:1,2',
            'type' => 'required|in:tuition,transport,other',
            'frequency' => 'required|in:monthly,semesterly,yearly',
            'late_fee_type' => 'required|in:none,fixed,per_month',
            'late_fee_amount' => 'nullable|numeric|min:0',
            'late_fee_after_days' => 'nullable|integer|min:0',
            'heads' => 'nullable|array',
            'heads.*.fee_head_id' => 'required|exists:fee_heads,id',
            'heads.*.amount' => 'required|numeric|min:0',
        ]);

        $overlap = $this->checkStandardOverlap($data['academic_year_id'], $data['type'], $data['semester'] ?? null, $data['standard_ids'], $id);
        if ($overlap) {
            return response()->json(['success' => false, 'message' => $overlap], 422);
        }

        $structure->update([
            'academic_year_id' => $data['academic_year_id'],
            'semester' => $data['semester'] ?? null,
            'type' => $data['type'],
            'frequency' => $data['frequency'],
            'late_fee_type' => $data['late_fee_type'],
            'late_fee_amount' => $data['late_fee_amount'] ?? 0,
            'late_fee_after_days' => $data['late_fee_after_days'] ?? 0,
        ]);

        $structure->standards()->sync($data['standard_ids']);
        $structure->details()->delete();

        if (!empty($data['heads'])) {
            foreach ($data['heads'] as $head) {
                FeeStructureDetail::create([
                    'fee_structure_id' => $structure->id,
                    'fee_head_id' => $head['fee_head_id'],
                    'amount' => $head['amount'],
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Fee structure updated',
            'structure' => $structure->fresh()->load('details.feeHead', 'standards', 'academicYear'),
        ]);
    }

    public function destroy($id)
    {
        $structure = FeeStructure::findOrFail($id);
        $structure->standards()->detach();
        $structure->details()->delete();
        $structure->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fee structure deleted',
        ]);
    }

    public function copyFromPreviousYear(Request $request)
    {
        $data = $request->validate([
            'to_academic_year_id' => 'required|exists:academic_years,id',
            'from_academic_year_id' => 'required|exists:academic_years,id',
            'standard_id' => 'nullable|exists:standards,id',
            'to_standard_id' => 'nullable|exists:standards,id',
        ]);

        $query = FeeStructure::with('details', 'standards')
            ->where('academic_year_id', $data['from_academic_year_id']);

        if (!empty($data['standard_id'])) {
            $query->whereHas('standards', fn($q) => $q->where('standard_id', $data['standard_id']));
        }

        $sourceStructures = $query->get();
        $copied = 0;

        foreach ($sourceStructures as $source) {
            $destStdIds = $data['to_standard_id']
                ? [$data['to_standard_id']]
                : $source->standards->pluck('id')->toArray();

            $exists = FeeStructure::where('academic_year_id', $data['to_academic_year_id'])
                ->where('semester', $source->semester)
                ->where('type', $source->type)
                ->whereHas('standards', fn($q) => $q->whereIn('standard_id', $destStdIds))
                ->exists();

            if ($exists) {
                continue;
            }

            $newStructure = FeeStructure::create([
                'academic_year_id' => $data['to_academic_year_id'],
                'semester' => $source->semester,
                'type' => $source->type,
                'frequency' => $source->frequency,
                'late_fee_type' => $source->late_fee_type,
                'late_fee_amount' => $source->late_fee_amount,
                'late_fee_after_days' => $source->late_fee_after_days,
            ]);

            $newStructure->standards()->attach($destStdIds);

            foreach ($source->details as $detail) {
                FeeStructureDetail::create([
                    'fee_structure_id' => $newStructure->id,
                    'fee_head_id' => $detail->fee_head_id,
                    'amount' => $detail->amount,
                ]);
            }

            $copied++;
        }

        return response()->json([
            'success' => true,
            'message' => "{$copied} fee structures copied",
            'count' => $copied,
        ]);
    }

    public function getByYear($academicYearId)
    {
        $structures = FeeStructure::with('details.feeHead', 'standards')
            ->where('academic_year_id', $academicYearId)
            ->get();

        return response()->json($structures);
    }

    private function checkStandardOverlap($academicYearId, $type, $semester, $stdIds, $excludeId)
    {
        $query = FeeStructure::where('academic_year_id', $academicYearId)
            ->where('type', $type)
            ->where('semester', $semester)
            ->whereHas('standards', fn($q) => $q->whereIn('standard_id', $stdIds));

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $conflicting = $query->first();
        if ($conflicting) {
            $msg = FeeStructure::TYPES[$type] ?? $type;
            $semLabel = $semester ? " (Semester $semester)" : '';
            $stdNames = $conflicting->standards->pluck('name')->implode(', ');
            return "$msg$semLabel — one or more selected standards already have this fee structure ($stdNames)";
        }

        return null;
    }
}
