<?php

namespace App\Http\Controllers;

use App\Exports\StudentDemoExport;
use App\Helpers\DateTextHelper;
use App\Imports\StudentsImport;
use App\Models\SchoolClass;
use App\Models\Standard;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index()
    {
        $standards = Standard::orderBy('sort_order')->get();
        $classes = SchoolClass::whereIn('standard_id', $standards->pluck('id'))->orderBy('sort_order')->get();

        $query = Student::with(['currentStandard', 'currentClass'])->where('status', 'active');
        $totalActive = (clone $query)->count();
        $totalBoys = (clone $query)->where('sharirik_jaati', 'kumar')->count();
        $totalGirls = (clone $query)->where('sharirik_jaati', 'kumari')->count();
        $students = $query->defaultSort()->paginate(20);

        return view('students.index', compact('students', 'standards', 'classes', 'totalActive', 'totalBoys', 'totalGirls'));
    }

    public function fetchData(Request $request)
    {
        $query = Student::with(['currentStandard', 'currentClass'])->where('status', 'active');

        if ($request->filled('standard_id')) {
            $query->where('current_standard_id', $request->standard_id);
        }
        if ($request->filled('class_id')) {
            $query->where('current_class_id', $request->class_id);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('gr_number', 'LIKE', "%$s%")
                  ->orWhere('student_name_gu', 'LIKE', "%$s%")
                  ->orWhere('student_name_en', 'LIKE', "%$s%")
                  ->orWhere('father_name_gu', 'LIKE', "%$s%")
                  ->orWhere('father_name_en', 'LIKE', "%$s%")
                  ->orWhere('surname_gu', 'LIKE', "%$s%")
                  ->orWhere('surname_en', 'LIKE', "%$s%")
                  ->orWhere('mobile', 'LIKE', "%$s%");
            });
        }

        $totalActive = (clone $query)->count();
        $totalBoys = (clone $query)->where('sharirik_jaati', 'kumar')->count();
        $totalGirls = (clone $query)->where('sharirik_jaati', 'kumari')->count();
        $students = $query->defaultSort()->paginate(20);

        return response()->json([
            'students' => $students->items(),
            'pagination' => [
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
            ],
            'stats' => [
                'total_boys' => $totalBoys,
                'total_girls' => $totalGirls,
                'total_active' => $totalActive,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateStudent($request);
        $data['date_of_admission'] = Carbon::createFromFormat('d/m/Y', $data['date_of_admission'])->format('Y-m-d');
        $dob = Carbon::createFromFormat('d/m/Y', $data['date_of_birth']);
        $data['date_of_birth'] = $dob->format('Y-m-d');
        $data['is_minority'] = $request->boolean('is_minority');
        $data['admission_under_rte'] = $request->boolean('admission_under_rte');
        $this->generateDobText($data, $dob);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('photos/students', 'public');
        }

        $student = Student::create($data);

        User::create([
            'name' => $data['full_name_en'],
            'username' => $data['gr_number'],
            'password' => $dob->format('d/m/Y'),
            'role' => 'student',
            'student_id' => $student->id,
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

        return response()->json([
            'success' => true,
            'message' => 'વિદ્યાર્થી ઉમેરાયો',
            'student' => $student->load(['admissionStandard', 'admissionClass', 'currentStandard', 'currentClass']),
        ]);
    }

    public function show(Student $student)
    {
        if (request()->wantsJson()) {
            $student->load(['admissionStandard', 'admissionClass', 'currentStandard', 'currentClass', 'leavingStandard']);
            $student->date_of_admission = $student->date_of_admission ? Carbon::parse($student->date_of_admission)->format('d/m/Y') : '';
            $student->date_of_birth = $student->date_of_birth ? Carbon::parse($student->date_of_birth)->format('d/m/Y') : '';
            if ($student->leaving_date) {
                $student->leaving_date = Carbon::parse($student->leaving_date)->format('d/m/Y');
            }
            return response()->json($student);
        }

        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'teacher']) && !($user->role === 'parent' && $user->student_id === $student->id)) {
            abort(403);
        }

        $student->load(['currentStandard', 'currentClass', 'admissionStandard', 'admissionClass']);

        $isBirthday = $student->date_of_birth
            && now()->format('m-d') === \Carbon\Carbon::parse($student->date_of_birth)->format('m-d');

        return view('students.profile', compact('student', 'isBirthday'));
    }

    public function update(Request $request, Student $student)
    {
        $data = $this->validateStudent($request, $student->id);
        $data['date_of_admission'] = Carbon::createFromFormat('d/m/Y', $data['date_of_admission'])->format('Y-m-d');
        $dob = Carbon::createFromFormat('d/m/Y', $data['date_of_birth']);
        $data['date_of_birth'] = $dob->format('Y-m-d');
        $data['is_minority'] = $request->boolean('is_minority');
        $data['admission_under_rte'] = $request->boolean('admission_under_rte');
        $this->generateDobText($data, $dob);

        // Handle photo upload — delete old if new uploaded
        if ($request->hasFile('photo')) {
            if ($student->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($student->photo);
            }
            $data['photo'] = $request->file('photo')->store('photos/students', 'public');
        }

        $student->update($data);

        return response()->json([
            'success' => true,
            'message' => 'વિદ્યાર્થી માહિતી સુધારાઈ',
            'student' => $student->fresh()->load(['admissionStandard', 'admissionClass', 'currentStandard', 'currentClass']),
        ]);
    }

    public function destroy(Student $student)
    {
        if ($student->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($student->photo);
        }
        User::where('student_id', $student->id)->delete();
        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'વિદ્યાર્થી કાઢી નાખ્યો',
        ]);
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

        return response()->json([
            'success' => true,
            'message' => 'શાળા છોડવાની માહિતી સાચવાઈ',
            'student' => $student->load(['admissionStandard', 'admissionClass', 'currentStandard', 'currentClass', 'leavingStandard']),
        ]);
    }

    private function validateStudent(Request $request, $ignoreId = null)
    {
        $unique = $ignoreId ? 'unique:students,gr_number,' . $ignoreId : 'unique:students,gr_number';
        return $request->validate([
            'gr_number' => 'required|numeric|' . $unique,
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
            'message' => $import->getImportedCount() . ' વિદ્યાર્થી ઉમેરાયા' . ($import->getSkippedCount() ? ', ' . $import->getSkippedCount() . ' અવગણાયા' : ''),
            'imported' => $import->getImportedCount(),
            'skipped' => $import->getSkippedCount(),
            'errors' => $import->getErrors(),
        ]);
    }

    public function importView()
    {
        $standards = \App\Models\Standard::orderBy('sort_order')->get();
        $classes = \App\Models\SchoolClass::where('status', 'active')->orderBy('sort_order')->get();
        return view('students.import', compact('standards', 'classes'));
    }
}
