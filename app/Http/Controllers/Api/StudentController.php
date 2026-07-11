<?php

namespace App\Http\Controllers\Api;

use App\Exports\StudentDemoExport;
use App\Helpers\DateTextHelper;
use App\Http\Controllers\Controller;
use App\Imports\StudentsImport;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index()
    {
        return response()->json(
            Student::with(['admissionStandard', 'admissionClass', 'currentStandard', 'currentClass'])->defaultSort()->get()
        );
    }

    protected function generateUrNumber(): string
    {
        $maxUr = Student::where('gr_number', 'LIKE', 'UR-%')
            ->orderByRaw('LENGTH(gr_number) DESC, gr_number DESC')
            ->value('gr_number');
        $num = $maxUr ? (int) substr($maxUr, 3) + 1 : 1;
        return 'UR-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $data = $this->validateStudent($request);
        $data['date_of_admission'] = Carbon::createFromFormat('d/m/Y', $data['date_of_admission'])->format('Y-m-d');
        $dob = Carbon::createFromFormat('d/m/Y', $data['date_of_birth']);
        $data['date_of_birth'] = $dob->format('Y-m-d');
        $data['is_registered'] = $request->boolean('is_registered');
        $this->generateDobText($data, $dob);

        if (!$data['is_registered']) {
            $data['gr_number'] = $this->generateUrNumber();
        }

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('photos/students', 'public');
        }

        $student = Student::create($data);

        $student->user()->create([
            'name' => $data['full_name_en'],
            'username' => $data['gr_number'],
            'password' => $dob->format('d/m/Y'),
            'role' => 'student',
        ]);

        if (!empty($data['mobile']) && !empty($data['father_name_en'])) {
            User::firstOrCreate(
                ['username' => $data['mobile'], 'role' => 'parent'],
                [
                    'name' => $data['father_name_en'],
                    'password' => 'Parent@123',
                    'parent_mobile' => $data['mobile'],
                ]
            );
        }

        return response()->json($student->load(['admissionStandard', 'admissionClass', 'currentStandard', 'currentClass']), 201);
    }

    public function show(Student $student)
    {
        $student->load(['admissionStandard', 'admissionClass', 'currentStandard', 'currentClass', 'leavingStandard']);
        $student->date_of_admission = $student->date_of_admission ? Carbon::parse($student->date_of_admission)->format('d/m/Y') : '';
        $student->date_of_birth = $student->date_of_birth ? Carbon::parse($student->date_of_birth)->format('d/m/Y') : '';
        if ($student->leaving_date) {
            $student->leaving_date = Carbon::parse($student->leaving_date)->format('d/m/Y');
        }
        return response()->json($student);
    }

    public function update(Request $request, Student $student)
    {
        $data = $this->validateStudent($request, $student->id);
        $data['date_of_admission'] = Carbon::createFromFormat('d/m/Y', $data['date_of_admission'])->format('Y-m-d');
        $dob = Carbon::createFromFormat('d/m/Y', $data['date_of_birth']);
        $data['date_of_birth'] = $dob->format('Y-m-d');
        $data['is_registered'] = $request->boolean('is_registered');
        $this->generateDobText($data, $dob);

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($student->photo);
            }
            $data['photo'] = $request->file('photo')->store('photos/students', 'public');
        }

        if ($data['is_registered'] && str_starts_with($student->gr_number, 'UR-')) {
            $data['gr_number'] = $request->input('gr_number');
        }

        $student->update($data);

        if ($student->user) {
            $student->user->update(['username' => $data['gr_number'] ?? $student->gr_number]);
        }

        return response()->json($student->load(['admissionStandard', 'admissionClass', 'currentStandard', 'currentClass']));
    }

    public function destroy(Student $student)
    {
        $student->user()->delete();
        if ($student->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($student->photo);
        }
        $student->delete();
        return response()->json(null, 204);
    }

    public function updateLeaving(Request $request, Student $student)
    {
        $data = $request->validate([
            'leaving_reason_gu' => 'nullable|string',
            'leaving_reason_en' => 'nullable|string',
            'leaving_date' => 'nullable|date_format:d/m/Y',
            'leaving_standard_id' => 'nullable|exists:standards,id',
            'lc_number' => 'nullable|string|max:50',
            'leaving_remarks' => 'nullable|string',
        ]);

        if (!empty($data['leaving_date'])) {
            $data['leaving_date'] = Carbon::createFromFormat('d/m/Y', $data['leaving_date'])->format('Y-m-d');
        }
        $data['status'] = 'alumni';

        $student->update($data);

        return response()->json($student->load(['admissionStandard', 'admissionClass', 'currentStandard', 'currentClass', 'leavingStandard']));
    }

    private function validateStudent(Request $request, $ignoreId = null)
    {
        $isRegistered = $request->boolean('is_registered');
        $unique = $ignoreId ? 'unique:students,gr_number,' . $ignoreId : 'unique:students,gr_number';
        return $request->validate([
            'gr_number' => $isRegistered ? 'required|numeric|' . $unique : 'nullable|string',
            'admission_standard_id' => 'required|exists:standards,id',
            'admission_class_id' => 'nullable|exists:school_classes,id',
            'current_standard_id' => 'required|exists:standards,id',
            'current_class_id' => 'nullable|exists:school_classes,id',
            'date_of_admission' => 'required|date_format:d/m/Y',
            'student_name_gu' => 'required|string|max:255',
            'student_name_en' => 'required|string|max:255',
            'father_name_gu' => 'required|string|max:255',
            'father_name_en' => 'required|string|max:255',
            'surname_gu' => 'required|string|max:255',
            'surname_en' => 'required|string|max:255',
            'full_name_gu' => 'required|string',
            'full_name_en' => 'required|string',
            'mother_name_gu' => 'nullable|string|max:255',
            'mother_name_en' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date_format:d/m/Y',
            'dob_in_text_gu' => 'nullable|string',
            'dob_in_text_en' => 'nullable|string',
            'birth_place_gu' => 'nullable|string|max:255',
            'birth_place_en' => 'nullable|string|max:255',
            'native_place_gu' => 'nullable|string|max:255',
            'native_place_en' => 'nullable|string|max:255',
            'gaam' => 'nullable|string|max:255',
            'gaam_en' => 'nullable|string|max:255',
            'religion_gu' => 'nullable|in:હિન્દુ,મુસ્લિમ,શીખ,બૌદ્ધ,ઈસાઈ,પારસી',
            'religion_en' => 'nullable|in:Hindu,Muslim,Sikh,Buddhist,Christian,Parsi',
            'cast_gu' => 'nullable|string|max:255',
            'cast_en' => 'nullable|string|max:255',
            'category_gu' => 'nullable|in:સામાન્ય,અનુસુચિત જાતિ,અનુસુચિત જન જાતિ,બક્ષીપંચ,આર્થિક પછાત',
            'category_en' => 'nullable|in:General,SC,ST,OBC,EWS',
            'is_minority' => 'nullable|boolean',
            'sharirik_jaati' => 'nullable|in:kumar,kumari',
            'last_school_gu' => 'nullable|string|max:255',
            'last_school_en' => 'nullable|string|max:255',
            'admission_under_rte' => 'nullable|boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'mobile' => 'nullable|digits:10',
            'whatsapp' => 'nullable|digits:10',
            'apaar_id' => 'nullable|digits:12',
            'uid_no' => 'nullable|digits:18',
            'pen_no' => 'nullable|digits:11',
            'aadhar_no' => 'nullable|digits:12',
            'name_as_per_aadhar' => 'nullable|string|max:255',
            'ration_card_no' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'bank_ifsc' => 'nullable|string|max:20',
            'bank_account_no' => 'nullable|string|max:30',
            'name_as_per_bank' => 'nullable|string|max:255',
        ]);
    }

    private function generateDobText(array &$data, Carbon $dob)
    {
        $data['dob_in_text_gu'] = DateTextHelper::gujaratiDateText($dob->day, $dob->month, $dob->year);
        $data['dob_in_text_en'] = DateTextHelper::englishDateText($dob->day, $dob->month, $dob->year);
    }

    public function importDemo()
    {
        return Excel::download(new StudentDemoExport, 'nexschool_student_demo.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $import = new StudentsImport();
        Excel::import($import, $request->file('file'));

        return response()->json([
            'success' => true,
            'message' => $import->getImportedCount() . ' students imported' . ($import->getSkippedCount() ? ', ' . $import->getSkippedCount() . ' skipped' : ''),
            'imported' => $import->getImportedCount(),
            'skipped' => $import->getSkippedCount(),
            'errors' => $import->getErrors(),
        ]);
    }
}
