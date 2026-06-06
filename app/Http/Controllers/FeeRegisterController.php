<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\FeeCarryForward;
use App\Models\FeePayment;
use App\Models\SchoolClass;
use App\Models\Standard;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\StudentRoute;
use Illuminate\Http\Request;

class FeeRegisterController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $standards = Standard::orderBy('sort_order')->get();
        return view('fees.register.index', compact('academicYears', 'standards'));
    }

    public function print(Request $request)
    {
        $data = $request->validate([
            'standard_id' => 'required|exists:standards,id',
            'class_id' => 'required|exists:school_classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester' => 'nullable|in:1,2',
            'lang' => 'nullable|in:gu,en',
        ]);

        $lang = $data['lang'] ?? 'gu';
        $semester = $data['semester'] ?? null;
        $standard = Standard::findOrFail($data['standard_id']);
        $class = SchoolClass::findOrFail($data['class_id']);
        $academicYear = AcademicYear::findOrFail($data['academic_year_id']);

        $students = Student::where('current_standard_id', $data['standard_id'])
            ->where('current_class_id', $data['class_id'])
            ->where('status', 'active')
            ->orderBy('gr_number')
            ->get();

        $studentIds = $students->pluck('id');

        $carryForwards = FeeCarryForward::whereIn('student_id', $studentIds)
            ->where('to_academic_year_id', $data['academic_year_id'])
            ->get()
            ->groupBy('student_id');

        // Semester 1 data (only needed for sem=2 balance)
        $sem1Fees = collect();
        $sem1Payments = collect();
        if ($semester == 2) {
            $sem1Fees = StudentFee::whereIn('student_id', $studentIds)
                ->where('academic_year_id', $data['academic_year_id'])
                ->where('semester', 1)
                ->with('feeStructure')
                ->get()
                ->groupBy('student_id');

            $sem1Payments = FeePayment::whereIn('student_id', $studentIds)
                ->where('academic_year_id', $data['academic_year_id'])
                ->where('semester', 1)
                ->with('studentFee.feeStructure')
                ->get()
                ->groupBy('student_id');
        }

        // Current semester fees
        $studentFeeQuery = StudentFee::whereIn('student_id', $studentIds)
            ->where('academic_year_id', $data['academic_year_id']);
        if ($semester) {
            $studentFeeQuery->where('semester', $semester);
        }
        $studentFees = $studentFeeQuery->with('feeStructure')
            ->get()
            ->groupBy('student_id');

        // Current semester payments
        $paymentQuery = FeePayment::whereIn('student_id', $studentIds)
            ->where('academic_year_id', $data['academic_year_id']);
        if ($semester) {
            $paymentQuery->where('semester', $semester);
        }
        $payments = $paymentQuery->with('studentFee.feeStructure')
            ->orderBy('payment_date')
            ->orderBy('id')
            ->get()
            ->groupBy('student_id');

        // Load student routes for bus assignment status
        $studentRoutes = StudentRoute::whereIn('student_id', $studentIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('student_id');

        $registerData = [];

        foreach ($students as $student) {
            $prevDues = collect($carryForwards->get($student->id, collect()))->sum('amount');

            // Current semester fee
            $sfRecords = $studentFees->get($student->id, collect());
            $isWaived = $sfRecords->contains(function ($sf) {
                return $sf->is_waived;
            });
            $currentFee = $sfRecords->sum(function ($sf) {
                return $sf->is_waived ? 0 : $sf->net_amount;
            });

            // Semester 1 balance (for sem=2 register)
            $sem1Balance = 0;
            if ($semester == 2) {
                $s1Fees = $sem1Fees->get($student->id, collect());
                $sem1TotalFee = $s1Fees->sum(function ($sf) {
                    return $sf->is_waived ? 0 : $sf->net_amount;
                });
                $s1Pmts = $sem1Payments->get($student->id, collect());
                $sem1Paid = $s1Pmts->sum('amount_paid');
                $sem1Balance = max(0, $sem1TotalFee - $sem1Paid);
            }

            $totalPayable = $prevDues + $sem1Balance + $currentFee;

            $studentPayments = $payments->get($student->id, collect());

            $schoolPayments = [];
            $busPayments = [];
            foreach ($studentPayments as $p) {
                $type = $p->studentFee?->feeStructure?->type;
                $entry = [
                    'receipt' => $p->receipt_number,
                    'amount' => $p->amount_paid,
                    'date' => $p->payment_date,
                ];
                if ($type === 'transport') {
                    $busPayments[] = $entry;
                } else {
                    $schoolPayments[] = $entry;
                }
            }

            $totalSchoolPaid = collect($schoolPayments)->sum('amount');
            $totalBusPaid = collect($busPayments)->sum('amount');
            $totalPaid = $totalSchoolPaid + $totalBusPaid;
            $balance = max(0, $totalPayable - $totalPaid);

            // Build note column
            $note = '';
            $sr = $studentRoutes->get($student->id);
            $hasBusRoute = $sr !== null;
            $hasRte = !empty($student->admission_under_rte) && in_array(strtolower($student->admission_under_rte), ['yes', 'ha', '1', 'true', 'on']);

            if ($hasBusRoute && $hasRte) {
                $isOneWay = (!$sr->pickup || !$sr->drop);
                $note = $isOneWay ? 'RTE-B1' : 'RTE-B';
            } elseif ($hasBusRoute) {
                $isOneWay = (!$sr->pickup || !$sr->drop);
                $note = $isOneWay ? 'B1' : 'B';
            } elseif ($hasRte) {
                $note = 'RTE';
            }

            $registerData[] = [
                'student' => $student,
                'is_waived' => $isWaived,
                'prev_dues' => $prevDues,
                'sem1_balance' => $sem1Balance,
                'current_fee' => $currentFee,
                'total_payable' => $totalPayable,
                'school_payments' => $schoolPayments,
                'bus_payments' => $busPayments,
                'total_paid' => $totalPaid,
                'balance' => $balance,
                'note' => $note,
            ];
        }

        $schoolFeeCols = 4;
        $busFeeCols = 4;

        $school = \App\Models\SchoolSetting::find(1);

        return view('fees.register.print', compact(
            'registerData', 'standard', 'class', 'academicYear',
            'schoolFeeCols', 'busFeeCols', 'lang', 'semester', 'school'
        ));
    }
}
