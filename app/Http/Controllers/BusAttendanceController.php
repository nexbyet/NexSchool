<?php

namespace App\Http\Controllers;

use App\Models\BusAttendance;
use App\Models\BusOnlyStudent;
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

            $schoolStudents = Student::whereIn('id', $assignedIds)
                ->where('status', 'active')
                ->orderBy('gr_number')
                ->get()
                ->map(fn($s) => [
                    'id' => $s->id,
                    'display_id' => $s->id,
                    'gr_number' => $s->gr_number,
                    'name' => $s->full_name_gu ?: $s->full_name_en,
                    'mobile' => $s->mobile ?? ($s->whatsapp ?? ''),
                    'type' => $s->is_registered ? 'regular' : 'unregistered',
                    'type_label' => $s->is_registered ? '' : 'અનબોર્ડ',
                    'model' => 'student',
                ]);

            $busOnlyStudents = BusOnlyStudent::where('route_id', $request->route_id)
                ->where('status', 'active')
                ->orderBy('full_name_gu')
                ->get()
                ->map(fn($s) => [
                    'id' => $s->id,
                    'display_id' => 'bus_' . $s->id,
                    'gr_number' => 'BUS',
                    'name' => $s->full_name_gu,
                    'mobile' => $s->mobile ?? '',
                    'type' => 'bus_only',
                    'type_label' => 'બસ',
                    'model' => 'bus_only',
                ]);

            $students = $schoolStudents->concat($busOnlyStudents)->values();
        }

        $attendances = collect();
        if ($request->date && $request->route_id && $students->isNotEmpty()) {
            $regIds = $students->where('model', 'student')->pluck('id');
            $busIds = $students->where('model', 'bus_only')->pluck('id');

            $regAtt = BusAttendance::where('route_id', $request->route_id)
                ->where('date', $request->date)
                ->whereNull('bus_only_student_id')
                ->whereIn('student_id', $regIds)
                ->get()
                ->keyBy(fn($a) => 'student_' . $a->student_id);

            $busAtt = BusAttendance::where('route_id', $request->route_id)
                ->where('date', $request->date)
                ->whereNotNull('bus_only_student_id')
                ->whereIn('bus_only_student_id', $busIds)
                ->get()
                ->keyBy(fn($a) => 'bus_' . $a->bus_only_student_id);

            $attendances = $regAtt->union($busAtt);
        }

        $routeName = $request->route_id ? Route::find($request->route_id)?->route_name : '';

        return view('transport.bus-attendance.index', compact(
            'routes', 'students', 'attendances', 'routeName'
        ));
    }

    public function mark(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'bus_only_student_id' => 'nullable|exists:bus_only_students,id',
            'student_type' => 'required|in:student,bus_only',
            'route_id' => 'required|exists:routes,id',
            'date' => 'required|date',
            'morning_status' => 'nullable|in:present,absent,leave',
            'evening_status' => 'nullable|in:present,absent,leave',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($data['student_type'] === 'bus_only') {
            $data['bus_only_student_id'] = $data['bus_only_student_id'];
            $data['student_id'] = null;
        } else {
            $data['student_id'] = $data['student_id'];
            $data['bus_only_student_id'] = null;
        }

        BusAttendance::updateOrCreate(
            [
                'student_id' => $data['student_id'],
                'bus_only_student_id' => $data['bus_only_student_id'],
                'route_id' => $data['route_id'],
                'date' => $data['date'],
            ],
            [
                'student_type' => $data['student_type'],
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

        $schoolStudents = Student::whereIn('id', $assignedIds)
            ->where('status', 'active')
            ->orderBy('gr_number')
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'display_id' => 'student_' . $s->id,
                'gr_number' => $s->gr_number,
                'name' => $s->full_name_gu ?: $s->full_name_en,
                'mobile' => $s->mobile ?? ($s->whatsapp ?? ''),
                'type' => $s->is_registered ? 'regular' : 'unregistered',
                'type_label' => $s->is_registered ? 'શાળા' : 'અનબોર્ડ',
                'model' => 'student',
                'model_id' => $s->id,
            ]);

        $busOnlyStudents = BusOnlyStudent::where('route_id', $route->id)
            ->where('status', 'active')
            ->orderBy('full_name_gu')
            ->get()
            ->map(fn($s) => [
                'id' => 'bus_' . $s->id,
                'display_id' => 'bus_' . $s->id,
                'gr_number' => 'BUS',
                'name' => $s->full_name_gu,
                'mobile' => $s->mobile ?? '',
                'type' => 'bus_only',
                'type_label' => 'બસ',
                'model' => 'bus_only',
                'model_id' => $s->id,
            ]);

        $students = $schoolStudents->concat($busOnlyStudents)->values();

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
            ];
        }

        $workingDates = array_filter($dates, fn($d) => !$d['isSunday']);
        $workingDates = array_values($workingDates);
        $workingDays = count($workingDates);

        $attendances = collect();
        if ($type === 'filled') {
            $allRegAtt = BusAttendance::where('route_id', $route->id)
                ->whereNull('bus_only_student_id')
                ->whereIn('student_id', $schoolStudents->pluck('model_id'))
                ->whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
                ->get()
                ->keyBy(fn($a) => 'student_' . $a->student_id . '-' . $a->date->format('Y-m-d'));

            $allBusAtt = BusAttendance::where('route_id', $route->id)
                ->whereNotNull('bus_only_student_id')
                ->whereIn('bus_only_student_id', $busOnlyStudents->pluck('model_id'))
                ->whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
                ->get()
                ->keyBy(fn($a) => 'bus_' . $a->bus_only_student_id . '-' . $a->date->format('Y-m-d'));

            $attendances = $allRegAtt->union($allBusAtt);
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
