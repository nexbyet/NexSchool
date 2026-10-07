<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ExamMark;
use App\Models\ExamPattern;
use App\Models\ExamPatternApply;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamMarkController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();

        return view('exams.marks.index', compact('academicYears'));
    }

    // Varsh ma pattern laghu hoy e j dhoran/varg/vishay tree — dead-end vagar cascade mate.
    // Dareke vishay sathe eni pattern(s) pan aave (1 hoy to auto, 1+ hoy to user picks).
    public function scope($yearId)
    {
        $rows = ExamPatternApply::with(['standard', 'schoolClass', 'subject', 'pattern'])
            ->whereHas('pattern', function ($q) use ($yearId) {
                $q->where('academic_year_id', $yearId);
            })
            ->get();

        $tree = [];
        foreach ($rows as $a) {
            $sid = $a->standard_id;
            $cid = $a->school_class_id;
            $subid = $a->subject_id;
            if (!isset($tree[$sid])) {
                $tree[$sid] = [
                    'id' => $sid,
                    'name' => $a->standard->name ?? '',
                    'sort' => $a->standard->sort_order ?? 0,
                    'classes' => [],
                ];
            }
            if (!isset($tree[$sid]['classes'][$cid])) {
                $tree[$sid]['classes'][$cid] = [
                    'id' => $cid,
                    'name' => $a->schoolClass->name ?? '',
                    'sort' => $a->schoolClass->sort_order ?? 0,
                    'subjects' => [],
                ];
            }
            if (!isset($tree[$sid]['classes'][$cid]['subjects'][$subid])) {
                $tree[$sid]['classes'][$cid]['subjects'][$subid] = [
                    'id' => $subid,
                    'name' => $a->subject->name ?? '',
                    'patterns' => [],
                ];
            }
            $tree[$sid]['classes'][$cid]['subjects'][$subid]['patterns'][] = [
                'id' => $a->exam_pattern_id,
                'name' => $a->pattern->name ?? '',
            ];
        }

        $standards = array_values($tree);
        usort($standards, function ($a, $b) { return ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0); });
        foreach ($standards as &$std) {
            $std['classes'] = array_values($std['classes']);
            usort($std['classes'], function ($a, $b) { return ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0); });
            foreach ($std['classes'] as &$cls) {
                $cls['subjects'] = array_values($cls['subjects']);
            }
        }

        return response()->json(['success' => true, 'standards' => $standards]);
    }

    // Grid data: varg na students + saved marks map
    public function grid(Request $request)
    {
        $data = $request->validate([
            'pattern_id' => 'required|exists:exam_patterns,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_id' => 'required|exists:standards,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'semester' => 'required|in:1,2',
        ]);

        $pattern = ExamPattern::with('heads')->findOrFail($data['pattern_id']);
        $heads = $pattern->heads->where('semester', (int) $data['semester'])->values();
        if ($heads->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'આ સત્રમાં કોઈ હેડ નથી.'], 422);
        }

        $students = Student::where('current_standard_id', $data['standard_id'])
            ->where('current_class_id', $data['school_class_id'])
            ->where('status', '!=', 'alumni')
            ->defaultSort()
            ->get(['id', 'gr_number', 'full_name_gu', 'full_name_en']);

        $marks = ExamMark::where('exam_pattern_id', $data['pattern_id'])
            ->where('academic_year_id', $data['academic_year_id'])
            ->where('standard_id', $data['standard_id'])
            ->where('school_class_id', $data['school_class_id'])
            ->where('subject_id', $data['subject_id'])
            ->where('semester', $data['semester'])
            ->get();

        $map = [];
        foreach ($marks as $m) {
            $map[$m->student_id][$m->head_id] = (float) $m->marks_obtained;
        }

        return response()->json([
            'success' => true,
            'pattern' => ['id' => $pattern->id, 'name' => $pattern->name],
            'students' => $students->map(function ($s) {
                return [
                    'id' => $s->id,
                    'gr_number' => $s->gr_number,
                    'name' => $s->full_name_gu ?: $s->full_name_en,
                ];
            })->values(),
            'heads' => $heads->map(function ($h) {
                return [
                    'id' => $h->id,
                    'name' => $h->name,
                    'total_marks' => (float) $h->total_marks,
                    'converted_marks' => (float) $h->converted_marks,
                ];
            })->values(),
            'marks' => $map,
        ]);
    }

    // Bulk save: number = upsert, null/khali = row delete (not entered)
    public function save(Request $request)
    {
        $data = $request->validate([
            'pattern_id' => 'required|exists:exam_patterns,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_id' => 'required|exists:standards,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'semester' => 'required|in:1,2',
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.head_id' => 'required|exists:exam_pattern_heads,id',
            'marks.*.marks' => 'nullable|numeric|min:0',
        ]);

        $pattern = ExamPattern::with('heads')->findOrFail($data['pattern_id']);
        $validHeads = $pattern->heads->where('semester', (int) $data['semester'])->keyBy('id');

        $saved = 0;
        $cleared = 0;

        DB::transaction(function () use ($data, $validHeads, &$saved, &$cleared) {
            foreach ($data['marks'] as $row) {
                $head = $validHeads->get($row['head_id']);
                if (!$head) continue; // biju sem/head — skip (safety)
                if ($row['marks'] === null || $row['marks'] === '') {
                    $deleted = ExamMark::where('exam_pattern_id', $data['pattern_id'])
                        ->where('student_id', $row['student_id'])
                        ->where('subject_id', $data['subject_id'])
                        ->where('semester', $data['semester'])
                        ->where('head_id', $row['head_id'])
                        ->delete();
                    $cleared += $deleted;
                    continue;
                }
                $marks = (float) $row['marks'];
                if ($marks < 0 || $marks > (float) $head->total_marks) continue; // had bahar — skip (UI pan roke che)
                ExamMark::updateOrCreate(
                    [
                        'exam_pattern_id' => $data['pattern_id'],
                        'student_id' => $row['student_id'],
                        'subject_id' => $data['subject_id'],
                        'semester' => $data['semester'],
                        'head_id' => $row['head_id'],
                    ],
                    [
                        'academic_year_id' => $data['academic_year_id'],
                        'standard_id' => $data['standard_id'],
                        'school_class_id' => $data['school_class_id'],
                        'marks_obtained' => $marks,
                    ]
                );
                $saved++;
            }
        });

        $message = "{$saved} ગુણ સાચવ્યા.";
        if ($cleared > 0) {
            $message .= " {$cleared} ખાલી કરેલા કાઢ્યા.";
        }

        return response()->json(['success' => true, 'message' => $message, 'saved' => $saved, 'cleared' => $cleared]);
    }
}
