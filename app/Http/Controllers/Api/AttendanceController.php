<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Standard;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'standard_id' => 'required|exists:standards,id',
            'class_id' => 'required|exists:school_classes,id',
            'date' => 'required|date_format:Y-m-d',
            'lang' => 'nullable|in:gu,en',
        ]);

        $lang = $request->get('lang', 'gu');
        $date = Carbon::parse($request->date);
        $standard = Standard::findOrFail($request->standard_id);
        $class = SchoolClass::findOrFail($request->class_id);

        $students = Student::where('current_standard_id', $standard->id)
            ->where('current_class_id', $class->id)
            ->where('status', 'active')
            ->where('date_of_admission', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('leaving_date')->orWhere('leaving_date', '>=', $date);
            })
            ->orderBy('gr_number')
            ->get(['id', 'gr_number', 'student_name_gu', 'student_name_en',
                'sharirik_jaati', 'date_of_birth', 'category_gu', 'category_en']);

        $existingAttendance = Attendance::whereIn('student_id', $students->pluck('id'))
            ->whereDate('date', $date)
            ->get(['student_id', 'status', 'marked_by'])
            ->keyBy('student_id');

        $dayNamesGu = ['રવિ', 'સોમ', 'મંગળ', 'બુધ', 'ગુરુ', 'શુક્ર', 'શનિ'];

        $studentsData = $students->map(function ($s) use ($lang, $existingAttendance) {
            return [
                'id' => $s->id,
                'gr_number' => $s->gr_number,
                'name' => $lang === 'gu' ? $s->student_name_gu : $s->student_name_en,
                'sharirik_jaati' => $s->sharirik_jaati,
                'date_of_birth' => $s->date_of_birth ? Carbon::parse($s->date_of_birth)->format('d/m/Y') : null,
                'category' => $lang === 'gu' ? $s->category_gu : $s->category_en,
                'attendance_status' => $existingAttendance[$s->id]->status ?? null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'standard' => $standard->only(['id', 'name']),
                'class' => $class->only(['id', 'name']),
                'date' => $date->format('Y-m-d'),
                'day_name' => $dayNamesGu[$date->dayOfWeek],
                'total_students' => $students->count(),
                'students' => $studentsData,
            ],
        ]);
    }

    public function mark(Request $request)
    {
        $data = $request->validate([
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.date' => 'required|date_format:Y-m-d',
            'attendances.*.status' => 'required|in:present,absent,absent_with_leave,medical_leave',
        ]);

        $userId = $request->user()->id;
        $saved = 0;

        foreach ($data['attendances'] as $att) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $att['student_id'],
                    'date' => $att['date'],
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
            'message' => $saved . ' attendance records saved.',
            'count' => $saved,
        ]);
    }
}
