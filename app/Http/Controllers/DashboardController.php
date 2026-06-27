<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $activeYear = AcademicYear::getActive();

        $stats = [
            'students' => Student::where('status', 'active')->count(),
            'teachers' => Teacher::count(),
            'classes' => SchoolClass::count(),
            'subjects' => Subject::count(),
        ];

        // Standard × Class × Category matrix (active students only)
        $classStats = DB::table('students')
            ->join('standards', 'students.current_standard_id', '=', 'standards.id')
            ->join('school_classes', 'students.current_class_id', '=', 'school_classes.id')
            ->selectRaw("
                standards.id as standard_id,
                standards.name as standard_name,
                standards.sort_order,
                school_classes.id as class_id,
                school_classes.name as class_name,
                COUNT(*) as total_students,
                SUM(CASE WHEN students.sharirik_jaati = 'kumar' THEN 1 ELSE 0 END) as total_boys,
                SUM(CASE WHEN students.sharirik_jaati = 'kumari' THEN 1 ELSE 0 END) as total_girls,
                SUM(CASE WHEN students.category_en = 'General' AND students.sharirik_jaati = 'kumar' THEN 1 ELSE 0 END) as general_boys,
                SUM(CASE WHEN students.category_en = 'General' AND students.sharirik_jaati = 'kumari' THEN 1 ELSE 0 END) as general_girls,
                SUM(CASE WHEN students.category_en = 'General' THEN 1 ELSE 0 END) as general_total,
                SUM(CASE WHEN students.category_en = 'SC' AND students.sharirik_jaati = 'kumar' THEN 1 ELSE 0 END) as sc_boys,
                SUM(CASE WHEN students.category_en = 'SC' AND students.sharirik_jaati = 'kumari' THEN 1 ELSE 0 END) as sc_girls,
                SUM(CASE WHEN students.category_en = 'SC' THEN 1 ELSE 0 END) as sc_total,
                SUM(CASE WHEN students.category_en = 'ST' AND students.sharirik_jaati = 'kumar' THEN 1 ELSE 0 END) as st_boys,
                SUM(CASE WHEN students.category_en = 'ST' AND students.sharirik_jaati = 'kumari' THEN 1 ELSE 0 END) as st_girls,
                SUM(CASE WHEN students.category_en = 'ST' THEN 1 ELSE 0 END) as st_total,
                SUM(CASE WHEN students.category_en = 'OBC' AND students.sharirik_jaati = 'kumar' THEN 1 ELSE 0 END) as obc_boys,
                SUM(CASE WHEN students.category_en = 'OBC' AND students.sharirik_jaati = 'kumari' THEN 1 ELSE 0 END) as obc_girls,
                SUM(CASE WHEN students.category_en = 'OBC' THEN 1 ELSE 0 END) as obc_total,
                SUM(CASE WHEN students.category_en = 'OBC' AND students.is_minority = 1 AND students.sharirik_jaati = 'kumar' THEN 1 ELSE 0 END) as obc_min_boys,
                SUM(CASE WHEN students.category_en = 'OBC' AND students.is_minority = 1 AND students.sharirik_jaati = 'kumari' THEN 1 ELSE 0 END) as obc_min_girls,
                SUM(CASE WHEN students.category_en = 'OBC' AND students.is_minority = 1 THEN 1 ELSE 0 END) as obc_min_total
            ")
            ->where('students.status', 'active')
            ->whereNotNull('students.current_class_id')
            ->groupBy('standards.id', 'standards.name', 'standards.sort_order', 'school_classes.id', 'school_classes.name')
            ->orderBy('standards.sort_order')
            ->orderBy('school_classes.name')
            ->get();

        // Summary totals across all standards
        $summaryTotals = (object) [
            'total_boys'      => $classStats->sum('total_boys'),
            'total_girls'     => $classStats->sum('total_girls'),
            'total_students'  => $classStats->sum('total_students'),
            'general_boys'    => $classStats->sum('general_boys'),
            'general_girls'   => $classStats->sum('general_girls'),
            'general_total'   => $classStats->sum('general_total'),
            'sc_boys'         => $classStats->sum('sc_boys'),
            'sc_girls'        => $classStats->sum('sc_girls'),
            'sc_total'        => $classStats->sum('sc_total'),
            'st_boys'         => $classStats->sum('st_boys'),
            'st_girls'        => $classStats->sum('st_girls'),
            'st_total'        => $classStats->sum('st_total'),
            'obc_boys'        => $classStats->sum('obc_boys'),
            'obc_girls'       => $classStats->sum('obc_girls'),
            'obc_total'       => $classStats->sum('obc_total'),
            'obc_min_boys'    => $classStats->sum('obc_min_boys'),
            'obc_min_girls'   => $classStats->sum('obc_min_girls'),
            'obc_min_total'   => $classStats->sum('obc_min_total'),
        ];

        // Upcoming 10 days birthdays (active students only)
        $start = now();
        $end = now()->addDays(10);
        $allActive = Student::where('status', 'active')
            ->with(['currentStandard', 'currentClass'])
            ->get(['id', 'gr_number', 'student_name_gu', 'student_name_en', 'full_name_gu', 'full_name_en', 'date_of_birth', 'sharirik_jaati', 'photo', 'current_standard_id', 'current_class_id']);
        $upcomingBirthdays = $allActive->filter(function ($s) use ($start, $end) {
            $dob = \Carbon\Carbon::parse($s->date_of_birth);
            $next = $dob->copy()->year($start->year);
            if ($next->lt($start)) $next->addYear();
            return $next->between($start, $end);
        })->sortBy(function ($s) {
            $dob = \Carbon\Carbon::parse($s->date_of_birth);
            $next = $dob->copy()->year(now()->year);
            if ($next->lt(now())) $next->addYear();
            return $next->format('md');
        })->values();
        $birthdayBoys = $upcomingBirthdays->where('sharirik_jaati', 'kumar');
        $birthdayGirls = $upcomingBirthdays->where('sharirik_jaati', 'kumari');

        // Upcoming activities and holidays (next 10 days)
        $today = now()->format('Y-m-d');
        $tenDaysLater = now()->addDays(10)->format('Y-m-d');
        $upcomingPlans = \App\Models\ActivityPlan::where('academic_year_id', $activeYear?->id)
            ->whereBetween('date', [$today, $tenDaysLater])
            ->orderBy('date')
            ->orderBy('sort_order')
            ->get();
        $upcomingHolidays = \App\Models\PublicHoliday::where('academic_year_id', $activeYear?->id)
            ->whereBetween('date', [$today, $tenDaysLater])
            ->orderBy('date')
            ->get();

        return view('dashboard.index', compact('stats', 'classStats', 'summaryTotals', 'activeYear', 'upcomingBirthdays', 'birthdayBoys', 'birthdayGirls', 'upcomingPlans', 'upcomingHolidays'));
    }
}
