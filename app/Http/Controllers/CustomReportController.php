<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Standard;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomReportController extends Controller
{
    protected $availableFields = [];

    public function __construct()
    {
        $this->availableFields = [
            ['key' => 'sr_no', 'label_gu' => 'ક્રમ', 'label_en' => 'Sr No', 'type' => 'system', 'width' => 40],
            ['key' => 'gr_number', 'label_gu' => 'GR નંબર', 'label_en' => 'GR Number', 'type' => 'student', 'width' => 80],
            ['key' => 'full_name_gu', 'label_gu' => 'પૂરું નામ (ગુજરાતી)', 'label_en' => 'Full Name (Gujarati)', 'type' => 'student', 'width' => 160],
            ['key' => 'full_name_en', 'label_gu' => 'પૂરું નામ (English)', 'label_en' => 'Full Name (English)', 'type' => 'student', 'width' => 160],
            ['key' => 'student_name_gu', 'label_gu' => 'નામ (ગુજરાતી)', 'label_en' => 'Name (Gujarati)', 'type' => 'student', 'width' => 120],
            ['key' => 'student_name_en', 'label_gu' => 'નામ (English)', 'label_en' => 'Name (English)', 'type' => 'student', 'width' => 120],
            ['key' => 'father_name_gu', 'label_gu' => 'પિતાનું નામ (ગુજરાતી)', 'label_en' => "Father's Name (Gujarati)", 'type' => 'student', 'width' => 130],
            ['key' => 'father_name_en', 'label_gu' => 'પિતાનું નામ (English)', 'label_en' => "Father's Name (English)", 'type' => 'student', 'width' => 130],
            ['key' => 'surname_gu', 'label_gu' => 'અટક (ગુજરાતી)', 'label_en' => 'Surname (Gujarati)', 'type' => 'student', 'width' => 100],
            ['key' => 'surname_en', 'label_gu' => 'અટક (English)', 'label_en' => 'Surname (English)', 'type' => 'student', 'width' => 100],
            ['key' => 'mother_name_gu', 'label_gu' => 'માતાનું નામ (ગુજરાતી)', 'label_en' => "Mother's Name (Gujarati)", 'type' => 'student', 'width' => 130],
            ['key' => 'mother_name_en', 'label_gu' => 'માતાનું નામ (English)', 'label_en' => "Mother's Name (English)", 'type' => 'student', 'width' => 130],
            ['key' => 'date_of_birth', 'label_gu' => 'જન્મ તારીખ', 'label_en' => 'Date of Birth', 'type' => 'student', 'width' => 80],
            ['key' => 'age', 'label_gu' => 'ઉંમર', 'label_en' => 'Age', 'type' => 'computed', 'width' => 50],
            ['key' => 'sharirik_jaati', 'label_gu' => 'કુમાર/કુમારી', 'label_en' => 'Gender', 'type' => 'student', 'width' => 70],
            ['key' => 'category_gu', 'label_gu' => 'શ્રેણી (ગુજરાતી)', 'label_en' => 'Category (Gujarati)', 'type' => 'student', 'width' => 90],
            ['key' => 'category_en', 'label_gu' => 'શ્રેણી (English)', 'label_en' => 'Category (English)', 'type' => 'student', 'width' => 70],
            ['key' => 'religion_gu', 'label_gu' => 'ધર્મ (ગુજરાતી)', 'label_en' => 'Religion (Gujarati)', 'type' => 'student', 'width' => 70],
            ['key' => 'religion_en', 'label_gu' => 'ધર્મ (English)', 'label_en' => 'Religion (English)', 'type' => 'student', 'width' => 70],
            ['key' => 'cast_gu', 'label_gu' => 'જ્ઞાતિ (ગુજરાતી)', 'label_en' => 'Cast (Gujarati)', 'type' => 'student', 'width' => 80],
            ['key' => 'cast_en', 'label_gu' => 'જ્ઞાતિ (English)', 'label_en' => 'Cast (English)', 'type' => 'student', 'width' => 80],
            ['key' => 'mobile', 'label_gu' => 'મોબાઇલ', 'label_en' => 'Mobile', 'type' => 'student', 'width' => 100],
            ['key' => 'whatsapp', 'label_gu' => 'WhatsApp', 'label_en' => 'WhatsApp', 'type' => 'student', 'width' => 100],
            ['key' => 'aadhar_no', 'label_gu' => 'આધાર નંબર', 'label_en' => 'Aadhar Number', 'type' => 'student', 'width' => 110],
            ['key' => 'apaar_id', 'label_gu' => 'APAAR ID', 'label_en' => 'APAAR ID', 'type' => 'student', 'width' => 100],
            ['key' => 'uid_no', 'label_gu' => 'UID નંબર', 'label_en' => 'UID Number', 'type' => 'student', 'width' => 110],
            ['key' => 'pen_no', 'label_gu' => 'PEN નંબર', 'label_en' => 'PEN Number', 'type' => 'student', 'width' => 100],
            ['key' => 'current_standard', 'label_gu' => 'હાલનું ધોરણ', 'label_en' => 'Current Standard', 'type' => 'relation', 'width' => 60],
            ['key' => 'current_class', 'label_gu' => 'હાલનો વર્ગ', 'label_en' => 'Current Class', 'type' => 'relation', 'width' => 60],
            ['key' => 'date_of_admission', 'label_gu' => 'પ્રવેશ તારીખ', 'label_en' => 'Admission Date', 'type' => 'student', 'width' => 80],
            ['key' => 'admission_standard', 'label_gu' => 'પ્રવેશ ધોરણ', 'label_en' => 'Admission Standard', 'type' => 'relation', 'width' => 60],
            ['key' => 'last_school_gu', 'label_gu' => 'છેલ્લી શાળા (ગુજરાતી)', 'label_en' => 'Last School (Gujarati)', 'type' => 'student', 'width' => 130],
            ['key' => 'last_school_en', 'label_gu' => 'છેલ્લી શાળા (English)', 'label_en' => 'Last School (English)', 'type' => 'student', 'width' => 130],
            ['key' => 'birth_place_gu', 'label_gu' => 'જન્મ સ્થળ (ગુજરાતી)', 'label_en' => 'Birth Place (Gujarati)', 'type' => 'student', 'width' => 100],
            ['key' => 'native_place_gu', 'label_gu' => 'વતન (ગુજરાતી)', 'label_en' => 'Native Place (Gujarati)', 'type' => 'student', 'width' => 100],
            ['key' => 'is_minority', 'label_gu' => 'લઘુમતી', 'label_en' => 'Minority', 'type' => 'student', 'width' => 50],
            ['key' => 'admission_under_rte', 'label_gu' => 'RTE', 'label_en' => 'RTE', 'type' => 'student', 'width' => 50],
        ];
    }

    public function index()
    {
        $standards = Standard::orderBy('sort_order')->get();
        $classes = SchoolClass::whereIn('standard_id', $standards->pluck('id'))
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get(['id', 'name', 'standard_id']);
        $activeYear = AcademicYear::getActive();
        $school = SchoolSetting::find(1);
        $fields = $this->availableFields;

        $fieldGroups = [
            'personal' => ['title_gu' => 'વ્યક્તિગત માહિતી', 'title_en' => 'Personal Info'],
            'identity' => ['title_gu' => 'ઓળખ', 'title_en' => 'Identity'],
            'academic' => ['title_gu' => 'શૈક્ષણિક', 'title_en' => 'Academic'],
            'contact'  => ['title_gu' => 'સંપર્ક', 'title_en' => 'Contact'],
        ];

        return view('custom-report.index', compact('standards', 'classes', 'activeYear', 'school', 'fields', 'fieldGroups'));
    }

    public function preview(Request $request)
    {
        $data = $request->validate([
            'columns' => 'required|array|min:1',
            'columns.*' => 'string',
            'standard_id' => 'nullable|exists:standards,id',
            'class_id' => 'nullable|exists:school_classes,id',
            'report_type' => 'required|in:filled,blank',
            'blank_rows' => 'nullable|integer|min:1|max:200',
            'title_gu' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
        ]);

        $columns = $data['columns'];
        $hasSrNo = in_array('sr_no', $columns);
        $columns = array_values(array_filter($columns, fn($c) => $c !== 'sr_no'));

        $students = collect();
        $studentCount = 0;

        if ($data['report_type'] === 'filled') {
            $query = Student::with(['currentStandard', 'currentClass', 'admissionStandard'])
                ->whereIn('status', ['active', 'alumni']);

            if (!empty($data['standard_id'])) {
                $query->where('current_standard_id', $data['standard_id']);
            }
            if (!empty($data['class_id'])) {
                $query->where('current_class_id', $data['class_id']);
            }

            $studentCount = $query->count();
            $query->defaultSort();

            if ($studentCount > 500) {
                return response()->json([
                    'success' => false,
                    'message' => 'વિદ્યાર્થીઓની સંખ્યા 500 થી વધુ છે. કૃપા કરીને ધોરણ અથવા વર્ગ પસંદ કરો.',
                ], 422);
            }

            $students = $query->get()->map(function ($s) use ($columns) {
                $row = [];
                foreach ($columns as $col) {
                    $row[$col] = $this->getFieldValue($s, $col);
                }
                return $row;
            });
        } else {
            $studentCount = (int) ($data['blank_rows'] ?? 20);
            for ($i = 1; $i <= $studentCount; $i++) {
                $row = [];
                foreach ($columns as $col) {
                    $row[$col] = '';
                }
                $students->push($row);
            }
        }

        $school = SchoolSetting::find(1);
        $titleGu = $data['title_gu'] ?? '';
        $titleEn = $data['title_en'] ?? '';

        $html = view('custom-report.print', compact('students', 'columns', 'hasSrNo', 'school', 'titleGu', 'titleEn', 'studentCount'))->render();

        return response()->json(['success' => true, 'html' => $html]);
    }

    protected function getFieldValue($student, $key)
    {
        return match ($key) {
            'gr_number' => $student->gr_number,
            'full_name_gu' => $student->full_name_gu,
            'full_name_en' => $student->full_name_en,
            'student_name_gu' => $student->student_name_gu,
            'student_name_en' => $student->student_name_en,
            'father_name_gu' => $student->father_name_gu,
            'father_name_en' => $student->father_name_en,
            'surname_gu' => $student->surname_gu,
            'surname_en' => $student->surname_en,
            'mother_name_gu' => $student->mother_name_gu,
            'mother_name_en' => $student->mother_name_en,
            'date_of_birth' => $student->date_of_birth ? Carbon::parse($student->date_of_birth)->format('d/m/Y') : '',
            'age' => $student->date_of_birth ? Carbon::parse($student->date_of_birth)->age : '',
            'sharirik_jaati' => $student->sharirik_jaati === 'kumar' ? 'કુમાર' : ($student->sharirik_jaati === 'kumari' ? 'કુમારી' : ''),
            'category_gu' => $student->category_gu,
            'category_en' => $student->category_en,
            'religion_gu' => $student->religion_gu,
            'religion_en' => $student->religion_en,
            'cast_gu' => $student->cast_gu,
            'cast_en' => $student->cast_en,
            'mobile' => $student->mobile,
            'whatsapp' => $student->whatsapp,
            'aadhar_no' => $student->aadhar_no,
            'apaar_id' => $student->apaar_id,
            'uid_no' => $student->uid_no,
            'pen_no' => $student->pen_no,
            'current_standard' => $student->currentStandard?->name ?? '',
            'current_class' => $student->currentClass?->name ?? '',
            'date_of_admission' => $student->date_of_admission ? Carbon::parse($student->date_of_admission)->format('d/m/Y') : '',
            'admission_standard' => $student->admissionStandard?->name ?? '',
            'last_school_gu' => $student->last_school_gu,
            'last_school_en' => $student->last_school_en,
            'birth_place_gu' => $student->birth_place_gu,
            'native_place_gu' => $student->native_place_gu,
            'is_minority' => $student->is_minority ? '✓' : '',
            'admission_under_rte' => $student->admission_under_rte ? '✓' : '',
            default => '',
        };
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
