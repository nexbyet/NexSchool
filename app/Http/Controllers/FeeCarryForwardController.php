<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\FeeCarryForward;
use App\Models\Standard;
use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Http\Request;

class FeeCarryForwardController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $standards = Standard::orderBy('sort_order')->get();
        return view('fees.carry-forward.index', compact('academicYears', 'standards'));
    }

    public function students(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_id' => 'required|exists:standards,id',
            'class_id' => 'nullable|exists:school_classes,id',
        ]);

        $query = Student::where('current_standard_id', $data['standard_id'])
            ->where('status', '!=', 'alumni');

        if (!empty($data['class_id'])) {
            $query->where('current_class_id', $data['class_id']);
        }

        $studentsList = $query->defaultSort()->get();
        $studentIds = $studentsList->pluck('id');

        $carryForwards = FeeCarryForward::whereIn('student_id', $studentIds)
            ->where('to_academic_year_id', $data['academic_year_id'])
            ->with('fromAcademicYear')
            ->get()
            ->keyBy('student_id');

        $carryForwardFees = StudentFee::whereIn('student_id', $studentIds)
            ->where('academic_year_id', $data['academic_year_id'])
            ->whereNull('fee_structure_id')
            ->get()
            ->keyBy('student_id');

        $students = $studentsList->map(function ($student) use ($carryForwards, $carryForwardFees) {
            $cf = $carryForwards->get($student->id);
            $cfFee = $carryForwardFees->get($student->id);
            return [
                'id' => $student->id,
                'gr_number' => $student->gr_number,
                'full_name_gu' => $student->full_name_gu,
                'full_name_en' => $student->full_name_en,
                'father_name_gu' => $student->father_name_gu,
                'father_name_en' => $student->father_name_en,
                'standard' => $student->currentStandard->name ?? '',
                'class' => $student->currentClass->name ?? '',
                'carry_forward' => $cf ? [
                    'id' => $cf->id,
                    'amount' => (float) $cf->amount,
                    'from_year_id' => $cf->from_academic_year_id,
                    'from_year' => $cf->fromAcademicYear->year ?? '',
                ] : null,
                'carry_forward_fee_id' => $cfFee ? $cfFee->id : null,
            ];
        });

        return response()->json([
            'success' => true,
            'students' => $students,
            'academic_years' => AcademicYear::orderBy('year', 'desc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'from_academic_year_id' => 'required|exists:academic_years,id|different:academic_year_id',
            'amount' => 'required|numeric|min:0',
        ]);

        // Upsert FeeCarryForward record
        $cf = FeeCarryForward::updateOrCreate(
            [
                'student_id' => $data['student_id'],
                'to_academic_year_id' => $data['academic_year_id'],
            ],
            [
                'from_academic_year_id' => $data['from_academic_year_id'],
                'amount' => $data['amount'],
            ]
        );

        // Upsert StudentFee record (fee_structure_id = null for carry forward)
        $studentFee = StudentFee::updateOrCreate(
            [
                'student_id' => $data['student_id'],
                'academic_year_id' => $data['academic_year_id'],
                'fee_structure_id' => null,
            ],
            [
                'total_amount' => $data['amount'],
                'concession_amount' => 0,
                'net_amount' => $data['amount'],
                'is_waived' => false,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Carry forward saved',
            'carry_forward' => $cf->load('fromAcademicYear'),
            'student_fee_id' => $studentFee->id,
        ]);
    }

    public function destroy($id)
    {
        $cf = FeeCarryForward::findOrFail($id);
        $studentId = $cf->student_id;
        $yearId = $cf->to_academic_year_id;

        // Remove the linked StudentFee record
        StudentFee::where('student_id', $studentId)
            ->where('academic_year_id', $yearId)
            ->whereNull('fee_structure_id')
            ->delete();

        $cf->delete();

        return response()->json([
            'success' => true,
            'message' => 'Carry forward removed',
        ]);
    }
}
