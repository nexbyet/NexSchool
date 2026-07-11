<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\PublicHoliday;
use App\Models\SchoolClass;
use App\Models\Standard;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyStatsController extends Controller
{
    public function index()
    {
        return view('daily-stats.index');
    }

    protected function computeClassStats(Standard $standard, SchoolClass $class, Carbon $date, array &$grandTotals)
    {
        $dateKey = $date->format('Y-m-d');
        $prevDay = $date->copy()->subDay();

        $students = Student::where('current_standard_id', $standard->id)
            ->where('current_class_id', $class->id)
            ->where('status', 'active')
            ->where('is_registered', true)
            ->where('date_of_admission', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('leaving_date')->orWhere('leaving_date', '>=', $date);
            })
            ->get();

        $ad = fn($s) => $s->date_of_admission instanceof Carbon ? $s->date_of_admission : Carbon::parse($s->date_of_admission);
        $ld = fn($s) => $s->leaving_date ? ($s->leaving_date instanceof Carbon ? $s->leaving_date : Carbon::parse($s->leaving_date)) : null;

        $prevSet = $students->filter(fn($s) => $ad($s)->lte($prevDay) && (!$ld($s) || $ld($s)->gte($prevDay)));
        $prev = ['kumar' => $prevSet->where('sharirik_jaati', 'kumar')->count(), 'kumari' => $prevSet->where('sharirik_jaati', 'kumari')->count(), 'total' => $prevSet->count()];

        $admissionSet = $students->filter(fn($s) => $ad($s)->format('Y-m-d') === $dateKey);
        $adm = ['kumar' => $admissionSet->where('sharirik_jaati', 'kumar')->count(), 'kumari' => $admissionSet->where('sharirik_jaati', 'kumari')->count(), 'total' => $admissionSet->count()];

        $leftSet = $students->filter(fn($s) => $s->leaving_date && ($ld($s)->format('Y-m-d') === $dateKey));
        $lft = ['kumar' => $leftSet->where('sharirik_jaati', 'kumar')->count(), 'kumari' => $leftSet->where('sharirik_jaati', 'kumari')->count(), 'total' => $leftSet->count()];

        $t1 = [
            'kumar' => $prev['kumar'] + $adm['kumar'] + $lft['kumar'],
            'kumari' => $prev['kumari'] + $adm['kumari'] + $lft['kumari'],
            'total' => $prev['total'] + $adm['total'] + $lft['total'],
        ];

        $att = Attendance::whereIn('student_id', $students->pluck('id'))
            ->whereDate('date', $date)->get()->keyBy('student_id');

        $byStatus = [];
        foreach (['present', 'absent', 'absent_with_leave', 'medical_leave'] as $st) {
            $f = $students->filter(fn($s) => isset($att[$s->id]) && $att[$s->id]->status === $st);
            $byStatus[$st] = [
                'kumar' => $f->where('sharirik_jaati', 'kumar')->count(),
                'kumari' => $f->where('sharirik_jaati', 'kumari')->count(),
                'total' => $f->count(),
            ];
        }

        $p = $byStatus['present'];
        $a = $byStatus['absent'];
        $l = $byStatus['absent_with_leave'];
        $s = $byStatus['medical_leave'];

        $t2 = [
            'kumar' => $p['kumar'] + $a['kumar'] + $l['kumar'] + $s['kumar'],
            'kumari' => $p['kumari'] + $a['kumari'] + $l['kumari'] + $s['kumari'],
            'total' => $p['total'] + $a['total'] + $l['total'] + $s['total'],
        ];

        // Update grand totals
        foreach (['prev', 'adm', 'lft', 't1', 'p', 'a', 'l', 's', 't2'] as $key) {
            foreach (['kumar', 'kumari', 'total'] as $g) {
                $grandTotals[$key][$g] = ($grandTotals[$key][$g] ?? 0) + ($$key)[$g];
            }
        }

        return compact('prev', 'adm', 'lft', 't1', 'p', 'a', 'l', 's', 't2');
    }

    public function show(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = Carbon::parse($data['date']);
        $dayNamesGu = ['રવિ', 'સોમ', 'મંગળ', 'બુધ', 'ગુરુ', 'શુક્ર', 'શનિ'];
        $isSunday = $date->isSunday();
        $isHoliday = !$isSunday && PublicHoliday::whereYear('date', $date->year)->whereDate('date', $date)->exists();

        $standards = Standard::orderBy('sort_order')->get();
        $classes = SchoolClass::orderBy('name')->get()->groupBy('standard_id');

        $grandTotals = [];
        $rows = [];

        foreach ($standards as $std) {
            $stdClasses = $classes->get($std->id, collect());
            if ($stdClasses->isEmpty()) continue;
            foreach ($stdClasses as $cls) {
                $stats = $this->computeClassStats($std, $cls, $date, $grandTotals);
                $rows[] = ['standard' => $std->name, 'class' => $cls->name] + $stats;
            }
        }

        $html = view('daily-stats._table', compact('rows', 'grandTotals', 'date', 'dayNamesGu', 'isSunday', 'isHoliday'))->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'is_sunday' => $isSunday,
            'is_holiday' => $isHoliday,
            'day_name' => $dayNamesGu[$date->dayOfWeek],
        ]);
    }

    public function print(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = Carbon::parse($data['date']);
        $standards = Standard::orderBy('sort_order')->get();
        $classes = SchoolClass::orderBy('name')->get()->groupBy('standard_id');

        $grandTotals = [];
        $rows = [];

        foreach ($standards as $std) {
            $stdClasses = $classes->get($std->id, collect());
            if ($stdClasses->isEmpty()) continue;
            foreach ($stdClasses as $cls) {
                $stats = $this->computeClassStats($std, $cls, $date, $grandTotals);
                $rows[] = ['standard' => $std->name, 'class' => $cls->name] + $stats;
            }
        }

        $dayNamesGu = ['રવિ', 'સોમ', 'મંગળ', 'બુધ', 'ગુરુ', 'શુક્ર', 'શનિ'];
        $school = \App\Models\SchoolSetting::find(1);

        return view('daily-stats.print', compact('rows', 'grandTotals', 'date', 'dayNamesGu', 'school'));
    }
}
