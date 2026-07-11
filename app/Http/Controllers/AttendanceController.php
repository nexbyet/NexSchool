<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\PublicHoliday;
use App\Models\SchoolClass;
use App\Models\Standard;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $standards = Standard::orderBy('sort_order')->get();
        return view('attendance.index', compact('standards'));
    }

    public function students(Request $request)
    {
        $data = $request->validate([
            'standard_id' => 'required|exists:standards,id',
            'class_id' => 'required|exists:school_classes,id',
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = Carbon::parse($data['date']);
        $standard = Standard::findOrFail($data['standard_id']);
        $class = SchoolClass::findOrFail($data['class_id']);

        $isSunday = $date->isSunday();
        $holidays = PublicHoliday::whereYear('date', $date->year)->pluck('date')->map(fn($d) => $d->format('Y-m-d'))->toArray();
        $isHoliday = !$isSunday && in_array($date->format('Y-m-d'), $holidays);

        if ($isSunday || $isHoliday) {
            $dayNamesGu = ['રવિ', 'સોમ', 'મંગળ', 'બુધ', 'ગુરુ', 'શુક્ર', 'શનિ'];
            $label = $isSunday ? 'રવિવાર' : 'રજા';
            return response()->json([
                'students_html' => '',
                'is_sunday' => $isSunday,
                'is_holiday' => $isHoliday,
                'day_name' => $dayNamesGu[$date->dayOfWeek],
                'student_count' => 0,
                'error' => $date->format('d/m/Y') . ' (' . $dayNamesGu[$date->dayOfWeek] . ') — ' . $label . 'ના દિવસે હાજરી લઈ શકાતી નથી.',
            ]);
        }

        $students = Student::where('current_standard_id', $standard->id)
            ->where('current_class_id', $class->id)
            ->whereIn('status', ['active', 'alumni'])
            ->where('is_registered', true)
            ->where('date_of_admission', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('leaving_date')->orWhere('leaving_date', '>=', $date);
            })
            ->defaultSort()
            ->get();

        $existing = Attendance::whereIn('student_id', $students->pluck('id'))
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_id');

        $lastDates = [];
        $lastDateStrs = [];
        $d = $date->copy()->subDay();
        while (count($lastDates) < 5) {
            if (!$d->isSunday() && !in_array($d->format('Y-m-d'), $holidays)) {
                $lastDates[] = $d->copy();
                $lastDateStrs[] = $d->format('Y-m-d');
            }
            $d->subDay();
        }
        $lastDates = array_reverse($lastDates);

        $lastAttendance = Attendance::whereIn('student_id', $students->pluck('id'))
            ->whereIn('date', $lastDateStrs)
            ->get()
            ->keyBy(fn($a) => $a->student_id . '-' . $a->date->format('Y-m-d'));

        $studentsHtml = view('attendance._list', compact(
            'students', 'existing', 'date', 'standard', 'class', 'lastDates', 'lastAttendance'
        ))->render();

        $dayNamesGu = ['રવિ', 'સોમ', 'મંગળ', 'બુધ', 'ગુરુ', 'શુક્ર', 'શનિ'];

        return response()->json([
            'students_html' => $studentsHtml,
            'is_sunday' => false,
            'is_holiday' => false,
            'day_name' => $dayNamesGu[$date->dayOfWeek],
            'student_count' => $students->count(),
        ]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'standard_id' => 'required|exists:standards,id',
            'class_id' => 'required|exists:school_classes,id',
            'date' => 'required|date_format:Y-m-d',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:present,absent,absent_with_leave,medical_leave',
        ]);

        $date = Carbon::parse($data['date']);

        if ($date->isSunday()) {
            return response()->json(['success' => false, 'message' => 'રવિવારે હાજરી સેવ કરી શકાતી નથી.'], 422);
        }

        $isHoliday = PublicHoliday::whereYear('date', $date->year)->whereDate('date', $date)->exists();
        if ($isHoliday) {
            return response()->json(['success' => false, 'message' => 'રજાના દિવસે હાજરી સેવ કરી શકાતી નથી.'], 422);
        }

        $userId = $request->user()->id;
        $saved = 0;

        foreach ($data['attendances'] as $att) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $att['student_id'],
                    'date' => $date,
                ],
                [
                    'status' => $att['status'],
                    'marked_by' => $userId,
                ]
            );
            $saved++;
        }

        return response()->json([
            'success' => true,
            'message' => $saved . ' હાજરી સફળતાપૂર્વક સેવ થઈ.',
            'count' => $saved,
        ]);
    }
}
