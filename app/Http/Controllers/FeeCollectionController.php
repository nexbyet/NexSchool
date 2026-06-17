<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\FeeCarryForward;
use App\Models\FeePayment;
use App\Models\Standard;
use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Http\Request;

class FeeCollectionController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $standards = Standard::orderBy('sort_order')->get();
        return view('fees.collection.index', compact('academicYears', 'standards'));
    }

    public function getStudents(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_id' => 'required|exists:standards,id',
            'class_id' => 'nullable|exists:school_classes,id',
            'semester' => 'nullable|in:1,2',
            'search' => 'nullable|string|max:255',
        ]);

        $semester = $data['semester'] ?? null;

        $query = Student::where('current_standard_id', $data['standard_id'])
            ->where('status', '!=', 'alumni');

        if (!empty($data['class_id'])) {
            $query->where('current_class_id', $data['class_id']);
        }

        if (!empty($data['search'])) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('gr_number', 'LIKE', "%{$search}%")
                  ->orWhere('full_name_gu', 'LIKE', "%{$search}%")
                  ->orWhere('full_name_en', 'LIKE', "%{$search}%")
                  ->orWhere('father_name_gu', 'LIKE', "%{$search}%")
                  ->orWhere('father_name_en', 'LIKE', "%{$search}%");
            });
        }

        $studentsList = $query->defaultSort()->get();
        $studentIds = $studentsList->pluck('id');

        $feeQuery = StudentFee::whereIn('student_id', $studentIds)
            ->where('academic_year_id', $data['academic_year_id']);
        if ($semester) {
            $feeQuery->where('semester', $semester);
        }
        $allFees = $feeQuery->with('payments', 'feeStructure')
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

    public function collect(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'student_fee_id' => 'required|exists:student_fees,id',
            'amount_paid' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank,cheque,online',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $sf = StudentFee::with('feeStructure')->find($data['student_fee_id']);
        $feeType = $sf?->feeStructure?->type ?? 'other';
        $prefix = 'REC';
        if ($feeType === 'tuition') $prefix = 'SCH';
        elseif ($feeType === 'transport') $prefix = 'BUS';

        $lastSeq = FeePayment::where('receipt_number', 'LIKE', $prefix . '%')
            ->count();

        $receiptNumber = $prefix . str_pad($lastSeq + 1, 5, '0', STR_PAD_LEFT);

        $payment = FeePayment::create([
            'student_id' => $data['student_id'],
            'academic_year_id' => $data['academic_year_id'],
            'student_fee_id' => $data['student_fee_id'],
            'semester' => $sf->semester,
            'receipt_number' => $receiptNumber,
            'amount_paid' => $data['amount_paid'],
            'payment_date' => $data['payment_date'],
            'payment_method' => $data['payment_method'],
            'reference_number' => $data['reference_number'],
            'notes' => $data['notes'],
            'received_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment collected',
            'payment' => $payment->load('student', 'academicYear', 'receiver'),
        ]);
    }

    public function collectMulti(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank,cheque,online',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'payments' => 'required|array|min:1',
            'payments.*.student_fee_id' => 'required|exists:student_fees,id',
            'payments.*.amount_paid' => 'required|numeric|min:0',
        ]);

        $createdIds = [];
        foreach ($data['payments'] as $pdata) {
            if ($pdata['amount_paid'] <= 0) continue;

            $sf = StudentFee::with('feeStructure')->find($pdata['student_fee_id']);
            $feeType = $sf?->feeStructure?->type ?? 'other';
            $prefix = $feeType === 'tuition' ? 'SCH' : ($feeType === 'transport' ? 'BUS' : 'REC');

            $lastSeq = FeePayment::where('receipt_number', 'LIKE', $prefix . '%')
                ->count();

            $receiptNumber = $prefix . str_pad($lastSeq + 1, 5, '0', STR_PAD_LEFT);

            $payment = FeePayment::create([
                'student_id' => $data['student_id'],
                'academic_year_id' => $data['academic_year_id'],
                'student_fee_id' => $pdata['student_fee_id'],
                'semester' => $sf->semester,
                'receipt_number' => $receiptNumber,
                'amount_paid' => $pdata['amount_paid'],
                'payment_date' => $data['payment_date'],
                'payment_method' => $data['payment_method'],
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
                'received_by' => auth()->id(),
            ]);

            $createdIds[] = $payment->id;
        }

        if (empty($createdIds)) {
            return response()->json(['success' => false, 'message' => 'કોઈ ચુકવણી થઈ નથી.'], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'ચુકવણી સફળ!',
            'payment_ids' => $createdIds,
        ]);
    }

    public function receipt($studentId, $academicYearId)
    {
        $student = Student::with('currentStandard', 'currentClass')->findOrFail($studentId);
        $academicYear = AcademicYear::findOrFail($academicYearId);

        $paymentIds = request()->query('payment_ids');
        if ($paymentIds) {
            $ids = explode(',', $paymentIds);
            $payments = FeePayment::whereIn('id', $ids)
                ->where('student_id', $studentId)
                ->where('academic_year_id', $academicYearId)
                ->with('studentFee.feeStructure')
                ->orderBy('student_fee_id')
                ->get();
        } else {
            $paymentId = request()->query('payment_id');
            if ($paymentId) {
                $payments = FeePayment::where('id', $paymentId)
                    ->where('student_id', $studentId)
                    ->where('academic_year_id', $academicYearId)
                    ->with('studentFee.feeStructure')
                    ->get();
            } else {
                $payments = collect();
            }
        }

        if ($payments->isEmpty()) {
            abort(404, 'No payments found.');
        }

        $allStudentFees = StudentFee::where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->with('feeStructure.details.feeHead')
            ->get();

        $typeData = [];
        foreach ($payments->groupBy(fn($p) => $p->student_fee_id) as $sfId => $feePayments) {
            $sf = $allStudentFees->first(fn($s) => $s->id === $sfId);
            if (!$sf) continue;
            $type = $sf->feeStructure?->type ?? 'other';
            $isCf = !$sf->fee_structure_id;
            $paidNow = $feePayments->sum('amount_paid');
            $totalPaid = FeePayment::where('student_id', $studentId)
                ->where('academic_year_id', $academicYearId)
                ->where('student_fee_id', $sf->id)
                ->sum('amount_paid');
            $netAmount = $sf->net_amount;
            $prevPaid = $totalPaid - $paidNow;
            $due = max(0, $netAmount - $totalPaid);
            $waived = $sf->is_waived ? $netAmount : 0;
            $semVal = $sf->feeStructure?->semester;

            $typeData[$sf->id] = [
                'label' => $isCf ? 'કેરી ફોરવર્ડ' : ($type === 'tuition' ? 'શાળા ફી' : ($type === 'transport' ? 'બસ ફી' : 'અન્ય')),
                'payments' => $feePayments,
                'heads' => $sf->feeStructure?->details ?? collect(),
                'net_amount' => $netAmount,
                'paid_now' => $paidNow,
                'prev_paid' => $prevPaid,
                'total_paid' => $totalPaid,
                'waived' => $waived,
                'due' => $due,
                'sf' => $sf,
                'semester' => $semVal,
            ];
        }

        return view('fees.collection.receipt', compact('student', 'academicYear', 'typeData'));
    }

    public function studentHistory(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'student_fee_id' => 'nullable|exists:student_fees,id',
            'semester' => 'nullable|in:1,2',
        ]);

        $semester = $data['semester'] ?? null;

        $student = Student::find($data['student_id']);

        $feeQuery = StudentFee::where('student_id', $data['student_id'])
            ->where('academic_year_id', $data['academic_year_id']);
        if ($semester) {
            $feeQuery->where('semester', $semester);
        }
        $studentFees = $feeQuery->with('feeStructure')->get();

        $specificFee = null;
        if (!empty($data['student_fee_id'])) {
            $specificFee = $studentFees->firstWhere('id', $data['student_fee_id']);
            if ($specificFee) {
                $specificFee->load('feeStructure.details.feeHead');
            }
        }

        $paymentsQuery = FeePayment::where('student_id', $data['student_id'])
            ->where('academic_year_id', $data['academic_year_id']);

        if (!empty($data['student_fee_id'])) {
            $paymentsQuery->where('student_fee_id', $data['student_fee_id']);
        }

        $payments = $paymentsQuery->orderBy('payment_date')->get();

        $carryForwards = FeeCarryForward::where('student_id', $data['student_id'])
            ->where(function ($q) use ($data) {
                $q->where('to_academic_year_id', $data['academic_year_id'])
                  ->orWhere('from_academic_year_id', $data['academic_year_id']);
            })
            ->get();

        $totalPaid = $payments->sum('amount_paid');
        $totalDue = 0;
        $dueForFee = 0;
        foreach ($studentFees as $sf) {
            $paidForFee = FeePayment::where('student_id', $data['student_id'])
                ->where('academic_year_id', $data['academic_year_id'])
                ->where('student_fee_id', $sf->id)
                ->sum('amount_paid');
            $feeDue = max(0, $sf->net_amount - $paidForFee);
            $totalDue += $feeDue;
            if ($specificFee && $sf->id === $specificFee->id) {
                $dueForFee = $feeDue;
            }
        }

        $studentData = $student ? [
            'id' => $student->id,
            'gr_number' => $student->gr_number,
            'full_name_gu' => $student->full_name_gu,
            'full_name_en' => $student->full_name_en,
            'current_standard' => $student->currentStandard ? ['name' => $student->currentStandard->name] : null,
            'current_class' => $student->currentClass ? ['name' => $student->currentClass->name] : null,
        ] : null;

        return response()->json([
            'success' => true,
            'student' => $studentData,
            'fees' => $studentFees,
            'fee' => $specificFee,
            'payments' => $payments,
            'carry_forwards' => $carryForwards,
            'total_paid' => $totalPaid,
            'due_amount' => $specificFee ? $dueForFee : $totalDue,
        ]);
    }
}
