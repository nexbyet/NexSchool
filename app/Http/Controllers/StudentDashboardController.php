<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentFee;
use App\Models\FeePayment;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            abort(404);
        }

        $activeYear = AcademicYear::getActive();

        // Student's fee data (view only)
        $feeAssignments = collect();
        $feePayments = collect();
        $totalDue = 0;
        $totalPaid = 0;

        if ($activeYear && $student) {
            $feeAssignments = StudentFee::where('student_id', $student->id)
                ->where('academic_year_id', $activeYear->id)
                ->with('feeStructure.details.feeHead')
                ->get();

            $feePayments = FeePayment::where('student_id', $student->id)
                ->where('academic_year_id', $activeYear->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $totalDue = $feeAssignments->sum(function ($a) {
                return $a->feeStructure?->total_amount ?? 0;
            });

            $totalPaid = $feePayments->sum('amount_paid');
        }

        // Birthday check
        $isBirthday = $student->date_of_birth
            && now()->format('m-d') === \Carbon\Carbon::parse($student->date_of_birth)->format('m-d');

        return view('student.dashboard', compact(
            'user', 'student', 'activeYear',
            'feeAssignments', 'feePayments',
            'totalDue', 'totalPaid', 'isBirthday'
        ));
    }
}
