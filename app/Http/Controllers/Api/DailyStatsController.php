<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\PublicHoliday;
use App\Models\SchoolClass;
use App\Models\Standard;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyStatsController extends Controller
{
    protected function computeClassStats(Standard $standard, SchoolClass $class, Carbon $date, array &$grandTotals)
    {
        $dateKey = $date->format('Y-m-d');
        $prevDay = $date->copy()->subDay();

        $students = Student::where('current_standard_id', $standard->id)
            ->where('current_class_id', $class->id)
            ->where('status', 'active')
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
                $rows[] = [
                    'standard_id' => $std->id,
                    'standard_name' => $std->name,
                    'class_id' => $cls->id,
                    'class_name' => $cls->name,
                ] + $stats;
            }
        }

        $metrics = ['prev', 'adm', 'lft', 't1', 'p', 'a', 'l', 's', 't2'];
        $metricLabels = [
            'prev' => 'આગલા દિવસની સંખ્યા',
            'adm' => 'દાખલ સંખ્યા',
            'lft' => 'છોડીને ગયા સંખ્યા',
            't1' => 'કુલ (૧+૨+૩)',
            'p' => 'હાજર સંખ્યા',
            'a' => 'રજા વગર ગેરહાજર',
            'l' => 'રજા સાથે ગેરહાજર',
            's' => 'માંદગી રજા',
            't2' => 'કુલ (૫+૬+૭+૮)',
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date->format('Y-m-d'),
                'day_name' => $dayNamesGu[$date->dayOfWeek],
                'is_sunday' => $isSunday,
                'is_holiday' => $isHoliday,
                'metrics' => $metrics,
                'metric_labels' => $metricLabels,
                'rows' => $rows,
                'grand_totals' => $grandTotals,
                'total_standards' => $standards->count(),
                'total_classes' => count($rows),
            ],
        ]);
    }
}
