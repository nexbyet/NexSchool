<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\FeePayment;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Standard;
use App\Models\Student;
use App\Models\StudentFee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomReportController extends Controller
{
    protected $availableFields = [];
    protected $feeData = [];

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
            ['key' => 'gaam', 'label_gu' => 'ગામ (ગુજરાતી)', 'label_en' => 'Village (Gujarati)', 'type' => 'student', 'width' => 100],
            ['key' => 'gaam_en', 'label_gu' => 'ગામ (English)', 'label_en' => 'Village (English)', 'type' => 'student', 'width' => 100],
            ['key' => 'is_minority', 'label_gu' => 'લઘુમતી', 'label_en' => 'Minority', 'type' => 'student', 'width' => 50],
            ['key' => 'admission_under_rte', 'label_gu' => 'RTE', 'label_en' => 'RTE', 'type' => 'student', 'width' => 50],
            ['key' => 'is_registered', 'label_gu' => 'નોંધાયેલ', 'label_en' => 'Registered', 'type' => 'student', 'width' => 50],
            // Fee fields
            ['key' => 'total_fee', 'label_gu' => 'કુલ ફી', 'label_en' => 'Total Fee', 'type' => 'fee', 'width' => 90],
            ['key' => 'paid_fee', 'label_gu' => 'ભરેલ ફી', 'label_en' => 'Paid Fee', 'type' => 'fee', 'width' => 90],
            ['key' => 'due_fee', 'label_gu' => 'બાકી ફી', 'label_en' => 'Due Fee', 'type' => 'fee', 'width' => 90],
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

        return view('custom-report.index', compact('standards', 'classes', 'activeYear', 'school', 'fields'));
    }

    public function preview(Request $request)
    {
        $data = $request->validate([
            'columns' => 'required|array|min:1',
            'columns.*' => 'string',
            'column_widths' => 'nullable|array',
            'column_widths.*' => 'nullable|integer|min:10|max:500',
            'custom_columns' => 'nullable|array',
            'custom_columns.*.header_gu' => 'nullable|string|max:255',
            'custom_columns.*.header_en' => 'nullable|string|max:255',
            'custom_columns.*.width' => 'nullable|integer|min:10|max:500',
            'selection_mode' => 'nullable|in:filter,manual',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'integer|exists:students,id',
            'sort_column' => 'nullable|string',
            'sort_direction' => 'nullable|in:asc,desc',
            'include_unregistered' => 'nullable|boolean',
            'standard_id' => 'nullable|exists:standards,id',
            'class_id' => 'nullable|exists:school_classes,id',
            'report_type' => 'required|in:filled,blank',
            'blank_rows' => 'nullable|integer|min:1|max:200',
            'title_gu' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'row_height' => 'nullable|integer|min:4|max:50',
        ]);

        $columns = $data['columns'];
        $hasSrNo = in_array('sr_no', $columns);
        $columns = array_values(array_filter($columns, fn($c) => $c !== 'sr_no'));

        $columnWidths = $data['column_widths'] ?? [];
        $customColumns = $data['custom_columns'] ?? [];
        $rowHeight = (int) ($data['row_height'] ?? 7);
        $selectionMode = $data['selection_mode'] ?? 'filter';
        $standardName = null;
        $className = null;
        $selectionLabel = '';

        $students = collect();
        $studentCount = 0;

        if ($data['report_type'] === 'filled') {
            $query = Student::with(['currentStandard', 'currentClass', 'admissionStandard'])
                ->whereIn('status', ['active', 'alumni']);

            // Filter out unregistered students unless explicitly included
            if (!$request->boolean('include_unregistered')) {
                $query->where('is_registered', true);
            }

            if ($selectionMode === 'manual' && !empty($data['student_ids'])) {
                $query->whereIn('id', $data['student_ids']);
                $selectionLabel = 'પસંદ કરેલ વિદ્યાર્થીઓ: ' . count($data['student_ids']);
            } else {
                if (!empty($data['standard_id'])) {
                    $query->where('current_standard_id', $data['standard_id']);
                    $std = Standard::find($data['standard_id']);
                    $standardName = $std?->name;
                }
                if (!empty($data['class_id'])) {
                    $query->where('current_class_id', $data['class_id']);
                    $cls = SchoolClass::find($data['class_id']);
                    $className = $cls?->name;
                }
                $selectionLabel = trim(($standardName ?? 'બધા ધોરણ') . ($className ? ' — ' . $className : ''));
            }

            $studentCount = $query->count();
            $query->defaultSort();

            if ($selectionMode !== 'manual' && $studentCount > 500) {
                return response()->json([
                    'success' => false,
                    'message' => 'વિદ્યાર્થીઓની સંખ્યા 500 થી વધુ છે. કૃપા કરીને ધોરણ અથવા વર્ગ પસંદ કરો.',
                ], 422);
            }

            if ($studentCount > 500) {
                return response()->json([
                    'success' => false,
                    'message' => 'વિદ્યાર્થીઓની સંખ્યા 500 થી વધુ છે. કૃપા કરીને ઓછા વિદ્યાર્થીઓ પસંદ કરો.',
                ], 422);
            }

            // Load fee data if any fee column is selected
            $hasFeeFields = !empty(array_intersect($columns, ['total_fee', 'paid_fee', 'due_fee']));
            if ($hasFeeFields) {
                $this->loadFeeData($query->pluck('id')->toArray());
            }

            $students = $query->get()->map(function ($s) use ($columns) {
                $row = [];
                foreach ($columns as $col) {
                    $row[$col] = $this->getFieldValue($s, $col);
                }
                return $row;
            });

            // Sort by column
            if (!empty($data['sort_column']) && in_array($data['sort_column'], $columns)) {
                $direction = $data['sort_direction'] ?? 'asc';
                $sortCol = $data['sort_column'];
                $students = $students->sortBy(function ($row) use ($sortCol) {
                    $val = $row[$sortCol] ?? '';
                    // Try numeric sort if value looks like a number
                    if (is_numeric($val)) {
                        return (float) $val;
                    }
                    // Remove ₹ and commas for fee values
                    $clean = str_replace(['₹', ',', ' '], '', $val);
                    if (is_numeric($clean)) {
                        return (float) $clean;
                    }
                    return mb_strtolower(trim($val), 'UTF-8');
                }, SORT_REGULAR, $direction === 'desc');
                $students = $students->values();
            }
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

        $html = view('custom-report.print', compact(
            'students', 'columns', 'hasSrNo', 'school', 'titleGu', 'titleEn', 'studentCount',
            'columnWidths', 'customColumns', 'rowHeight', 'standardName', 'className', 'selectionLabel'
        ))->render();

        return response()->json(['success' => true, 'html' => $html]);
    }

    protected function loadFeeData(array $studentIds)
    {
        $yearId = AcademicYear::getActive()?->id;
        if (!$yearId || empty($studentIds)) return;

        $studentFees = StudentFee::whereIn('student_id', $studentIds)
            ->where('academic_year_id', $yearId)
            ->get();

        if ($studentFees->isEmpty()) return;

        $payments = FeePayment::whereIn('student_fee_id', $studentFees->pluck('id'))
            ->select('student_fee_id', 'amount_paid')
            ->get()
            ->groupBy('student_fee_id');

        $this->feeData = [];
        foreach ($studentFees as $sf) {
            $paid = ($payments->get($sf->id) ?? collect())->sum('amount_paid');
            $due = max(0, $sf->net_amount - $paid);

            if (!isset($this->feeData[$sf->student_id])) {
                $this->feeData[$sf->student_id] = ['total_fee' => 0, 'paid_fee' => 0, 'due_fee' => 0];
            }
            $this->feeData[$sf->student_id]['total_fee'] += (float) $sf->net_amount;
            $this->feeData[$sf->student_id]['paid_fee'] += (float) $paid;
            $this->feeData[$sf->student_id]['due_fee'] += (float) $due;
        }
    }

    protected function getFieldValue($student, $key)
    {
        $feeKeys = ['total_fee', 'paid_fee', 'due_fee'];
        if (in_array($key, $feeKeys)) {
            $data = $this->feeData[$student->id] ?? null;
            if (!$data) return '—';
            $val = $data[$key] ?? 0;
            return '₹' . number_format($val, 2);
        }

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
            'gaam' => $student->gaam,
            'gaam_en' => $student->gaam_en,
            'is_minority' => $student->is_minority ? '✓' : '',
            'admission_under_rte' => $student->admission_under_rte ? '✓' : '',
            'is_registered' => $student->is_registered ? 'ના' : 'ના (અનબોર્ડ)',
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

    public function searchStudents(Request $request)
    {
        $search = $request->input('search', '');
        $query = Student::whereIn('status', ['active', 'alumni']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('gr_number', 'LIKE', "%{$search}%")
                  ->orWhere('full_name_gu', 'LIKE', "%{$search}%")
                  ->orWhere('full_name_en', 'LIKE', "%{$search}%");
            });
        }

        $students = $query->orderBy('full_name_gu')->limit(50)->get()->map(function ($s) {
            return [
                'id' => $s->id,
                'gr_number' => $s->gr_number,
                'full_name_gu' => $s->full_name_gu,
                'full_name_en' => $s->full_name_en,
            ];
        });

        return response()->json(['success' => true, 'students' => $students]);
    }

    public function getStudentsByFilter(Request $request)
    {
        $standardId = $request->input('standard_id');
        $classId = $request->input('class_id');

        $query = Student::whereIn('status', ['active', 'alumni'])
            ->with('currentStandard', 'currentClass');

        if (!empty($standardId)) {
            $query->where('current_standard_id', $standardId);
        }
        if (!empty($classId)) {
            $query->where('current_class_id', $classId);
        }

        if (empty($standardId) && empty($classId)) {
            return response()->json(['success' => true, 'students' => []]);
        }

        $students = $query->orderBy('gr_number')->limit(300)->get()->map(function ($s) {
            return [
                'id' => $s->id,
                'gr_number' => $s->gr_number,
                'full_name_gu' => $s->full_name_gu,
                'full_name_en' => $s->full_name_en,
            ];
        });

        return response()->json(['success' => true, 'students' => $students]);
    }
}
