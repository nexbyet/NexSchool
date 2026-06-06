<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use App\Models\Standard;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function standards()
    {
        return response()->json(Standard::orderBy('sort_order')->with('classes')->get());
    }

    public function searchByGr(Request $request)
    {
        $request->validate(['gr_number' => 'required|string|max:20']);
        $students = Student::where('status', 'active')
            ->where('gr_number', 'LIKE', $request->gr_number . '%')
            ->with('currentStandard', 'currentClass')
            ->orderByRaw('CAST(gr_number AS UNSIGNED)')
            ->take(10)
            ->get()
            ->map(fn($s) => [
                'id'        => $s->id,
                'gr_number' => $s->gr_number,
                'name_gu'   => $s->student_name_gu,
                'name_en'   => $s->student_name_en,
                'standard'  => $s->currentStandard?->name,
                'class'     => $s->currentClass?->name,
                'has_photo' => !empty($s->photo),
                'photo'     => $s->photo ? asset('storage/' . $s->photo) : null,
            ]);
        return response()->json(['students' => $students]);
    }

    public function searchByClass(Request $request)
    {
        $request->validate([
            'standard_id' => 'required|exists:standards,id',
            'class_id'    => 'required|exists:school_classes,id',
        ]);
        $students = Student::where('status', 'active')
            ->where('current_standard_id', $request->standard_id)
            ->where('current_class_id', $request->class_id)
            ->with('currentStandard', 'currentClass')
            ->defaultSort()
            ->get()
            ->map(fn($s) => [
                'id'        => $s->id,
                'gr_number' => $s->gr_number,
                'name_gu'   => $s->student_name_gu,
                'name_en'   => $s->student_name_en,
                'standard'  => $s->currentStandard?->name,
                'class'     => $s->currentClass?->name,
                'has_photo' => !empty($s->photo),
                'photo'     => $s->photo ? asset('storage/' . $s->photo) : null,
            ]);
        return response()->json(['students' => $students, 'count' => $students->count()]);
    }

    public function preview($studentId, $lang)
    {
        $student = Student::with('currentStandard', 'currentClass')->findOrFail($studentId);
        $school = SchoolSetting::find(1);
        $isGu = $lang === 'gu';
        $data = $this->buildPreviewData($student, $school, $isGu);
        return response()->json($data);
    }

    public function print($studentId, $lang)
    {
        $student = Student::with('currentStandard', 'currentClass')->findOrFail($studentId);
        $school = SchoolSetting::find(1);
        $isGu = $lang === 'gu';
        $data = $this->buildPreviewData($student, $school, $isGu);
        $data['lang'] = $lang;
        $data['today'] = Carbon::now();
        $data['print'] = true;
        return response()->json($data);
    }

    private function buildPreviewData($student, $school, $isGu): array
    {
        $dob = $student->date_of_birth ? Carbon::parse($student->date_of_birth)->format('d/m/Y') : '—';
        $genderLabel = $student->sharirik_jaati === 'kumar' ? ($isGu ? 'શ્રી' : 'Mr.') : ($isGu ? 'કુ. /શ્રીમતી' : 'Ms./Mrs.');
        $genderText = $student->sharirik_jaati === 'kumar' ? ($isGu ? 'પુત્ર' : 'son') : ($isGu ? 'પુત્રી' : 'daughter');
        return [
            'student'     => $student,
            'school'      => $school,
            'school_name' => $isGu ? $school?->school_name_gu : $school?->school_name_en,
            'name'        => $isGu ? $student->student_name_gu : $student->student_name_en,
            'father_name' => $isGu ? $student->father_name_gu : $student->father_name_en,
            'surname'     => $isGu ? $student->surname_gu : $student->surname_en,
            'full_name'   => $isGu ? $student->full_name_gu : $student->full_name_en,
            'religion'    => $isGu ? $student->religion_gu : $student->religion_en,
            'cast'        => $isGu ? $student->cast_gu : $student->cast_en,
            'dob_in_text' => $isGu ? $student->dob_in_text_gu : $student->dob_in_text_en,
            'dob'         => $dob,
            'gender_label'=> $genderLabel,
            'gender_text' => $genderText,
            'standard'    => $student->currentStandard?->name ?? '—',
            'class'       => $student->currentClass?->name ?? '—',
            'gr_number'   => $student->gr_number ?? '—',
            'uid_no'      => $student->uid_no ?? '—',
            'photo_url'   => $student->photo ? asset('storage/' . $student->photo) : null,
            'date'        => Carbon::now()->format('d/m/Y'),
            'lang'        => $isGu ? 'gu' : 'en',
        ];
    }
}
