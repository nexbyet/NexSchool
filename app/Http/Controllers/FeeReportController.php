<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\FeeCarryForward;
use App\Models\FeePayment;
use App\Models\SchoolSetting;
use App\Models\Standard;
use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Http\Request;

class FeeReportController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $standards = Standard::orderBy('sort_order')->get();
        return view('fees.reports.index', compact('academicYears', 'standards'));
    }

    public function summary(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester' => 'nullable|in:1,2',
        ]);

        $yearId = $data['academic_year_id'];
        $semester = $data['semester'] ?? null;

        $feesQuery = StudentFee::where('academic_year_id', $yearId);
        if ($semester) $feesQuery->where('semester', $semester);
        $fees = $feesQuery->with('feeStructure')->get();

        $paymentQuery = FeePayment::where('academic_year_id', $yearId);
        if ($semester) $paymentQuery->where('semester', $semester);
        $paymentTotals = $paymentQuery
            ->selectRaw('student_fee_id, SUM(amount_paid) as total_paid')
            ->groupBy('student_fee_id')
            ->pluck('total_paid', 'student_fee_id');

        $typeLabels = ['tuition' => 'શાળા ફી', 'transport' => 'બસ ફી', 'other' => 'અન્ય'];
        $byType = [];
        foreach ($fees as $sf) {
            $type = $sf->feeStructure?->type ?? 'other';
            if (!isset($byType[$type])) $byType[$type] = ['assigned' => 0, 'collected' => 0, 'concession' => 0, 'due' => 0];
            $byType[$type]['assigned'] += $sf->net_amount;
            $byType[$type]['concession'] += $sf->concession_amount;
            $collected = $paymentTotals->get($sf->id, 0);
            $byType[$type]['collected'] += $collected;
            $byType[$type]['due'] += max(0, $sf->net_amount - $collected);
        }

        $totalAssigned = $fees->sum('net_amount');
        $totalConcession = $fees->sum('concession_amount');
        $totalCollected = $paymentTotals->sum();
        $totalDue = $fees->sum(function ($sf) use ($paymentTotals) {
            return max(0, $sf->net_amount - $paymentTotals->get($sf->id, 0));
        });

        $standards = Standard::orderBy('sort_order')->get();
        $perStandard = [];

        foreach ($standards as $standard) {
            $studentIds = Student::where('current_standard_id', $standard->id)->pluck('id');
            $stdFees = $fees->whereIn('student_id', $studentIds);
            $stdAssigned = $stdFees->sum('net_amount');
            $stdCollected = 0;
            $stdDue = 0;

            foreach ($stdFees as $sf) {
                $c = $paymentTotals->get($sf->id, 0);
                $stdCollected += $c;
                $stdDue += max(0, $sf->net_amount - $c);
            }

            $stdConcession = $stdFees->sum('concession_amount');

            if ($stdAssigned > 0 || $stdConcession > 0) {
                $perStandard[] = [
                    'standard' => $standard->name,
                    'assigned' => $stdAssigned,
                    'collected' => $stdCollected,
                    'concession' => $stdConcession,
                    'due' => $stdDue,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'total_assigned' => $totalAssigned,
            'total_collected' => $totalCollected,
            'total_due' => $totalDue,
            'total_concession' => $totalConcession,
            'per_standard' => $perStandard,
            'by_type' => $byType,
        ]);
    }

    public function dueList(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_id' => 'nullable|exists:standards,id',
            'class_id' => 'nullable|exists:school_classes,id',
            'semester' => 'nullable|in:1,2',
        ]);

        $yearId = $data['academic_year_id'];

        $query = StudentFee::with('feeStructure')
            ->where('student_fees.academic_year_id', $yearId)
            ->join('students', 'student_fees.student_id', '=', 'students.id')
            ->where('students.status', '!=', 'alumni');

        if (!empty($data['semester'])) {
            $query->where('student_fees.semester', $data['semester']);
        }

        if (!empty($data['standard_id'])) {
            $query->where('students.current_standard_id', $data['standard_id']);
        }

        if (!empty($data['class_id'])) {
            $query->where('students.current_class_id', $data['class_id']);
        }

        $rows = $query->select(
            'student_fees.*',
            'students.gr_number',
            'students.full_name_gu',
            'students.full_name_en',
            'students.mobile',
            'students.father_name_gu',
            'students.father_name_en',
            'students.current_standard_id',
            'students.current_class_id'
        )->get();

        // Group by student_id, then by semester, then by fee type
        $grouped = [];
        $feeTypes = [];
        $semestersFound = [];

        foreach ($rows as $sf) {
            $sid = $sf->student_id;
            $sem = $sf->semester ?? 1;
            $type = $sf->feeStructure?->type ?? 'other';

            $feeTypes[$type] = true;
            $semestersFound[$sem] = true;

            if (!isset($grouped[$sid])) {
                $grouped[$sid] = [
                    'id' => $sf->student_id,
                    'gr_number' => $sf->gr_number,
                    'full_name_gu' => $sf->full_name_gu,
                    'full_name_en' => $sf->full_name_en,
                    'mobile' => $sf->mobile,
                    'father_name_gu' => $sf->father_name_gu,
                    'father_name_en' => $sf->father_name_en,
                    'current_standard_id' => $sf->current_standard_id,
                    'current_class_id' => $sf->current_class_id,
                    'entries' => [],
                    'total_due' => 0,
                ];
            }

            $paid = FeePayment::where('student_id', $sf->student_id)
                ->where('academic_year_id', $yearId)
                ->where('student_fee_id', $sf->id)
                ->sum('amount_paid');

            $due = max(0, $sf->net_amount - $paid);

            $grouped[$sid]['entries']["sem_{$sem}_{$type}"] = [
                'net_amount' => (float) $sf->net_amount,
                'paid_amount' => (float) $paid,
                'due_amount' => (float) $due,
            ];
            $grouped[$sid]['total_due'] += $due;
        }

        // Load student relations
        $studentIds = array_keys($grouped);
        $students = \App\Models\Student::whereIn('id', $studentIds)
            ->with('currentStandard', 'currentClass')
            ->get()->keyBy('id');

        $studentsData = [];
        foreach ($grouped as $sid => $g) {
            $stu = $students->get($sid);
            $g['student'] = $stu ? [
                'current_standard' => $stu->currentStandard,
                'current_class' => $stu->currentClass,
            ] : null;
            $studentsData[] = $g;
        }

        // Sort by total_due descending
        usort($studentsData, fn($a, $b) => $b['total_due'] <=> $a['total_due']);

        // Determine which semester-type combos exist
        $typeList = array_keys($feeTypes);
        $semList = array_keys($semestersFound);
        sort($semList);
        $typeLabels = ['tuition' => 'શાળા ફી', 'transport' => 'બસ ફી', 'other' => 'અન્ય'];

        return response()->json([
            'success' => true,
            'students' => $studentsData,
            'fee_types' => $typeList,
            'semesters' => $semList,
            'type_labels' => $typeLabels,
        ]);
    }

    public function collectionReport(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'payment_method' => 'nullable|in:cash,bank,cheque,online',
            'semester' => 'nullable|in:1,2',
        ]);

        $query = FeePayment::with('student.currentStandard', 'student.currentClass', 'receiver')
            ->where('academic_year_id', $data['academic_year_id'])
            ->whereBetween('payment_date', [$data['from_date'], $data['to_date']]);

        if (!empty($data['semester'])) {
            $query->where('semester', $data['semester']);
        }

        if (!empty($data['payment_method'])) {
            $query->where('payment_method', $data['payment_method']);
        }

        $payments = $query->orderBy('payment_date')->get();
        $totalAmount = $payments->sum('amount_paid');

        return response()->json([
            'success' => true,
            'payments' => $payments,
            'total_amount' => $totalAmount,
            'count' => $payments->count(),
        ]);
    }

    public function searchStudents(Request $request)
    {
        $search = $request->input('search', '');
        $query = Student::where('status', '!=', 'alumni');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('gr_number', 'LIKE', "%{$search}%")
                  ->orWhere('full_name_gu', 'LIKE', "%{$search}%")
                  ->orWhere('full_name_en', 'LIKE', "%{$search}%");
            });
        }

        $students = $query->orderBy('full_name_gu')->limit(50)->get()->map(function ($s) {
            return [
                'id' => $s->id,
                'gr_number' => $s->gr_number,
                'full_name_gu' => $s->full_name_gu,
                'full_name_en' => $s->full_name_en,
                'mobile' => $s->mobile,
            ];
        });

        return response()->json(['success' => true, 'students' => $students]);
    }

    public function studentStatement(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester' => 'nullable|in:1,2',
        ]);

        $semester = $data['semester'] ?? null;
        $student = Student::findOrFail($data['student_id']);

        $feesQuery = StudentFee::where('student_id', $data['student_id'])
            ->where('academic_year_id', $data['academic_year_id'])
            ->whereNotNull('fee_structure_id');
        if ($semester) $feesQuery->where('semester', $semester);
        $studentFees = $feesQuery->with('feeStructure.details.feeHead')->get();

        $payQuery = FeePayment::where('student_id', $data['student_id'])
            ->where('academic_year_id', $data['academic_year_id']);
        if ($semester) $payQuery->where('semester', $semester);
        $payments = $payQuery->with('studentFee.feeStructure')->orderBy('payment_date')->get();

        $carryForwards = FeeCarryForward::where('student_id', $data['student_id'])
            ->where(function ($q) use ($data) {
                $q->where('to_academic_year_id', $data['academic_year_id'])
                  ->orWhere('from_academic_year_id', $data['academic_year_id']);
            })
            ->with('fromAcademicYear', 'toAcademicYear')
            ->get();

        $totalPaid = $payments->sum('amount_paid');
        $totalCarryForward = $carryForwards->where('to_academic_year_id', $data['academic_year_id'])->sum('amount');
        $totalNetFee = $studentFees->sum('net_amount') + $totalCarryForward;
        $dueAmount = max(0, $totalNetFee - $totalPaid);

        return response()->json([
            'success' => true,
            'student' => $student,
            'fees' => $studentFees,
            'payments' => $payments,
            'carry_forwards' => $carryForwards,
            'total_paid' => $totalPaid,
            'total_carry_forward' => $totalCarryForward,
            'net_fee' => $totalNetFee,
            'due_amount' => $dueAmount,
        ]);
    }

    public function printSummary(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester' => 'nullable|in:1,2',
        ]);

        $yearId = $data['academic_year_id'];
        $semester = $data['semester'] ?? null;
        $academicYear = AcademicYear::findOrFail($yearId);

        $feesQuery = StudentFee::where('academic_year_id', $yearId);
        if ($semester) $feesQuery->where('semester', $semester);
        $fees = $feesQuery->with('feeStructure')->get();

        $paymentQuery = FeePayment::where('academic_year_id', $yearId);
        if ($semester) $paymentQuery->where('semester', $semester);
        $paymentTotals = $paymentQuery
            ->selectRaw('student_fee_id, SUM(amount_paid) as total_paid')
            ->groupBy('student_fee_id')
            ->pluck('total_paid', 'student_fee_id');

        $typeLabels = ['tuition' => 'શાળા ફી', 'transport' => 'બસ ફી', 'other' => 'અન્ય'];
        $byType = [];
        foreach ($fees as $sf) {
            $type = $sf->feeStructure?->type ?? 'other';
            if (!isset($byType[$type])) $byType[$type] = ['assigned' => 0, 'collected' => 0, 'concession' => 0, 'due' => 0];
            $byType[$type]['assigned'] += $sf->net_amount;
            $byType[$type]['concession'] += $sf->concession_amount;
            $collected = $paymentTotals->get($sf->id, 0);
            $byType[$type]['collected'] += $collected;
            $byType[$type]['due'] += max(0, $sf->net_amount - $collected);
        }

        $standards = Standard::orderBy('sort_order')->get();
        $perStandard = [];
        foreach ($standards as $standard) {
            $studentIds = Student::where('current_standard_id', $standard->id)->pluck('id');
            $stdFees = $fees->whereIn('student_id', $studentIds);
            $stdAssigned = $stdFees->sum('net_amount');
            $stdCollected = 0;
            $stdDue = 0;
            foreach ($stdFees as $sf) {
                $c = $paymentTotals->get($sf->id, 0);
                $stdCollected += $c;
                $stdDue += max(0, $sf->net_amount - $c);
            }
            $stdConcession = $stdFees->sum('concession_amount');
            if ($stdAssigned > 0 || $stdConcession > 0) {
                $perStandard[] = ['standard' => $standard->name, 'assigned' => $stdAssigned, 'collected' => $stdCollected, 'concession' => $stdConcession, 'due' => $stdDue];
            }
        }

        $totalAssigned = $fees->sum('net_amount');
        $totalConcession = $fees->sum('concession_amount');
        $totalCollected = $paymentTotals->sum();
        $totalDue = $fees->sum(function ($sf) use ($paymentTotals) {
            return max(0, $sf->net_amount - $paymentTotals->get($sf->id, 0));
        });

        $semLabel = $semester ? "સત્ર $semester" : 'બધા સત્ર';
        $school = SchoolSetting::find(1);

        return view('fees.reports.print-summary', compact(
            'academicYear', 'semester', 'semLabel', 'totalAssigned', 'totalCollected', 'totalDue', 'totalConcession',
            'byType', 'perStandard', 'typeLabels', 'school'
        ));
    }

    public function printDueList(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_id' => 'nullable|exists:standards,id',
            'class_id' => 'nullable|exists:school_classes,id',
            'semester' => 'nullable|in:1,2',
        ]);

        $academicYear = AcademicYear::findOrFail($data['academic_year_id']);
        $semester = $data['semester'] ?? null;
        $yearId = $data['academic_year_id'];

        $query = StudentFee::with('feeStructure')
            ->where('student_fees.academic_year_id', $yearId)
            ->join('students', 'student_fees.student_id', '=', 'students.id')
            ->where('students.status', '!=', 'alumni');

        if (!empty($data['semester'])) $query->where('student_fees.semester', $data['semester']);
        if (!empty($data['standard_id'])) $query->where('students.current_standard_id', $data['standard_id']);
        if (!empty($data['class_id'])) $query->where('students.current_class_id', $data['class_id']);

        $rows = $query->select(
            'student_fees.*',
            'students.gr_number', 'students.full_name_gu', 'students.full_name_en',
            'students.mobile',
            'students.father_name_gu', 'students.father_name_en',
            'students.current_standard_id', 'students.current_class_id'
        )->get();

        // Group by student
        $grouped = [];
        $feeTypes = [];
        $semestersFound = [];

        foreach ($rows as $sf) {
            $sid = $sf->student_id;
            $sem = $sf->semester ?? 1;
            $type = $sf->feeStructure?->type ?? 'other';

            $feeTypes[$type] = true;
            $semestersFound[$sem] = true;

            if (!isset($grouped[$sid])) {
                $grouped[$sid] = [
                    'id' => $sf->student_id,
                    'gr_number' => $sf->gr_number,
                    'full_name_gu' => $sf->full_name_gu,
                    'full_name_en' => $sf->full_name_en,
                    'mobile' => $sf->mobile,
                    'father_name_gu' => $sf->father_name_gu,
                    'father_name_en' => $sf->father_name_en,
                    'current_standard_id' => $sf->current_standard_id,
                    'current_class_id' => $sf->current_class_id,
                    'entries' => [],
                    'total_due' => 0,
                ];
            }

            $paid = FeePayment::where('student_id', $sf->student_id)
                ->where('academic_year_id', $yearId)
                ->where('student_fee_id', $sf->id)
                ->sum('amount_paid');

            $due = max(0, $sf->net_amount - $paid);

            $grouped[$sid]['entries']["sem_{$sem}_{$type}"] = [
                'net_amount' => (float) $sf->net_amount,
                'paid_amount' => (float) $paid,
                'due_amount' => (float) $due,
            ];
            $grouped[$sid]['total_due'] += $due;
        }

        // Load student relations
        $studentIds = array_keys($grouped);
        $students = \App\Models\Student::whereIn('id', $studentIds)
            ->with('currentStandard', 'currentClass')
            ->get()->keyBy('id');

        $studentsData = [];
        foreach ($grouped as $sid => $g) {
            $stu = $students->get($sid);
            $g['student'] = $stu ? [
                'current_standard' => $stu->currentStandard,
                'current_class' => $stu->currentClass,
            ] : null;
            $studentsData[] = (object) $g;
        }

        usort($studentsData, fn($a, $b) => $b->total_due <=> $a->total_due);

        $typeList = array_keys($feeTypes);
        $semList = array_keys($semestersFound);
        sort($semList);
        $semLabel = $semester ? "સત્ર $semester" : 'બધા સત્ર';
        $typeLabels = ['tuition' => 'શાળા ફી', 'transport' => 'બસ ફી', 'other' => 'અન્ય'];
        $school = SchoolSetting::find(1);

        return view('fees.reports.print-due-list', compact(
            'academicYear', 'semester', 'semLabel', 'studentsData', 'typeList', 'semList', 'typeLabels', 'school'
        ));
    }

    public function printCollectionReport(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'payment_method' => 'nullable|in:cash,bank,cheque,online',
            'semester' => 'nullable|in:1,2',
        ]);

        $academicYear = AcademicYear::findOrFail($data['academic_year_id']);
        $semester = $data['semester'] ?? null;

        $query = FeePayment::with('student.currentStandard', 'student.currentClass', 'receiver')
            ->where('academic_year_id', $data['academic_year_id'])
            ->whereBetween('payment_date', [$data['from_date'], $data['to_date']]);
        if ($semester) $query->where('semester', $semester);
        if (!empty($data['payment_method'])) $query->where('payment_method', $data['payment_method']);

        $payments = $query->orderBy('payment_date')->get();
        $totalAmount = $payments->sum('amount_paid');
        $semLabel = $semester ? "સત્ર $semester" : 'બધા સત્ર';
        $school = SchoolSetting::find(1);

        return view('fees.reports.print-collection', compact('academicYear', 'semester', 'semLabel', 'payments', 'totalAmount', 'school'));
    }

    public function printStudentStatement(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester' => 'nullable|in:1,2',
        ]);

        $semester = $data['semester'] ?? null;
        $academicYear = AcademicYear::findOrFail($data['academic_year_id']);
        $student = Student::findOrFail($data['student_id']);

        $feesQuery = StudentFee::where('student_id', $data['student_id'])
            ->where('academic_year_id', $data['academic_year_id'])
            ->whereNotNull('fee_structure_id');
        if ($semester) $feesQuery->where('semester', $semester);
        $studentFees = $feesQuery->with('feeStructure.details.feeHead')->get();

        $payQuery = FeePayment::where('student_id', $data['student_id'])
            ->where('academic_year_id', $data['academic_year_id']);
        if ($semester) $payQuery->where('semester', $semester);
        $payments = $payQuery->with('studentFee.feeStructure')->orderBy('payment_date')->get();

        $carryForwards = FeeCarryForward::where('student_id', $data['student_id'])
            ->where(fn($q) => $q->where('to_academic_year_id', $data['academic_year_id'])
                ->orWhere('from_academic_year_id', $data['academic_year_id']))
            ->with('fromAcademicYear', 'toAcademicYear')->get();

        $totalPaid = $payments->sum('amount_paid');
        $totalCarryForward = $carryForwards->where('to_academic_year_id', $data['academic_year_id'])->sum('amount');
        $totalNetFee = $studentFees->sum('net_amount') + $totalCarryForward;
        $dueAmount = max(0, $totalNetFee - $totalPaid);
        $semLabel = $semester ? "સત્ર $semester" : 'બધા સત્ર';
        $school = SchoolSetting::find(1);

        return view('fees.reports.print-statement', compact(
            'academicYear', 'semester', 'semLabel', 'student', 'studentFees', 'payments',
            'carryForwards', 'totalPaid', 'totalCarryForward', 'totalNetFee', 'dueAmount', 'school'
        ));
    }
}
