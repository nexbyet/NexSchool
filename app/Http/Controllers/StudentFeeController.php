<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\FeeStructure;
use App\Models\Standard;
use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Http\Request;

class StudentFeeController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $standards = Standard::orderBy('sort_order')->get();
        return view('fees.assignments.index', compact('academicYears', 'standards'));
    }

    public function getStudents(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_id' => 'required|exists:standards,id',
            'class_id' => 'nullable|exists:school_classes,id',
        ]);

        $query = Student::where('current_standard_id', $data['standard_id'])
            ->where('status', '!=', 'alumni')
            ->defaultSort();

        if (!empty($data['class_id'])) {
            $query->where('current_class_id', $data['class_id']);
        }

        $studentsList = $query->get();
        $studentIds = $studentsList->pluck('id');

        $allFees = StudentFee::whereIn('student_id', $studentIds)
            ->where('academic_year_id', $data['academic_year_id'])
            ->with('payments', 'feeStructure.details')
            ->get()
            ->groupBy('student_id');

        $students = $studentsList->map(function ($student) use ($data, $allFees) {
            $studentFees = isset($allFees[$student->id]) ? $allFees[$student->id]->values() : [];

            return [
                'id' => $student->id,
                'gr_number' => $student->gr_number,
                'full_name_gu' => $student->full_name_gu,
                'full_name_en' => $student->full_name_en,
                'father_name_gu' => $student->father_name_gu,
                'father_name_en' => $student->father_name_en,
                'standard' => $student->currentStandard->name ?? '',
                'class' => $student->currentClass->name ?? '',
                'fees' => $studentFees,
            ];
        });

        return response()->json([
            'success' => true,
            'students' => $students,
        ]);
    }

    public function bulkAssign(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_id' => 'required|exists:standards,id',
            'class_id' => 'nullable|exists:school_classes,id',
            'fee_structure_ids' => 'required|array',
            'fee_structure_ids.*' => 'exists:fee_structures,id',
            'concession_amount' => 'nullable|numeric|min:0',
            'is_waived' => 'boolean',
            'excluded_fee_heads' => 'nullable|array',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $concession = $data['concession_amount'] ?? 0;
        $isWaived = $data['is_waived'] ?? false;
        $assigned = 0;

        foreach ($data['fee_structure_ids'] as $fsId) {
            $feeStructure = FeeStructure::with('details')->findOrFail($fsId);
            $totalAmount = $feeStructure->total_amount;
            $excluded = $data['excluded_fee_heads'][$fsId] ?? [];

            $excludedTotal = 0;
            if (!empty($excluded)) {
                foreach ($feeStructure->details as $detail) {
                    if (in_array($detail->fee_head_id, $excluded)) {
                        $excludedTotal += $detail->amount;
                    }
                }
            }

            $netAmount = $isWaived ? 0 : max(0, $totalAmount - $excludedTotal - $concession);

            foreach ($data['student_ids'] as $studentId) {
                StudentFee::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'academic_year_id' => $data['academic_year_id'],
                        'fee_structure_id' => $fsId,
                        'semester' => $feeStructure->semester,
                    ],
                    [
                        'total_amount' => $totalAmount,
                        'concession_amount' => $concession,
                        'net_amount' => $netAmount,
                        'is_waived' => $isWaived,
                        'excluded_fee_heads' => $excluded,
                    ]
                );
                $assigned++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$assigned} fee assignments saved",
            'count' => $assigned,
        ]);
    }

    public function destroy($id)
    {
        $sf = StudentFee::findOrFail($id);
        $sf->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fee assignment removed',
        ]);
    }

    public function update(Request $request, $id)
    {
        $sf = StudentFee::with('feeStructure.details')->findOrFail($id);

        $data = $request->validate([
            'concession_amount' => 'nullable|numeric|min:0',
            'is_waived' => 'boolean',
            'excluded_fee_heads' => 'nullable|array',
            'excluded_fee_heads.*' => 'exists:fee_heads,id',
        ]);

        $sf->is_waived = $data['is_waived'] ?? false;
        $sf->excluded_fee_heads = $data['excluded_fee_heads'] ?? [];
        $sf->concession_amount = $data['concession_amount'] ?? $sf->concession_amount;

        if ($sf->is_waived) {
            $sf->net_amount = 0;
        } else {
            $excluded = $sf->excluded_fee_heads ?? [];
            $excludedTotal = 0;
            if (!empty($excluded) && $sf->feeStructure) {
                foreach ($sf->feeStructure->details as $detail) {
                    if (in_array($detail->fee_head_id, $excluded)) {
                        $excludedTotal += $detail->amount;
                    }
                }
            }
            $effectiveTotal = $sf->total_amount - $excludedTotal;
            $sf->net_amount = max(0, $effectiveTotal - $sf->concession_amount);
        }

        $sf->save();

        return response()->json([
            'success' => true,
            'message' => 'Fee assignment updated',
            'fee' => $sf->fresh()->load('payments', 'feeStructure.details'),
        ]);
    }

    public function getUnassignedStudents(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_id' => 'required|exists:standards,id',
            'class_id' => 'nullable|exists:school_classes,id',
        ]);

        $assignedIds = StudentFee::where('academic_year_id', $data['academic_year_id'])
            ->pluck('student_id');

        $query = Student::where('current_standard_id', $data['standard_id'])
            ->where('status', '!=', 'alumni')
            ->whereNotIn('id', $assignedIds)
            ->defaultSort();

        if (!empty($data['class_id'])) {
            $query->where('current_class_id', $data['class_id']);
        }

        $students = $query->get()->map(function ($student) {
            return [
                'id' => $student->id,
                'gr_number' => $student->gr_number,
                'full_name_gu' => $student->full_name_gu,
                'full_name_en' => $student->full_name_en,
                'father_name_gu' => $student->father_name_gu,
                'father_name_en' => $student->father_name_en,
                'standard' => $student->currentStandard->name ?? '',
                'class' => $student->currentClass->name ?? '',
            ];
        });

        return response()->json([
            'success' => true,
            'students' => $students,
        ]);
    }
}
