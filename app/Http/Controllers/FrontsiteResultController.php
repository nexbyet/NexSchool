<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ResultPublication;
use App\Models\SchoolSetting;
use App\Models\Student;
use Illuminate\Http\Request;

class FrontsiteResultController extends Controller
{
    protected $school;

    public function __construct()
    {
        $this->school = SchoolSetting::find(1);
    }

    public function index()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $school = $this->school;
        return view('frontsite.result.index', compact('academicYears', 'school'));
    }

    public function show(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'gr_number' => 'required|string',
            'dob' => 'required|string',
            'semester' => 'required|in:1,2,both',
        ]);

        $school = $this->school;

        // DOB parse: DDMMYYYY
        $dob = $data['dob'];
        if (!preg_match('/^\d{8}$/', $dob)) {
            return back()->withErrors(['dob' => 'જન્મ તારીખ DDMMYYYY ફોર્મેટમાં દાખલ કરો.'])->withInput();
        }
        $day = substr($dob, 0, 2);
        $month = substr($dob, 2, 2);
        $year = substr($dob, 4, 4);
        if (!checkdate((int) $month, (int) $day, (int) $year)) {
            return back()->withErrors(['dob' => 'અમાન્ય જન્મ તારીખ.'])->withInput();
        }
        $dobDate = $year . '-' . $month . '-' . $day;

        $student = Student::where('gr_number', $data['gr_number'])->first();
        if (!$student || (string) $student->date_of_birth !== $dobDate) {
            return back()->withErrors(['gr_number' => 'GR નંબર અથવા જન્મ તારીખ મેળ ખાતા નથી.'])->withInput();
        }

        // Past-year accuracy: student's class may have changed. Look up the class
        // from exam_marks for the selected academic year; fall back to current.
        $standardId = $student->current_standard_id;
        $classId = $student->current_class_id;

        $mark = \App\Models\ExamMark::where('student_id', $student->id)
            ->where('academic_year_id', $data['academic_year_id'])
            ->first();
        if ($mark) {
            $apply = \App\Models\ExamPatternApply::where('exam_pattern_id', $mark->exam_pattern_id)
                ->where('subject_id', $mark->subject_id)
                ->first();
            if ($apply) {
                $standardId = $apply->standard_id;
                $classId = $apply->school_class_id;
            }
        }

        $published = ResultPublication::isPublished(
            $data['academic_year_id'],
            $standardId,
            $classId,
            $data['semester']
        );

        if (!$published) {
            return back()->withErrors(['gr_number' => 'આ વિદ્યાર્થીનું result હજી publish થયું નથી.'])->withInput();
        }

        $report = (new \App\Http\Controllers\ExamReportController())->buildReport([
            'academic_year_id' => $data['academic_year_id'],
            'standard_id' => $standardId,
            'school_class_id' => $classId,
            'semester' => $data['semester'],
        ]);

        // Student ne fakt potanu result j dekhavu (marksheet)
        $report['rows'] = array_values(array_filter($report['rows'], function ($r) use ($student) {
            return (int) $r['id'] === (int) $student->id;
        }));

        $std = \App\Models\Standard::find($standardId);
        $cls = \App\Models\SchoolClass::find($classId);

        return view('frontsite.result.show', array_merge($report, [
            'school' => $school,
            'student' => $student,
            'academicYear' => \App\Models\AcademicYear::find($data['academic_year_id']),
            'semester' => $data['semester'],
            'stdName' => $std->name ?? '',
            'clsName' => $cls->name ?? '',
        ]));
    }
}