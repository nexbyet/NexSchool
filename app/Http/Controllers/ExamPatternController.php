<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ExamPattern;
use App\Models\ExamPatternHead;
use App\Models\SchoolClass;
use App\Models\Standard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamPatternController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $patterns = ExamPattern::with(['academicYear', 'heads'])
            ->withCount([
                'applies',
                'applies as standards_count' => function ($q) {
                    $q->select(DB::raw('COUNT(DISTINCT standard_id)'));
                },
            ])
            ->orderByDesc('id')
            ->get();

        return view('exams.patterns.index', compact('academicYears', 'patterns'));
    }

    public function create()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $standards = Standard::orderBy('sort_order')->get();

        return view('exams.patterns.form', [
            'mode' => 'create',
            'pattern' => null,
            'academicYears' => $academicYears,
            'standards' => $standards,
            'patternData' => null,
        ]);
    }

    public function edit($id)
    {
        $pattern = ExamPattern::with(['heads', 'applies.standard', 'applies.schoolClass', 'applies.subject'])->findOrFail($id);
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $standards = Standard::orderBy('sort_order')->get();

        // JS init mate: heads + applies grouped (standard -> class -> subjects)
        $grouped = [];
        foreach ($pattern->applies as $a) {
            $sid = $a->standard_id;
            $cid = $a->school_class_id;
            if (!isset($grouped[$sid])) {
                $grouped[$sid] = ['name' => $a->standard->name ?? '', 'classes' => []];
            }
            if (!isset($grouped[$sid]['classes'][$cid])) {
                $grouped[$sid]['classes'][$cid] = ['name' => $a->schoolClass->name ?? '', 'subjects' => []];
            }
            $grouped[$sid]['classes'][$cid]['subjects'][] = $a->subject_id;
        }

        return view('exams.patterns.form', [
            'mode' => 'edit',
            'pattern' => $pattern,
            'academicYears' => $academicYears,
            'standards' => $standards,
            'patternData' => ['heads' => $pattern->heads, 'grouped' => $grouped],
        ]);
    }

    // ધોરણના વર્ગો — builder mate
    public function standardClasses($standardId)
    {
        $classes = SchoolClass::where('standard_id', $standardId)->orderBy('sort_order')->get(['id', 'name']);

        return response()->json(['success' => true, 'classes' => $classes]);
    }

    // ધોરણને assign thayela subjects — builder mate
    public function standardSubjects($standardId)
    {
        $standard = Standard::findOrFail($standardId);
        $subjects = $standard->subjects()->where('subjects.status', 'active')->get(['subjects.id', 'subjects.name', 'subjects.code']);

        return response()->json(['success' => true, 'subjects' => $subjects]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        $pattern = DB::transaction(function () use ($data) {
            $pattern = ExamPattern::create([
                'academic_year_id' => $data['academic_year_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);
            $this->syncChildren($pattern, $data);
            return $pattern;
        });

        return response()->json([
            'success' => true,
            'message' => 'પરીક્ષા પેટર્ન બનાવી.',
            'pattern_id' => $pattern->id,
        ]);
    }

    public function show($id)
    {
        $pattern = ExamPattern::with(['academicYear', 'heads', 'applies.standard', 'applies.schoolClass', 'applies.subject'])->findOrFail($id);

        return response()->json(['success' => true, 'pattern' => $pattern]);
    }

    public function update(Request $request, $id)
    {
        $pattern = ExamPattern::findOrFail($id);
        $data = $this->validatePayload($request);

        DB::transaction(function () use ($pattern, $data) {
            $pattern->update([
                'academic_year_id' => $data['academic_year_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);
            $pattern->heads()->delete();
            $pattern->applies()->delete();
            $this->syncChildren($pattern, $data);
        });

        return response()->json(['success' => true, 'message' => 'પરીક્ષા પેટર્ન સુધારી.']);
    }

    // Copy pattern ek year mathi bija year ma (pachhi edit kari shakay)
    public function copy(Request $request, $id)
    {
        $pattern = ExamPattern::with(['heads', 'applies'])->findOrFail($id);
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $copy = DB::transaction(function () use ($pattern, $data) {
            $copy = ExamPattern::create([
                'academic_year_id' => $data['academic_year_id'],
                'name' => $pattern->name . ' (નકલ)',
                'description' => $pattern->description,
                'is_active' => $pattern->is_active,
            ]);
            foreach ($pattern->heads as $h) {
                ExamPatternHead::create([
                    'exam_pattern_id' => $copy->id,
                    'semester' => $h->semester,
                    'name' => $h->name,
                    'total_marks' => $h->total_marks,
                    'converted_marks' => $h->converted_marks,
                    'sort_order' => $h->sort_order,
                ]);
            }
            foreach ($pattern->applies as $a) {
                $copy->applies()->create([
                    'standard_id' => $a->standard_id,
                    'school_class_id' => $a->school_class_id,
                    'subject_id' => $a->subject_id,
                ]);
            }
            return $copy;
        });

        return response()->json([
            'success' => true,
            'message' => 'પેટર્નની નકલ બની — હવે સુધારો.',
            'pattern_id' => $copy->id,
        ]);
    }

    public function destroy($id)
    {
        $pattern = ExamPattern::findOrFail($id);
        $name = $pattern->name;
        $pattern->delete();

        return response()->json([
            'success' => true,
            'message' => "પરીક્ષા પેટર્ન \"{$name}\" કાઢી નાખી.",
        ]);
    }

    private function validatePayload(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'heads' => 'required|array|min:1',
            'heads.*.semester' => 'required|in:1,2',
            'heads.*.name' => 'required|string|max:255',
            'heads.*.total_marks' => 'required|numeric|min:0',
            'heads.*.converted_marks' => 'required|numeric|min:0',
            'applies' => 'required|array|min:1',
            'applies.*.standard_id' => 'required|exists:standards,id',
            'applies.*.school_class_id' => 'required|exists:school_classes,id',
            'applies.*.subject_id' => 'required|exists:subjects,id',
        ]);

        foreach ([1, 2] as $sem) {
            $count = collect($data['heads'])->where('semester', $sem)->count();
            if ($count === 0) {
                abort(response()->json(['success' => false, 'message' => "સત્ર {$sem} માં ઓછામાં ઓછો 1 માર્ક્સ હેડ જરૂરી છે."], 422));
            }
        }

        // વર્ગ એ ધોરણનો જ + વિષય એ ધોરણને assign થયેલો જ હોવો જોઈએ
        foreach ($data['applies'] as $row) {
            $classOk = SchoolClass::where('id', $row['school_class_id'])
                ->where('standard_id', $row['standard_id'])
                ->exists();
            if (!$classOk) {
                abort(response()->json(['success' => false, 'message' => 'કોઈક વર્ગ એ ધોરણનો નથી.'], 422));
            }
            $subOk = DB::table('standard_subject')
                ->where('standard_id', $row['standard_id'])
                ->where('subject_id', $row['subject_id'])
                ->exists();
            if (!$subOk) {
                abort(response()->json(['success' => false, 'message' => 'કોઈક વિષય એ ધોરણને assign થયેલો નથી.'], 422));
            }
        }

        return $data;
    }

    private function syncChildren(ExamPattern $pattern, array $data)
    {
        $order = [1 => 0, 2 => 0];
        foreach ($data['heads'] as $head) {
            $sem = (int) $head['semester'];
            $order[$sem]++;
            ExamPatternHead::create([
                'exam_pattern_id' => $pattern->id,
                'semester' => $sem,
                'name' => $head['name'],
                'total_marks' => $head['total_marks'],
                'converted_marks' => $head['converted_marks'],
                'sort_order' => $order[$sem],
            ]);
        }

        $seen = [];
        foreach ($data['applies'] as $row) {
            $key = $row['standard_id'] . '-' . $row['school_class_id'] . '-' . $row['subject_id'];
            if (isset($seen[$key])) continue;
            $seen[$key] = true;
            $pattern->applies()->create([
                'standard_id' => $row['standard_id'],
                'school_class_id' => $row['school_class_id'],
                'subject_id' => $row['subject_id'],
            ]);
        }
    }
}
