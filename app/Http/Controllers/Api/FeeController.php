<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeeHead;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\FeeStructureDetail;
use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    // ==================== Fee Heads ====================

    public function index()
    {
        return response()->json(FeeHead::orderBy('sort_order')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_gu' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        return response()->json(FeeHead::create($data), 201);
    }

    public function show($id)
    {
        return response()->json(FeeHead::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $feeHead = FeeHead::findOrFail($id);

        $data = $request->validate([
            'name_gu' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $feeHead->update($data);

        return response()->json($feeHead->fresh());
    }

    public function destroy($id)
    {
        FeeHead::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    // ==================== Fee Structures ====================

    public function structures(Request $request)
    {
        $query = FeeStructure::with('details.feeHead', 'standard', 'academicYear');

        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        return response()->json($query->get());
    }

    public function storeStructure(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_id' => 'required|exists:standards,id',
            'type' => 'required|in:tuition,transport,other',
            'frequency' => 'required|in:monthly,semesterly,yearly',
            'late_fee_type' => 'required|in:none,fixed,per_month',
            'late_fee_amount' => 'nullable|numeric|min:0',
            'late_fee_after_days' => 'nullable|integer|min:0',
            'heads' => 'nullable|array',
            'heads.*.fee_head_id' => 'required|exists:fee_heads,id',
            'heads.*.amount' => 'required|numeric|min:0',
        ]);

        $exists = FeeStructure::where('academic_year_id', $data['academic_year_id'])
            ->where('standard_id', $data['standard_id'])
            ->where('type', $data['type'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Duplicate fee structure for this academic year, standard and type'], 422);
        }

        $structure = FeeStructure::create([
            'academic_year_id' => $data['academic_year_id'],
            'standard_id' => $data['standard_id'],
            'type' => $data['type'],
            'frequency' => $data['frequency'],
            'late_fee_type' => $data['late_fee_type'],
            'late_fee_amount' => $data['late_fee_amount'] ?? 0,
            'late_fee_after_days' => $data['late_fee_after_days'] ?? 0,
        ]);

        if (!empty($data['heads'])) {
            foreach ($data['heads'] as $head) {
                FeeStructureDetail::create([
                    'fee_structure_id' => $structure->id,
                    'fee_head_id' => $head['fee_head_id'],
                    'amount' => $head['amount'],
                ]);
            }
        }

        return response()->json($structure->load('details.feeHead', 'standard', 'academicYear'), 201);
    }

    public function showStructure($id)
    {
        return response()->json(
            FeeStructure::with('details.feeHead', 'standard', 'academicYear')->findOrFail($id)
        );
    }

    public function destroyStructure($id)
    {
        $structure = FeeStructure::findOrFail($id);
        $structure->details()->delete();
        $structure->delete();
        return response()->json(null, 204);
    }

    // ==================== Student Fees ====================

    public function studentFees(Request $request)
    {
        $query = StudentFee::with('student', 'academicYear', 'feeStructure', 'payments');

        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->has('standard_id')) {
            $studentIds = Student::where('current_standard_id', $request->standard_id)->pluck('id');
            $query->whereIn('student_id', $studentIds);
        }

        if ($request->has('class_id')) {
            $studentIds = Student::where('current_class_id', $request->class_id)->pluck('id');
            $query->whereIn('student_id', $studentIds);
        }

        return response()->json($query->get());
    }

    public function storeStudentFee(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'fee_structure_id' => 'required|exists:fee_structures,id',
            'concession_amount' => 'nullable|numeric|min:0',
        ]);

        $feeStructure = FeeStructure::with('details')->findOrFail($data['fee_structure_id']);
        $totalAmount = $feeStructure->total_amount;
        $concession = $data['concession_amount'] ?? 0;
        $netAmount = $totalAmount - $concession;

        $studentFee = StudentFee::updateOrCreate(
            [
                'student_id' => $data['student_id'],
                'academic_year_id' => $data['academic_year_id'],
            ],
            [
                'fee_structure_id' => $data['fee_structure_id'],
                'total_amount' => $totalAmount,
                'concession_amount' => $concession,
                'net_amount' => $netAmount,
            ]
        );

        return response()->json($studentFee->load('student', 'academicYear', 'feeStructure'), 201);
    }

    // ==================== Fee Payments ====================

    public function payments(Request $request)
    {
        $query = FeePayment::with('student', 'academicYear', 'receiver');

        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function storePayment(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'amount_paid' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank,cheque,online',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $lastSeq = FeePayment::where('academic_year_id', $data['academic_year_id'])
            ->whereDate('created_at', today())
            ->count();

        $receiptNumber = 'REC-' . $data['academic_year_id'] . '-' . now()->format('Ymd') . '-' . str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);

        $payment = FeePayment::create([
            'student_id' => $data['student_id'],
            'academic_year_id' => $data['academic_year_id'],
            'receipt_number' => $receiptNumber,
            'amount_paid' => $data['amount_paid'],
            'payment_date' => $data['payment_date'],
            'payment_method' => $data['payment_method'],
            'reference_number' => $data['reference_number'],
            'notes' => $data['notes'],
            'received_by' => auth()->id(),
        ]);

        return response()->json($payment->load('student', 'academicYear', 'receiver'), 201);
    }

    // ==================== Reports ====================

    public function summary(Request $request)
    {
        $request->validate(['academic_year_id' => 'required|exists:academic_years,id']);

        $yearId = $request->academic_year_id;
        $fees = StudentFee::where('academic_year_id', $yearId)->get();
        $paymentTotals = FeePayment::where('academic_year_id', $yearId)
            ->selectRaw('student_id, SUM(amount_paid) as total_paid')
            ->groupBy('student_id')
            ->pluck('total_paid', 'student_id');

        return response()->json([
            'total_assigned' => $fees->sum('net_amount'),
            'total_collected' => $paymentTotals->sum(),
            'total_due' => $fees->sum('net_amount') - $paymentTotals->sum(),
            'total_concession' => $fees->sum('concession_amount'),
        ]);
    }

    public function dueList(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_id' => 'nullable|exists:standards,id',
        ]);

        $query = StudentFee::where('student_fees.academic_year_id', $request->academic_year_id)
            ->join('students', 'student_fees.student_id', '=', 'students.id')
            ->where('students.status', '!=', 'alumni');

        if ($request->has('standard_id')) {
            $query->where('students.current_standard_id', $request->standard_id);
        }

        $results = $query->select('student_fees.*', 'students.gr_number', 'students.full_name_gu', 'students.full_name_en')
            ->get()
            ->map(function ($sf) {
                $paid = FeePayment::where('student_id', $sf->student_id)
                    ->where('academic_year_id', $sf->academic_year_id)
                    ->sum('amount_paid');
                $sf->paid = $paid;
                $sf->due = max(0, $sf->net_amount - $paid);
                return $sf;
            })
            ->filter(fn($sf) => $sf->due > 0)
            ->sortByDesc('due')
            ->values();

        return response()->json($results);
    }

    public function collectionReport(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $query = FeePayment::with('student', 'receiver')
            ->where('academic_year_id', $request->academic_year_id)
            ->whereBetween('payment_date', [$request->from_date, $request->to_date]);

        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->orderBy('payment_date')->get();

        return response()->json([
            'payments' => $payments,
            'total_amount' => $payments->sum('amount_paid'),
            'count' => $payments->count(),
        ]);
    }
}
