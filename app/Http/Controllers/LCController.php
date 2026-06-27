<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Standard;
use App\Models\Student;
use App\Models\User;
use App\Models\SchoolSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LCController extends Controller
{
    public function index()
    {
        $standards = Standard::orderBy('sort_order')->get();
        $classes = SchoolClass::whereIn('standard_id', $standards->pluck('id'))
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get();
        $activeYear = AcademicYear::getActive();
        return view('lc.index', compact('standards', 'classes', 'activeYear'));
    }

    public function search(Request $request)
    {
        $query = Student::with(['currentStandard', 'currentClass', 'admissionStandard']);

        if ($request->filled('gr_number')) {
            $query->where('gr_number', $request->gr_number);
        } else {
            if ($request->filled('standard_id')) {
                $query->where('current_standard_id', $request->standard_id);
            }
            if ($request->filled('class_id')) {
                $query->where('current_class_id', $request->class_id);
            }
            $query->where('status', 'active');
        }

        $students = $query->defaultSort()->limit(50)->get()->map(function ($s) {
            return [
                'id' => $s->id,
                'gr_number' => $s->gr_number,
                'full_name_gu' => $s->full_name_gu,
                'full_name_en' => $s->full_name_en,
                'father_name_gu' => $s->father_name_gu,
                'father_name_en' => $s->father_name_en,
                'standard' => $s->currentStandard?->name ?? '',
                'class' => $s->currentClass?->name ?? '',
                'admission_standard' => $s->admissionStandard?->name ?? '',
                'date_of_admission' => $s->date_of_admission ? Carbon::parse($s->date_of_admission)->format('d/m/Y') : '',
                'photo' => $s->photo,
                'l_c_number' => $s->lc_number,
                'leaving_date' => $s->leaving_date ? Carbon::parse($s->leaving_date)->format('d/m/Y') : '',
                'leaving_reason_gu' => $s->leaving_reason_gu,
                'leaving_reason_en' => $s->leaving_reason_en,
                'leaving_standard_id' => $s->leaving_standard_id,
                'lc_issue_date' => $s->lc_issue_date ? Carbon::parse($s->lc_issue_date)->format('d/m/Y') : '',
                'attendance_days' => $s->attendance_days,
                'leaving_remarks' => $s->leaving_remarks,
                'status' => $s->status,
            ];
        });

        return response()->json(['success' => true, 'students' => $students]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'lc_number' => 'required|string|max:50',
            'leaving_date' => 'required|date_format:d/m/Y',
            'leaving_reason_gu' => 'nullable|string',
            'leaving_reason_en' => 'nullable|string',
            'leaving_standard_id' => 'nullable|exists:standards,id',
            'lc_issue_date' => 'nullable|date_format:d/m/Y',
            'attendance_days' => 'nullable|integer|min:0',
            'leaving_remarks' => 'nullable|string',
        ]);

        $student = Student::findOrFail($data['student_id']);

        $updateData = [
            'lc_number' => $data['lc_number'],
            'leaving_date' => Carbon::createFromFormat('d/m/Y', $data['leaving_date'])->format('Y-m-d'),
            'leaving_reason_gu' => $data['leaving_reason_gu'] ?? null,
            'leaving_reason_en' => $data['leaving_reason_en'] ?? null,
            'leaving_standard_id' => $data['leaving_standard_id'] ?? null,
            'lc_issue_date' => $data['lc_issue_date']
                ? Carbon::createFromFormat('d/m/Y', $data['lc_issue_date'])->format('Y-m-d')
                : Carbon::createFromFormat('d/m/Y', $data['leaving_date'])->format('Y-m-d'),
            'attendance_days' => $data['attendance_days'] ?? null,
            'leaving_remarks' => $data['leaving_remarks'] ?? null,
            'status' => 'alumni',
        ];

        $student->update($updateData);

        User::where('student_id', $student->id)->update(['active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'વિદ્યાર્થી માટે LC જારી કરવામાં આવ્યો. વિદ્યાર્થી હવે alumni છે.',
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'standard_id' => 'nullable|exists:standards,id',
            'class_id' => 'nullable|exists:school_classes,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);

        $query = Student::with(['leavingStandard', 'currentStandard', 'currentClass'])
            ->where('status', 'alumni')
            ->whereNotNull('lc_number');

        if (!empty($data['standard_id'])) {
            $query->where('leaving_standard_id', $data['standard_id']);
        }
        if (!empty($data['class_id'])) {
            $query->where('current_class_id', $data['class_id']);
        }
        if (!empty($data['from_date'])) {
            $query->where('leaving_date', '>=', $data['from_date']);
        }
        if (!empty($data['to_date'])) {
            $query->where('leaving_date', '<=', $data['to_date']);
        }
        if (!empty($data['academic_year_id'])) {
            $year = AcademicYear::find($data['academic_year_id']);
            if ($year) {
                $query->whereBetween('leaving_date', [$year->session_start_date, $year->session_end_date]);
            }
        }

        $students = $query->orderBy('leaving_date')->defaultSort()->get();
        $school = SchoolSetting::find(1);
        $activeYear = AcademicYear::find($data['academic_year_id']) ?? AcademicYear::getActive();

        return view('lc.register', compact('students', 'school', 'activeYear', 'data'));
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
