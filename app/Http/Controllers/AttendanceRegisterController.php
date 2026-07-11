<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassTeacher;
use App\Models\PublicHoliday;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Standard;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceRegisterController extends Controller
{
    public function index()
    {
        $standards = Standard::orderBy('sort_order')->get();
        $activeYear = AcademicYear::getActive();
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $months = [
            1 => 'જાન્યુઆરી', 2 => 'ફેબ્રુઆરી', 3 => 'માર્ચ',
            4 => 'એપ્રિલ', 5 => 'મે', 6 => 'જૂન',
            7 => 'જુલાઈ', 8 => 'ઑગસ્ટ', 9 => 'સપ્ટેમ્બર',
            10 => 'ઑક્ટોબર', 11 => 'નવેમ્બર', 12 => 'ડિસેમ્બર',
        ];
        return view('attendance-register.index', compact('standards', 'activeYear', 'academicYears', 'months'));
    }

    public function print(Request $request)
    {
        $data = $request->validate([
            'standard_id' => 'required|exists:standards,id',
            'class_id' => 'required|exists:school_classes,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2100',
            'academic_year_id' => 'required|exists:academic_years,id',
            'lang' => 'nullable|in:gu,en',
            'type' => 'nullable|in:blank,filled',
        ]);

        $lang = $data['lang'] ?? 'gu';
        $type = $data['type'] ?? 'blank';
        $standard = Standard::findOrFail($data['standard_id']);
        $class = SchoolClass::findOrFail($data['class_id']);
        $academicYear = AcademicYear::findOrFail($data['academic_year_id']);
        $school = SchoolSetting::find(1);

        $classTeacher = ClassTeacher::where('school_class_id', $class->id)
            ->where('academic_year_id', $academicYear->id)
            ->with('teacher')
            ->first();

        $monthStart = Carbon::create($data['year'], $data['month'], 1);
        $monthEnd = $monthStart->copy()->endOfMonth();

        $students = Student::where('current_standard_id', $standard->id)
            ->where('current_class_id', $class->id)
            ->whereIn('status', ['active', 'alumni'])
            ->where('is_registered', true)
            ->where('date_of_admission', '<=', $monthEnd)
            ->where(function ($q) use ($monthStart) {
                $q->whereNull('leaving_date')->orWhere('leaving_date', '>=', $monthStart);
            })
            ->defaultSort()
            ->get();

        $holidays = PublicHoliday::where('academic_year_id', $academicYear->id)
            ->whereYear('date', $data['year'])
            ->whereMonth('date', $data['month'])
            ->get()
            ->keyBy(fn($h) => $h->date->format('Y-m-d'));

        $attendanceData = collect();
        $studentTotals = collect();
        if ($type === 'filled' && $students->isNotEmpty()) {
            $monthStartStr = $monthStart->format('Y-m-d');
            $monthEndStr = $monthEnd->format('Y-m-d');

            $sessionStart = $academicYear->session_start_date
                ? $academicYear->session_start_date->copy()
                : Carbon::create($data['year'], 1, 1);
            $sessionStartStr = $sessionStart->format('Y-m-d');
            $prevEndStr = $monthStart->copy()->subDay()->format('Y-m-d');

            // Daily attendance for month cells
            $attendanceData = Attendance::whereIn('student_id', $students->pluck('id'))
                ->whereBetween('date', [$monthStartStr, $monthEndStr])
                ->get()
                ->keyBy(fn($a) => $a->student_id . '-' . $a->date->format('Y-m-d'));

            // Per-student present counts for summary columns
            $allPresent = Attendance::whereIn('student_id', $students->pluck('id'))
                ->where('status', 'present')
                ->whereBetween('date', [$sessionStartStr, $monthEndStr])
                ->get()
                ->groupBy('student_id');

            foreach ($students as $stu) {
                $stuPresent = $allPresent->get($stu->id, collect());
                $admissionMonth = Carbon::parse($stu->date_of_admission)->month;
                $admissionYear = Carbon::parse($stu->date_of_admission)->year;
                $isFirstMonth = ($admissionMonth == $data['month'] && $admissionYear == $data['year']);
                if ($isFirstMonth && $stu->previous_attendance_days !== null) {
                    $prevTotal = $stu->previous_attendance_days;
                } else {
                    $prevTotal = $stuPresent->filter(fn($a) => $a->date->format('Y-m-d') <= $prevEndStr)->count();
                }
                $currentTotal = $stuPresent->filter(fn($a) => $a->date->format('Y-m-d') >= $monthStartStr)->count();
                $studentTotals[$stu->id] = [
                    'prev' => $prevTotal,
                    'current' => $currentTotal,
                    'total' => $prevTotal + $currentTotal,
                ];
            }
        }

        $daysInMonth = $monthStart->daysInMonth;
        $dates = [];
        $dayNamesGu = ['રવિ', 'સોમ', 'મંગળ', 'બુધ', 'ગુરુ', 'શુક્ર', 'શનિ'];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::create($data['year'], $data['month'], $d);
            $dow = $date->dayOfWeek;
            $dateKey = $date->format('Y-m-d');
            $dates[] = [
                'day' => $d,
                'dateKey' => $dateKey,
                'dayName' => $lang === 'gu' ? $dayNamesGu[$dow] : $date->format('D'),
                'isSunday' => $dow === 0,
                'isHoliday' => isset($holidays[$dateKey]),
                'holidayName' => $holidays[$dateKey]->name ?? null,
                'holidayType' => $holidays[$dateKey]->type ?? null,
            ];
        }

        // ---- Daily summary rows (9 rows) ----
        $dailySummary = [];
        if ($type === 'filled' && $students->isNotEmpty()) {
            // Attendance grouped by date for quick lookup
            $attByDate = Attendance::whereIn('student_id', $students->pluck('id'))
                ->whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
                ->get()
                ->groupBy(fn($a) => $a->date->format('Y-m-d'));

            $datesWithSummary = $dates;
            $runningPrev = null;

            foreach ($dates as $di) {
                $dk = $di['dateKey'];
                $cd = Carbon::parse($dk);
                $attDate = $attByDate->get($dk, collect());

                // Prev day count: enrolled students as of previous day
                if ($runningPrev === null) {
                    // First day: count students enrolled as of day before month start
                    $prevDay = $cd->copy()->subDay();
                    $runningPrev = $students->filter(function ($s) use ($prevDay) {
                        $ad = $s->date_of_admission instanceof Carbon ? $s->date_of_admission->format('Y-m-d') : $s->date_of_admission;
                        $ld = $s->leaving_date ? ($s->leaving_date instanceof Carbon ? $s->leaving_date->format('Y-m-d') : $s->leaving_date) : null;
                        return $ad <= $prevDay->format('Y-m-d') && (!$ld || $ld >= $prevDay->format('Y-m-d'));
                    })->count();
                }
                $r1 = $runningPrev;

                // Admissions on this date
                $r2 = $students->filter(function ($s) use ($dk) {
                    $ad = $s->date_of_admission instanceof Carbon ? $s->date_of_admission->format('Y-m-d') : $s->date_of_admission;
                    return $ad === $dk;
                })->count();

                // Left on this date
                $r3 = $students->filter(function ($s) use ($dk) {
                    if (!$s->leaving_date) return false;
                    $ld = $s->leaving_date instanceof Carbon ? $s->leaving_date->format('Y-m-d') : $s->leaving_date;
                    return $ld === $dk;
                })->count();

                // Total (1+2+3)
                $r4 = $r1 + $r2 + $r3;

                // Present (P)
                $r5 = $attDate->where('status', 'present')->count();

                // Absent (A)
                $r6 = $attDate->where('status', 'absent')->count();

                // Absent with leave (L)
                $r7 = $attDate->where('status', 'absent_with_leave')->count();

                // Medical leave (S)
                $r8 = $attDate->where('status', 'medical_leave')->count();

                // Total (5+6+7+8)
                $r9 = $r5 + $r6 + $r7 + $r8;

                $dailySummary[$dk] = [$r1, $r2, $r3, $r4, $r5, $r6, $r7, $r8, $r9];

                // Update runningPrev for next day: today's total enrollment (r4)
                $runningPrev = $r4;
            }
        }

        $monthNamesGu = [
            1 => 'જાન્યુઆરી', 2 => 'ફેબ્રુઆરી', 3 => 'માર્ચ',
            4 => 'એપ્રિલ', 5 => 'મે', 6 => 'જૂન',
            7 => 'જુલાઈ', 8 => 'ઑગસ્ટ', 9 => 'સપ્ટેમ્બર',
            10 => 'ઑક્ટોબર', 11 => 'નવેમ્બર', 12 => 'ડિસેમ્બર',
        ];
        $monthName = $lang === 'gu' ? ($monthNamesGu[$data['month']] ?? '') : $monthStart->format('F');

        $studentCount = $students->count();
        $blankRows = 5;

        return view('attendance-register.print', compact(
            'standard', 'class', 'classTeacher', 'academicYear', 'school',
            'students', 'dates', 'daysInMonth', 'monthName',
            'data', 'lang', 'type', 'studentCount', 'blankRows',
            'attendanceData', 'studentTotals', 'dailySummary'
        ));
    }

    public function printSummary(Request $request)
    {
        $data = $request->validate([
            'standard_id' => 'required|exists:standards,id',
            'class_id' => 'required|exists:school_classes,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2100',
            'academic_year_id' => 'required|exists:academic_years,id',
            'lang' => 'nullable|in:gu,en',
            'type' => 'nullable|in:filled,blank',
            'page' => 'nullable|in:front,back',
        ]);

        $lang = $data['lang'] ?? 'gu';
        $type = $data['type'] ?? 'blank';
        $page = $data['page'] ?? 'front';
        $standard = Standard::findOrFail($data['standard_id']);
        $class = SchoolClass::findOrFail($data['class_id']);
        $academicYear = AcademicYear::findOrFail($data['academic_year_id']);
        $school = SchoolSetting::find(1);

        $classTeacher = ClassTeacher::where('school_class_id', $class->id)
            ->where('academic_year_id', $academicYear->id)
            ->with('teacher')
            ->first();

        $monthStart = Carbon::create($data['year'], $data['month'], 1);
        $monthEnd = $monthStart->copy()->endOfMonth();

        $students = Student::where('current_standard_id', $standard->id)
            ->where('current_class_id', $class->id)
            ->whereIn('status', ['active', 'alumni'])
            ->where('is_registered', true)
            ->where('date_of_admission', '<=', $monthEnd)
            ->where(function ($q) use ($monthStart) {
                $q->whereNull('leaving_date')->orWhere('leaving_date', '>=', $monthStart);
            })
            ->defaultSort()
            ->get();

        $studentsKumar = $students->where('sharirik_jaati', 'kumar');
        $studentsKumari = $students->where('sharirik_jaati', 'kumari');

        $holidays = PublicHoliday::where('academic_year_id', $academicYear->id)
            ->whereYear('date', $data['year'])
            ->whereMonth('date', $data['month'])
            ->get()
            ->keyBy(fn($h) => $h->date->format('Y-m-d'));

        $daysInMonth = $monthStart->daysInMonth;
        $dates = [];
        $dayNamesGu = ['રવિ', 'સોમ', 'મંગળ', 'બુધ', 'ગુરુ', 'શુક્ર', 'શનિ'];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::create($data['year'], $data['month'], $d);
            $dow = $date->dayOfWeek;
            $dateKey = $date->format('Y-m-d');
            $dates[] = [
                'day' => $d,
                'dateKey' => $dateKey,
                'dayName' => $lang === 'gu' ? $dayNamesGu[$dow] : $date->format('D'),
                'isSunday' => $dow === 0,
                'isHoliday' => isset($holidays[$dateKey]),
                'holidayName' => $holidays[$dateKey]->name ?? null,
            ];
        }

        // Per-date gender-split summary
        $dailySummary = [];
        $monthTotals = ['kumar' => array_fill(0, 9, 0), 'kumari' => array_fill(0, 9, 0), 'total' => array_fill(0, 9, 0)];

        if ($type === 'filled' && $students->isNotEmpty()) {
            $attByDate = Attendance::whereIn('student_id', $students->pluck('id'))
                ->whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
                ->get()
                ->groupBy(fn($a) => $a->date->format('Y-m-d') . '-' . $a->student_id);

            $attByDateGrouped = Attendance::whereIn('student_id', $students->pluck('id'))
                ->whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
                ->get()
                ->groupBy(fn($a) => $a->date->format('Y-m-d'));

            $runningPrev = null;
            $runningPrevK = null;
            $runningPrevKu = null;

            foreach ($dates as $di) {
                $dk = $di['dateKey'];
                $cd = Carbon::parse($dk);
                $attDateColl = $attByDateGrouped->get($dk, collect());

                // Helper: filter students by gender and enrollment status on a given date
                $enrolledOn = function($dateStr, $gender = null) use ($students) {
                    $s = $gender ? $students->where('sharirik_jaati', $gender) : $students;
                    return $s->filter(function ($stu) use ($dateStr) {
                        $ad = $stu->date_of_admission instanceof Carbon ? $stu->date_of_admission->format('Y-m-d') : $stu->date_of_admission;
                        $ld = $stu->leaving_date ? ($stu->leaving_date instanceof Carbon ? $stu->leaving_date->format('Y-m-d') : $stu->leaving_date) : null;
                        return $ad <= $dateStr && (!$ld || $ld >= $dateStr);
                    });
                };

                $prevDayStr = $cd->copy()->subDay()->format('Y-m-d');

                if ($runningPrev === null) {
                    $runningPrev = $enrolledOn($prevDayStr)->count();
                    $runningPrevK = $enrolledOn($prevDayStr, 'kumar')->count();
                    $runningPrevKu = $enrolledOn($prevDayStr, 'kumari')->count();
                }

                // Row 1: previous day count
                $r1 = ['kumar' => $runningPrevK, 'kumari' => $runningPrevKu, 'total' => $runningPrev];

                // Row 2: admissions on this date
                $r2k = $studentsKumar->filter(fn($s) => ($s->date_of_admission instanceof Carbon ? $s->date_of_admission->format('Y-m-d') : $s->date_of_admission) === $dk)->count();
                $r2ku = $studentsKumari->filter(fn($s) => ($s->date_of_admission instanceof Carbon ? $s->date_of_admission->format('Y-m-d') : $s->date_of_admission) === $dk)->count();
                $r2 = ['kumar' => $r2k, 'kumari' => $r2ku, 'total' => $r2k + $r2ku];

                // Row 3: left on this date
                $r3k = $studentsKumar->filter(function($s) use ($dk) {
                    if (!$s->leaving_date) return false;
                    $ld = $s->leaving_date instanceof Carbon ? $s->leaving_date->format('Y-m-d') : $s->leaving_date;
                    return $ld === $dk;
                })->count();
                $r3ku = $studentsKumari->filter(function($s) use ($dk) {
                    if (!$s->leaving_date) return false;
                    $ld = $s->leaving_date instanceof Carbon ? $s->leaving_date->format('Y-m-d') : $s->leaving_date;
                    return $ld === $dk;
                })->count();
                $r3 = ['kumar' => $r3k, 'kumari' => $r3ku, 'total' => $r3k + $r3ku];

                // Row 4: total (1+2+3)
                $r4 = [
                    'kumar' => $r1['kumar'] + $r2['kumar'] + $r3['kumar'],
                    'kumari' => $r1['kumari'] + $r2['kumari'] + $r3['kumari'],
                    'total' => $r1['total'] + $r2['total'] + $r3['total'],
                ];

                // Attendance counts split by gender
                $attKumar = $attDateColl->whereIn('student_id', $studentsKumar->pluck('id'));
                $attKumari = $attDateColl->whereIn('student_id', $studentsKumari->pluck('id'));

                // Row 5: present
                $r5 = [
                    'kumar' => $attKumar->where('status', 'present')->count(),
                    'kumari' => $attKumari->where('status', 'present')->count(),
                    'total' => $attDateColl->where('status', 'present')->count(),
                ];

                // Row 6: absent
                $r6 = [
                    'kumar' => $attKumar->where('status', 'absent')->count(),
                    'kumari' => $attKumari->where('status', 'absent')->count(),
                    'total' => $attDateColl->where('status', 'absent')->count(),
                ];

                // Row 7: absent with leave
                $r7 = [
                    'kumar' => $attKumar->where('status', 'absent_with_leave')->count(),
                    'kumari' => $attKumari->where('status', 'absent_with_leave')->count(),
                    'total' => $attDateColl->where('status', 'absent_with_leave')->count(),
                ];

                // Row 8: medical leave
                $r8 = [
                    'kumar' => $attKumar->where('status', 'medical_leave')->count(),
                    'kumari' => $attKumari->where('status', 'medical_leave')->count(),
                    'total' => $attDateColl->where('status', 'medical_leave')->count(),
                ];

                // Row 9: total (5+6+7+8)
                $r9 = [
                    'kumar' => $r5['kumar'] + $r6['kumar'] + $r7['kumar'] + $r8['kumar'],
                    'kumari' => $r5['kumari'] + $r6['kumari'] + $r7['kumari'] + $r8['kumari'],
                    'total' => $r5['total'] + $r6['total'] + $r7['total'] + $r8['total'],
                ];

                $dailySummary[$dk] = [$r1, $r2, $r3, $r4, $r5, $r6, $r7, $r8, $r9];

                // Accumulate month totals
                foreach (['kumar', 'kumari', 'total'] as $g) {
                    foreach ([$r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9] as $i => $row) {
                        $monthTotals[$g][$i] += $row[$g];
                    }
                }

                // Update running prev
                $runningPrev = $r4['total'];
                $runningPrevK = $r4['kumar'];
                $runningPrevKu = $r4['kumari'];
            }
        }

        // Also compute cumulative present totals per student for percentages
        $cumulativePresent = [];
        $cumulativeKU = []; $cumulativeKUmari = [];
        if ($type === 'filled' && $students->isNotEmpty()) {
            $sessionStart = $academicYear->session_start_date
                ? $academicYear->session_start_date->copy()
                : Carbon::create($data['year'], 1, 1);

            $allPresents = Attendance::whereIn('student_id', $students->pluck('id'))
                ->where('status', 'present')
                ->whereBetween('date', [$sessionStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
                ->get()
                ->groupBy('student_id');

            foreach ($studentsKumar as $s) {
                $cnt = $allPresents->get($s->id, collect())->count();
                $cumulativeKU[] = $cnt;
            }
            foreach ($studentsKumari as $s) {
                $cnt = $allPresents->get($s->id, collect())->count();
                $cumulativeKUmari[] = $cnt;
            }
        }

        $monthNamesGu = [
            1 => 'જાન્યુઆરી', 2 => 'ફેબ્રુઆરી', 3 => 'માર્ચ',
            4 => 'એપ્રિલ', 5 => 'મે', 6 => 'જૂન',
            7 => 'જુલાઈ', 8 => 'ઑગસ્ટ', 9 => 'સપ્ટેમ્બર',
            10 => 'ઑક્ટોબર', 11 => 'નવેમ્બર', 12 => 'ડિસેમ્બર',
        ];
        $monthName = $lang === 'gu' ? ($monthNamesGu[$data['month']] ?? '') : $monthStart->format('F');

        // ---- New summary data for the redesigned front page ----

        // Box 1: Enrollment summary (5 rows)
        // Row 1: માસના પ્રથમ દિવસે — enrolled as of day before month start
        $firstDayOfMonth = $monthStart->copy();
        $dayBeforeMonthStart = $monthStart->copy()->subDay()->format('Y-m-d');

        $enrolledBeforeStart = Student::where('current_standard_id', $standard->id)
            ->where('current_class_id', $class->id)
            ->whereIn('status', ['active', 'alumni'])
            ->where('is_registered', true)
            ->where('date_of_admission', '<=', $dayBeforeMonthStart)
            ->where(function ($q) use ($monthStart) {
                $q->whereNull('leaving_date')->orWhere('leaving_date', '>=', $monthStart->format('Y-m-d'));
            })
            ->get();

        $firstDayK = $enrolledBeforeStart->where('sharirik_jaati', 'kumar')->count();
        $firstDayKu = $enrolledBeforeStart->where('sharirik_jaati', 'kumari')->count();
        $firstDayTotal = $firstDayK + $firstDayKu;

        // Row 2: નવા દાખલ થયા — admitted during this month
        $monthAdmitted = Student::where('current_standard_id', $standard->id)
            ->where('current_class_id', $class->id)
            ->whereIn('status', ['active', 'alumni'])
            ->where('is_registered', true)
            ->whereBetween('date_of_admission', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
            ->get();

        $admittedK = $monthAdmitted->where('sharirik_jaati', 'kumar')->count();
        $admittedKu = $monthAdmitted->where('sharirik_jaati', 'kumari')->count();
        $admittedTotal = $admittedK + $admittedKu;

        // Row 3: કુલ સંખ્યા (Row 1 + Row 2)
        $sumTotalK = $firstDayK + $admittedK;
        $sumTotalKu = $firstDayKu + $admittedKu;
        $sumTotalAll = $firstDayTotal + $admittedTotal;

        // Row 4: ઉઠી જનારની સંખ્યા — left during this month
        $monthLeft = Student::where('current_standard_id', $standard->id)
            ->where('current_class_id', $class->id)
            ->whereIn('status', ['active', 'alumni'])
            ->where('is_registered', true)
            ->whereNotNull('leaving_date')
            ->whereBetween('leaving_date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
            ->get();

        $leftK = $monthLeft->where('sharirik_jaati', 'kumar')->count();
        $leftKu = $monthLeft->where('sharirik_jaati', 'kumari')->count();
        $leftTotal = $leftK + $leftKu;

        // Row 5: માસ અંતે કુલ સંખ્યા (Row 3 - Row 4)
        $monthEndK = $sumTotalK - $leftK;
        $monthEndKu = $sumTotalKu - $leftKu;
        $monthEndAll = $sumTotalAll - $leftTotal;

        $box1 = [
            'first_day' => ['kumar' => $firstDayK, 'kumari' => $firstDayKu, 'total' => $firstDayTotal],
            'admitted' => ['kumar' => $admittedK, 'kumari' => $admittedKu, 'total' => $admittedTotal],
            'sum_total' => ['kumar' => $sumTotalK, 'kumari' => $sumTotalKu, 'total' => $sumTotalAll],
            'left' => ['kumar' => $leftK, 'kumari' => $leftKu, 'total' => $leftTotal],
            'month_end' => ['kumar' => $monthEndK, 'kumari' => $monthEndKu, 'total' => $monthEndAll],
        ];

        // Box 2: Category-wise breakdown (કેટેગરી / કુમાર / કુમારી / કુલ)
        // All possible categories — show every category even if count is 0
        $allCategories = [
            ['gu' => 'સામાન્ય', 'en' => 'General'],
            ['gu' => 'અનુસુચિત જાતિ', 'en' => 'SC'],
            ['gu' => 'અનુસુચિત જન જાતિ', 'en' => 'ST'],
            ['gu' => 'બક્ષીપંચ', 'en' => 'OBC'],
            ['gu' => 'આર્થિક પછાત', 'en' => 'EWS'],
        ];

        $box2 = [];
        foreach ($allCategories as $cat) {
            $label = $lang === 'gu' ? $cat['gu'] : $cat['en'];
            $guLabel = $cat['gu'];
            $enLabel = $cat['en'];
            $studentsInCat = $students->filter(function ($s) use ($guLabel, $enLabel) {
                return $s->category_gu === $guLabel || $s->category_en === $enLabel;
            });
            $ck = $studentsInCat->where('sharirik_jaati', 'kumar')->count();
            $cku = $studentsInCat->where('sharirik_jaati', 'kumari')->count();
            $box2[] = [
                'category' => $label,
                'kumar' => $ck,
                'kumari' => $cku,
                'total' => $ck + $cku,
            ];
        }

        // Grand total for Box 2
        $box2Grand = [
            'kumar' => collect($box2)->sum('kumar'),
            'kumari' => collect($box2)->sum('kumari'),
            'total' => collect($box2)->sum('total'),
        ];

        // Minority count among enrolled students this month
        $minority = $students->where('is_minority', 1);
        $minorityData = [
            'kumar' => $minority->where('sharirik_jaati', 'kumar')->count(),
            'kumari' => $minority->where('sharirik_jaati', 'kumari')->count(),
            'total' => $minority->count(),
        ];

        // Admitted students list (this month)
        $admittedList = $monthAdmitted->sortBy('gr_number')->map(function ($s) {
            return [
                'gr' => $s->gr_number,
                'name' => $s->full_name_gu ?: $s->full_name_en,
                'date' => $s->date_of_admission instanceof Carbon
                    ? $s->date_of_admission->format('d/m/Y')
                    : \Carbon\Carbon::parse($s->date_of_admission)->format('d/m/Y'),
            ];
        });

        // Left students list (this month)
        $leftList = $monthLeft->sortBy('gr_number')->map(function ($s) {
            return [
                'gr' => $s->gr_number,
                'name' => $s->full_name_gu ?: $s->full_name_en,
                'date' => $s->leaving_date instanceof Carbon
                    ? $s->leaving_date->format('d/m/Y')
                    : \Carbon\Carbon::parse($s->leaving_date)->format('d/m/Y'),
            ];
        });

        $view = $page === 'back' ? 'attendance-register.summary-back' : 'attendance-register.summary';

        return view($view, compact(
            'standard', 'class', 'classTeacher', 'academicYear', 'school',
            'students', 'studentsKumar', 'studentsKumari',
            'dates', 'daysInMonth', 'monthName', 'monthTotals',
            'data', 'lang', 'type', 'dailySummary',
            'cumulativeKU', 'cumulativeKUmari', 'monthStart', 'monthEnd',
            'box1', 'box2', 'box2Grand', 'minorityData',
            'admittedList', 'leftList', 'firstDayOfMonth',
            'monthAdmitted', 'monthLeft'
        ));
    }

    public function getClasses($standardId)
    {
        $classes = SchoolClass::where('standard_id', $standardId)
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get(['id', 'name']);
        return response()->json($classes);
    }
}
