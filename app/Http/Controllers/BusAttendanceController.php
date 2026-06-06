<?php

namespace App\Http\Controllers;

use App\Models\BusAttendance;
use App\Models\Route;
use App\Models\Student;
use App\Models\StudentRoute;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BusAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $routes = Route::where('is_active', true)->orderBy('route_name')->get();

        $students = collect();
        if ($request->route_id && $request->date) {
            $assignedIds = StudentRoute::where('route_id', $request->route_id)
                ->where('is_active', true)->pluck('student_id');

            $students = Student::whereIn('id', $assignedIds)
                ->where('status', 'active')
                ->orderBy('gr_number')
                ->get();
        }

        $attendances = collect();
        if ($request->date && $request->route_id && $students->isNotEmpty()) {
            $attendances = BusAttendance::where('route_id', $request->route_id)
                ->where('date', $request->date)
                ->get()
                ->keyBy('student_id');
        }

        return view('transport.bus-attendance.index', compact(
            'routes', 'students', 'attendances'
        ));
    }

    public function mark(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'route_id' => 'required|exists:routes,id',
            'date' => 'required|date',
            'morning_status' => 'nullable|in:present,absent,leave',
            'evening_status' => 'nullable|in:present,absent,leave',
            'notes' => 'nullable|string|max:500',
        ]);

        BusAttendance::updateOrCreate(
            ['student_id' => $data['student_id'], 'route_id' => $data['route_id'], 'date' => $data['date']],
            [
                'morning_status' => $data['morning_status'] ?? null,
                'evening_status' => $data['evening_status'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]
        );

        return response()->json(['success' => true, 'message' => 'હાજરી સેવ થઈ']);
    }

    public function print(Request $request)
    {
        $data = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2100',
            'lang' => 'nullable|in:gu,en',
            'type' => 'nullable|in:blank,filled',
        ]);

        $lang = $data['lang'] ?? 'gu';
        $type = $data['type'] ?? 'blank';
        $route = Route::with('vehicle', 'stops')->findOrFail($data['route_id']);
        $monthStart = Carbon::create($data['year'], $data['month'], 1);
        $monthEnd = $monthStart->copy()->endOfMonth();

        $assignedIds = StudentRoute::where('route_id', $route->id)
            ->where('is_active', true)->pluck('student_id');

        $students = Student::whereIn('id', $assignedIds)
            ->where('status', 'active')
            ->orderBy('gr_number')
            ->get();

        $daysInMonth = $monthStart->daysInMonth;
        $dates = [];
        $dayNamesGu = ['રવિ', 'સોમ', 'મંગળ', 'બુધ', 'ગુરુ', 'શુક્ર', 'શનિ'];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::create($data['year'], $data['month'], $d);
            $dow = $date->dayOfWeek;
            $dateKey = $date->format('Y-m-d');
            $isSunday = $dow === 0;
            $dates[] = [
                'day' => $d,
                'dateKey' => $dateKey,
                'dayName' => $lang === 'gu' ? $dayNamesGu[$dow] : $date->format('D'),
                'isSunday' => $isSunday,
            ];
        }

        $workingDates = array_filter($dates, fn($d) => !$d['isSunday']);
        $workingDates = array_values($workingDates);
        $workingDays = count($workingDates);

        $attendances = collect();
        if ($type === 'filled') {
            $attendances = BusAttendance::where('route_id', $route->id)
                ->whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
                ->get()
                ->groupBy(fn($a) => $a->student_id . '-' . $a->date->format('Y-m-d'));
        }

        $blankRows = 5;

        $monthNamesGu = [
            1 => 'જાન્યુઆરી', 2 => 'ફેબ્રુઆરી', 3 => 'માર્ચ',
            4 => 'એપ્રિલ', 5 => 'મે', 6 => 'જૂન',
            7 => 'જુલાઈ', 8 => 'ઑગસ્ટ', 9 => 'સપ્ટેમ્બર',
            10 => 'ઑક્ટોબર', 11 => 'નવેમ્બર', 12 => 'ડિસેમ્બર',
        ];
        $monthName = $lang === 'gu' ? ($monthNamesGu[$data['month']] ?? '') : $monthStart->format('F');
        $school = \App\Models\SchoolSetting::find(1);

        return view('transport.bus-attendance.print', compact(
            'route', 'students', 'workingDates', 'workingDays', 'monthName',
            'attendances', 'data', 'lang', 'monthStart', 'monthEnd', 'school',
            'type', 'blankRows'
        ));
    }
}
