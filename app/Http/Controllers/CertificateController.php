<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Standard;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index()
    {
        $standards = Standard::orderBy('sort_order')->get();
        return view('certificate.index', compact('standards'));
    }

    public function preview($studentId, $lang)
    {
        $student = Student::with('currentStandard', 'currentClass')->findOrFail($studentId);
        $school = SchoolSetting::find(1);
        $name = $lang === 'gu' ? $student->student_name_gu : $student->student_name_en;
        $fatherName = $lang === 'gu' ? $student->father_name_gu : $student->father_name_en;
        $surname = $lang === 'gu' ? $student->surname_gu : $student->surname_en;
        $fullName = $lang === 'gu' ? $student->full_name_gu : $student->full_name_en;
        $religion = $lang === 'gu' ? $student->religion_gu : $student->religion_en;
        $cast = $lang === 'gu' ? $student->cast_gu : $student->cast_en;
        $standard = $student->currentStandard?->name ?? '—';
        $class = $student->currentClass?->name ?? '—';
        $dobInText = $lang === 'gu' ? $student->dob_in_text_gu : $student->dob_in_text_en;
        $dob = $student->date_of_birth ? Carbon::parse($student->date_of_birth)->format('d/m/Y') : '—';
        $grNumber = $student->gr_number ?? '—';
        $uidNo = $student->uid_no ?? '—';
        $dateGu = Carbon::now()->format('d/m/Y');
        $gender = $student->sharirik_jaati === 'kumar' ? ($lang === 'gu' ? 'શ્રી' : 'Mr.') : ($lang === 'gu' ? 'કુ. /શ્રીમતી' : 'Ms./Mrs.');
        $genderText = $student->sharirik_jaati === 'kumar' ? ($lang === 'gu' ? 'પુત્ર' : 'son') : ($lang === 'gu' ? 'પુત્રી' : 'daughter');
        $photoUrl = $student->photo ? asset('storage/' . $student->photo) : null;

        $html = view('certificate._preview', compact(
            'student', 'lang', 'school', 'name', 'fatherName', 'surname', 'fullName',
            'religion', 'cast', 'standard', 'class', 'dobInText', 'dob',
            'grNumber', 'uidNo', 'dateGu', 'gender', 'genderText', 'photoUrl'
        ))->render();

        return response()->json(['html' => $html]);
    }

    public function searchByGr(Request $request)
    {
        $data = $request->validate([
            'gr_number' => 'required|string|max:20',
        ]);

        $students = Student::where('status', 'active')
            ->where('gr_number', 'LIKE', $data['gr_number'] . '%')
            ->with('currentStandard', 'currentClass')
            ->orderByRaw('CAST(gr_number AS UNSIGNED)')
            ->take(10)
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'gr_number' => $s->gr_number,
                'name_gu' => $s->student_name_gu,
                'name_en' => $s->student_name_en,
                'standard' => $s->currentStandard?->name,
                'class' => $s->currentClass?->name,
                'has_photo' => !empty($s->photo),
                'photo' => $s->photo ? asset('storage/' . $s->photo) : null,
            ]);

        return response()->json(['students' => $students]);
    }

    public function searchByClass(Request $request)
    {
        $data = $request->validate([
            'standard_id' => 'required|exists:standards,id',
            'class_id' => 'required|exists:school_classes,id',
        ]);

        $students = Student::where('status', 'active')
            ->where('current_standard_id', $data['standard_id'])
            ->where('current_class_id', $data['class_id'])
            ->defaultSort()
            ->with('currentStandard', 'currentClass')
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'gr_number' => $s->gr_number,
                'name_gu' => $s->student_name_gu,
                'name_en' => $s->student_name_en,
                'standard' => $s->currentStandard?->name,
                'class' => $s->currentClass?->name,
                'has_photo' => !empty($s->photo),
                'photo' => $s->photo ? asset('storage/' . $s->photo) : null,
            ]);

        $html = view('certificate._student_table', compact('students'))->render();

        return response()->json(['html' => $html, 'count' => $students->count()]);
    }

    public function print($studentId, $lang)
    {
        $student = Student::with('currentStandard', 'currentClass')->findOrFail($studentId);
        $school = SchoolSetting::find(1);

        $name = $lang === 'gu' ? $student->student_name_gu : $student->student_name_en;
        $fatherName = $lang === 'gu' ? $student->father_name_gu : $student->father_name_en;
        $surname = $lang === 'gu' ? $student->surname_gu : $student->surname_en;
        $fullName = $lang === 'gu' ? $student->full_name_gu : $student->full_name_en;
        $religion = $lang === 'gu' ? $student->religion_gu : $student->religion_en;
        $cast = $lang === 'gu' ? $student->cast_gu : $student->cast_en;
        $standard = $student->currentStandard?->name ?? '—';
        $class = $student->currentClass?->name ?? '—';
        $dobInText = $lang === 'gu' ? $student->dob_in_text_gu : $student->dob_in_text_en;
        $dob = $student->date_of_birth ? Carbon::parse($student->date_of_birth)->format('d/m/Y') : '—';
        $grNumber = $student->gr_number ?? '—';
        $uidNo = $student->uid_no ?? '—';
        $today = Carbon::now();
        $date = $lang === 'gu'
            ? $today->format('d/m/Y')
            : $today->format('d/m/Y');
        $dateGu = $today->format('d/m/Y');
        $gender = $student->sharirik_jaati === 'kumar' ? ($lang === 'gu' ? 'શ્રી' : 'Mr.') : ($lang === 'gu' ? 'કુ. /શ્રીમતી' : 'Ms./Mrs.');
        $genderText = $student->sharirik_jaati === 'kumar' ? ($lang === 'gu' ? 'પુત્ર' : 'son') : ($lang === 'gu' ? 'પુત્રી' : 'daughter');
        $photoUrl = $student->photo ? asset('storage/' . $student->photo) : null;

        return view('certificate.bonafied_print', compact(
            'student', 'lang', 'school', 'name', 'fatherName', 'surname', 'fullName',
            'religion', 'cast', 'standard', 'class', 'dobInText', 'dob',
            'grNumber', 'uidNo', 'date', 'dateGu', 'gender', 'genderText', 'photoUrl', 'today'
        ));
    }
}
