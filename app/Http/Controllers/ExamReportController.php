<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ExamMark;
use App\Models\ExamPattern;
use App\Models\ExamPatternApply;
use App\Models\GradeScale;
use App\Models\Standard;
use App\Models\Student;
use Illuminate\Http\Request;

class ExamReportController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $standards = Standard::orderBy('sort_order')->get();

        return view('exams.reports.index', compact('academicYears', 'standards'));
    }

    public function report(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_id' => 'required|exists:standards,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'semester' => 'required|in:1,2,both',
        ]);

        return response()->json(['success' => true] + $this->buildReport($data));
    }

    public function print(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_id' => 'required|exists:standards,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'semester' => 'required|in:1,2,both',
        ]);

        $report = $this->buildReport($data);
        $school = \App\Models\SchoolSetting::find(1);

        return view('exams.reports.print', array_merge($report, [
            'school' => $school,
            'filters' => $data,
        ]));
    }

    // Combined report: head-wise columns (subject groups) + subject totals + overall.
    // semester = 1 | 2 | both (banne = annual)
    public function buildReport(array $data)
    {
        $sems = $data['semester'] === 'both' ? [1, 2] : [(int) $data['semester']];

        // અનબોર્ડ (is_registered = false) વિદ્યાર્થીઓ રિપોર્ટમાં ન આવે — એ શાળાના નોંધાયેલા વિદ્યાર્થી નથી
        $students = Student::where('current_standard_id', $data['standard_id'])
            ->where('current_class_id', $data['school_class_id'])
            ->where('is_registered', true)
            ->where('status', '!=', 'alumni')
            ->defaultSort()
            ->get(['id', 'gr_number', 'full_name_gu', 'full_name_en']);
        $studentIds = $students->pluck('id');

        // std+class ne lagtu applies (year na patterns purtu)
        $applies = ExamPatternApply::with(['subject', 'pattern'])
            ->where('standard_id', $data['standard_id'])
            ->where('school_class_id', $data['school_class_id'])
            ->whereHas('pattern', function ($q) use ($data) {
                $q->where('academic_year_id', $data['academic_year_id']);
            })
            ->get();

        // vishay yadi (standard_subject sort mujab) + darek vishay mate pattern (active pahela)
        $subjectOrder = \DB::table('standard_subject')
            ->where('standard_id', $data['standard_id'])
            ->orderBy('sort_order')
            ->pluck('subject_id')
            ->all();
        $bySubject = [];
        foreach ($applies as $a) {
            $sid = $a->subject_id;
            if (!isset($bySubject[$sid])) {
                $bySubject[$sid] = ['subject' => $a->subject, 'patterns' => []];
            }
            $bySubject[$sid]['patterns'][] = $a->pattern;
        }
        // કઈ pattern હેઠળ માર્ક્સ ભરાયા છે — report એ એ જ pattern વાપરવો જોઈએ.
        // નહીં તો active pattern બદલાયા પછી જૂના marks report માં ખાલી દેખાય
        // અને overall ખોટો નાપાસ આવે.
        $markPatterns = ExamMark::whereIn('student_id', $studentIds)
            ->where('academic_year_id', $data['academic_year_id'])
            ->whereIn('semester', $sems)
            ->select('subject_id', 'exam_pattern_id', \DB::raw('COUNT(*) as cnt'))
            ->groupBy('subject_id', 'exam_pattern_id')
            ->orderByDesc('cnt')
            ->get()
            ->groupBy('subject_id');

        $pickPattern = function ($sid, array $patterns) use ($markPatterns) {
            $pats = collect($patterns);
            $active = $pats->firstWhere('is_active', true);
            $used = $markPatterns->get($sid, collect());

            // સૌથી વધુ marks ધરાવતો pattern; સરખા count હોય તો active પસંદ.
            $top = $used->sortByDesc('cnt')->first();
            if ($top) {
                $match = $pats->firstWhere('id', $top->exam_pattern_id);
                if ($match) {
                    $activeUsed = $active ? $used->firstWhere('exam_pattern_id', $active->id) : null;
                    if ($activeUsed && (int) $activeUsed->cnt >= (int) $top->cnt) {
                        return $active;
                    }
                    return $match;
                }
            }

            return $active ?: $pats->sortByDesc('id')->first();
        };

        $subjects = [];
        foreach ($subjectOrder as $sid) {
            if (!isset($bySubject[$sid])) continue;
            $subjects[] = ['subject' => $bySubject[$sid]['subject'], 'pattern' => $pickPattern($sid, $bySubject[$sid]['patterns'])];
        }
        foreach ($bySubject as $sid => $row) {
            $found = false;
            foreach ($subjects as $s) {
                if ($s['subject']->id == $sid) { $found = true; break; }
            }
            if ($found) continue;
            $subjects[] = ['subject' => $row['subject'], 'pattern' => $pickPattern($sid, $row['patterns'])];
        }

        // Head-wise columns: darek vishay na pasand semester(s) na heads
        $headCols = []; // [subject_id, subject_name, semester, head_id, head_name, total, converted]
        foreach ($subjects as $sub) {
            /** @var ExamPattern|null $pat */
            $pat = $sub['pattern'];
            if (!$pat) continue;
            foreach ($sems as $sem) {
                foreach ($pat->heads->where('semester', $sem)->values() as $h) {
                    $headCols[] = [
                        'subject_id' => $sub['subject']->id,
                        'subject_name' => $sub['subject']->name ?? '',
                        'semester' => $sem,
                        'head_id' => $h->id,
                        'head_name' => $h->name,
                        'total' => (float) $h->total_marks,
                        'converted' => (float) $h->converted_marks,
                        'pattern_id' => $pat->id,
                    ];
                }
            }
        }

        $scale = GradeScale::activeForYear($data['academic_year_id']);

        // ટકા/ગ્રેડ ફક્ત એ વિષયો પરથી ગણાય જેમાં વિદ્યાર્થીના માર્ક્સ ભરેલા છે.
        // ખાલી (બાકી) વિષય overall માં 0 તરીકે ન ગણાય — નહીં તો બધા ભરેલા
        // વિષયો A/B/C/D હોવા છતાં overall E (fail) આવે.

        $allMarks = ExamMark::whereIn('student_id', $studentIds)
            ->where('academic_year_id', $data['academic_year_id'])
            ->whereIn('semester', $sems)
            ->get()
            ->groupBy('student_id');

        $convOf = function ($got, $headTotal, $headConv) {
            return ((float) $headTotal > 0) ? (float) $got * (float) $headConv / (float) $headTotal : 0;
        };

        $rows = [];
        foreach ($students as $st) {
            $stuMarks = isset($allMarks[$st->id]) ? $allMarks[$st->id] : collect();

            // head-wise cells (headCols order mujab) — GANATARI (converted) marks
            $cells = [];
            foreach ($headCols as $hc) {
                $m = $stuMarks->first(function ($x) use ($hc) {
                    return $x->head_id == $hc['head_id']
                        && $x->subject_id == $hc['subject_id']
                        && $x->exam_pattern_id == $hc['pattern_id'];
                });
                $cells[] = $m ? round($convOf((float) $m->marks_obtained, $hc['total'], $hc['converted']), 2) : null;
            }

            // subject-wise subtotals (pasand sems combined)
            $subs = [];
            $obtained = 0;
            $hasAny = false;
            $hasAll = true; // pattern ના બધા વિષયોમાં marks છે કે નહીં
            $allPass = true;
            $gradedTotal = 0; // ફક્ત ભરેલા વિષયોનો કુલ (overall % માટે)
            foreach ($subjects as $sub) {
                $subTotal = 0;
                $subObt = 0;
                $entered = 0;
                foreach ($headCols as $hi => $hc) {
                    if ($hc['subject_id'] != $sub['subject']->id) continue;
                    $subTotal += $hc['converted'];
                    if ($cells[$hi] !== null) {
                        $entered++;
                        $subObt += $cells[$hi]; // cells paheleti converted che
                    }
                }
                if ($entered === 0) {
                    $subs[] = null; // bilkul entry nahi — blank (0 nahi)
                    $hasAll = false;
                    continue;
                }
                $hasAny = true;
                $gradedTotal += $subTotal;
                $pct = $subTotal > 0 ? $subObt / $subTotal * 100 : 0;
                $slab = $scale ? $scale->gradeFor($pct) : null;
                $pass = $slab ? (bool) $slab->is_pass : true;
                if (!$pass) $allPass = false;
                $obtained += $subObt;
                $subs[] = [
                    'obtained' => round($subObt, 2),
                    'total' => round($subTotal, 2),
                    'grade' => $slab ? $slab->grade : null,
                    'pass' => $pass,
                ];
            }

            $percent = null;
            $grade = null;
            $pass = false;
            if ($hasAny && $gradedTotal > 0) {
                $percent = round($obtained / $gradedTotal * 100, 2);
                // Integer-style grade scale (0–34, 35–49...) — percentage nearest
                // integer par round kariye, jethe 34.5% → 35% (D, pass) ganay.
                if ($scale && $scale->isIntegerStyle()) {
                    $percent = round($percent);
                }
                $slab = $scale ? $scale->gradeFor($percent) : null;
                $grade = $slab ? $slab->grade : null;
                // કોઈ પણ ભરેલા વિષયમાં E (fail) હોય તો આખું પરિણામ નાપાસ.
                // બાકી (ખાલી) વિષયો overall માં ગણાતા નથી.
                // pattern ના બધા વિષયો ભરાયા ન હોય તો pass/fail નહીં — બાકી.
                $pass = $hasAll && $allPass && (!$slab || (bool) $slab->is_pass);
            }

            $rows[] = [
                'id' => $st->id,
                'gr_number' => $st->gr_number,
                'name' => $st->full_name_gu ?: $st->full_name_en,
                'cells' => $cells,
                'subs' => $subs,
                'total_marks' => round($gradedTotal, 2),
                'obtained' => round($obtained, 2),
                'percent' => $percent,
                'grade' => $grade,
                'pass' => $pass,
                'has_any' => $hasAny,
                'has_all' => $hasAll,
                'rank' => null,
            ];
        }

        // Rank: fakt pass vidyarthio, percent mujab (sarkha % = same rank).
        // Dense ranking: 1,2,3,3,4 (Excel-style 1,2,3,3,5 na badle).
        $passRows = array_filter($rows, function ($r) { return $r['pass'] && $r['percent'] !== null; });
        usort($passRows, function ($a, $b) { return $b['percent'] <=> $a['percent']; });
        $rank = 0;
        $prevPct = null;
        foreach ($passRows as $pr) {
            if ($prevPct === null || $pr['percent'] < $prevPct) {
                $rank++; // dense rank: next distinct percent gets +1
            }
            $prevPct = $pr['percent'];
            foreach ($rows as &$r) {
                if ($r['id'] === $pr['id']) { $r['rank'] = $rank; break; }
            }
        }
        unset($r);

        return [
            'semester_label' => $data['semester'] === 'both' ? 'બંને (વાર્ષિક)' : ('સત્ર ' . $data['semester']),
            'subjects' => array_map(function ($s) {
                return ['id' => $s['subject']->id, 'name' => $s['subject']->name ?? ''];
            }, $subjects),
            'head_cols' => array_map(function ($hc) {
                unset($hc['pattern_id']);
                return $hc;
            }, $headCols),
            'rows' => $rows,
            'has_scale' => (bool) $scale,
            'scale_name' => $scale ? $scale->name : null,
        ];
    }
}
