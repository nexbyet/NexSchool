<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Standard;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TimetableSlot;
use App\Models\TimetableEntry;
use App\Models\SubjectTeacherAssignment;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $academicYearId = $request->get('academic_year_id', AcademicYear::getActive()?->id);

        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $activeYear = AcademicYear::find($academicYearId);

        if (!$activeYear) {
            return view('timetable.index', compact('academicYears', 'academicYearId', 'activeYear'))
                ->with('error', 'કોઈ સક્રિય શૈક્ષણિક વર્ષ નથી.');
        }

        $slots = TimetableSlot::where('academic_year_id', $academicYearId)
            ->orderBy('sort_order')->get();

        $allTeachers = Teacher::orderBy('name')->get(['id', 'name', 'teacher_id', 'status']);

        $standards = Standard::with([
            'subjects',
            'classes' => fn($q) => $q->orderBy('sort_order')
        ])->orderBy('sort_order')->get();

        $subjectAssignments = SubjectTeacherAssignment::where('academic_year_id', $academicYearId)
            ->whereNotNull('teacher_id')
            ->with('teacher:id,name,teacher_id')
            ->get()
            ->groupBy(fn($a) => $a->standard_id . '-' . ($a->class_id ?? '0') . '-' . $a->subject_id);

        $allEntries = TimetableEntry::where('academic_year_id', $academicYearId)
            ->with(['subject:id,name', 'teacher:id,name,teacher_id'])
            ->get()
            ->keyBy(fn($e) => $e->day_of_week . '-' . $e->timetable_slot_id . '-' . $e->standard_id . '-' . $e->school_class_id);

        $conflicts = [];
        foreach (range(1, 6) as $day) {
            foreach ($slots as $slot) {
                $slotKey = $day . '-' . $slot->id;
                $teacherAssignments = [];
                foreach ($allEntries as $key => $entry) {
                    if ($entry->day_of_week == $day && $entry->timetable_slot_id == $slot->id && $entry->teacher_id) {
                        $teacherAssignments[$entry->teacher_id][] = $entry->standard_id . '-' . $entry->school_class_id;
                    }
                }
                foreach ($teacherAssignments as $teacherId => $classList) {
                    if (count($classList) > 1) {
                        foreach ($classList as $cls) {
                            $conflicts[$slotKey . '-' . $cls] = true;
                        }
                    }
                }
            }
        }

        $standardSubjects = [];
        $standardSubjectsJs = [];
        foreach ($standards as $std) {
            $standardSubjects[$std->id] = $std->subjects->keyBy('id');
            foreach ($std->subjects as $subj) {
                $standardSubjectsJs[$std->id][$subj->id] = [
                    'id' => $subj->id,
                    'name' => $subj->name,
                ];
            }
        }

        $subjectAssignmentsJs = [];
        foreach ($subjectAssignments as $key => $group) {
            foreach ($group as $a) {
                $subjectAssignmentsJs[$key][] = [
                    'teacher_id' => $a->teacher_id,
                    'teacher' => $a->teacher ? ['id' => $a->teacher->id, 'name' => $a->teacher->name] : null,
                ];
            }
        }

        $days = [
            1 => 'સોમવાર', 2 => 'મંગળવાર', 3 => 'બુધવાર',
            4 => 'ગુરુવાર', 5 => 'શુક્રવાર', 6 => 'શનિવાર',
        ];
        $dayEn = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];

        $readOnly = auth()->user()->role !== 'admin';

        return view('timetable.index', compact(
            'academicYears', 'academicYearId', 'activeYear',
            'standards', 'slots', 'allTeachers', 'allEntries',
            'standardSubjects', 'standardSubjectsJs', 'subjectAssignmentsJs',
            'conflicts', 'days', 'dayEn', 'readOnly'
        ));
    }

    public function storeSlot(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'ફક્ત એડમિન જ પીરિયડ ઉમેરી શકે છે.'], 403);
        }
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name_en' => 'required|string|max:100',
            'name_gu' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
            'saturday_start_time' => 'nullable',
            'saturday_end_time' => 'nullable',
            'is_break' => 'boolean',
        ]);

        $maxSort = TimetableSlot::where('academic_year_id', $validated['academic_year_id'])
            ->max('sort_order') ?? 0;
        $validated['sort_order'] = $maxSort + 1;
        $validated['is_break'] = $request->boolean('is_break');

        $slot = TimetableSlot::create($validated);
        $slots = TimetableSlot::where('academic_year_id', $validated['academic_year_id'])
            ->orderBy('sort_order')->get();

        return response()->json(['success' => true, 'message' => 'પીરિયડ ઉમેરાયો.', 'slot' => $slot, 'slots' => $slots]);
    }

    public function updateSlot(Request $request, TimetableSlot $slot)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'ફક્ત એડમિન જ પીરિયડ સંપાદિત કરી શકે છે.'], 403);
        }
        $validated = $request->validate([
            'name_en' => 'required|string|max:100',
            'name_gu' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
            'saturday_start_time' => 'nullable',
            'saturday_end_time' => 'nullable',
            'is_break' => 'boolean',
        ]);

        $validated['is_break'] = $request->boolean('is_break');
        $slot->update($validated);

        return response()->json(['success' => true, 'message' => 'પીરિયડ અપડેટ થયો.', 'slot' => $slot]);
    }

    public function deleteSlot(TimetableSlot $slot)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'ફક્ત એડમિન જ પીરિયડ કાઢી શકે છે.'], 403);
        }
        $slot->delete();
        return response()->json(['success' => true, 'message' => 'પીરિયડ કાઢી નાખ્યો.']);
    }

    public function getClasses(Request $request)
    {
        $request->validate(['standard_id' => 'required|exists:standards,id']);
        $classes = Standard::find($request->standard_id)?->classes()->orderBy('sort_order')->get(['id', 'name']) ?? collect();
        return response()->json(['classes' => $classes]);
    }

    public function showSlot(TimetableSlot $slot)
    {
        return response()->json(['slot' => $slot]);
    }

    public function reorderSlots(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'ફક્ત એડમિન જ ક્રમ બદલી શકે છે.'], 403);
        }
        $request->validate([
            'slots' => 'required|array',
            'slots.*.id' => 'required|exists:timetable_slots,id',
            'slots.*.sort_order' => 'required|integer',
        ]);

        foreach ($request->slots as $item) {
            TimetableSlot::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'ક્રમ બદલાયો.']);
    }

    public function updateEntry(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'ફક્ત એડમિન જ ટાઇમટેબલ બદલી શકે છે.'], 403);
        }
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'timetable_slot_id' => 'required|exists:timetable_slots,id',
            'standard_id' => 'required|exists:standards,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'day_of_week' => 'required|integer|between:1,6',
            'subject_id' => 'nullable|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $entry = TimetableEntry::updateOrCreate(
            [
                'academic_year_id' => $validated['academic_year_id'],
                'timetable_slot_id' => $validated['timetable_slot_id'],
                'standard_id' => $validated['standard_id'],
                'school_class_id' => $validated['school_class_id'],
                'day_of_week' => $validated['day_of_week'],
            ],
            [
                'subject_id' => $validated['subject_id'],
                'teacher_id' => $validated['teacher_id'],
            ]
        );

        if (empty($validated['subject_id'])) {
            $entry->delete();
            return response()->json([
                'success' => true,
                'message' => 'એન્ટ્રી દૂર કરવામાં આવી.',
            ]);
        }

        $entry->load(['subject:id,name', 'teacher:id,name,teacher_id']);

        return response()->json([
            'success' => true,
            'message' => 'ટાઇમટેબલ અપડેટ થયું.',
            'entry' => $entry,
        ]);
    }

    public function copyToAllDays(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'ફક્ત એડમિન જ કોપી કરી શકે છે.'], 403);
        }
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'from_day' => 'required|integer|between:1,6',
        ]);

        $sourceEntries = TimetableEntry::where('academic_year_id', $validated['academic_year_id'])
            ->where('day_of_week', $validated['from_day'])
            ->get();

        $createdCount = 0;
        foreach (range(1, 6) as $day) {
            if ($day === $validated['from_day']) continue;
            foreach ($sourceEntries as $entry) {
                TimetableEntry::updateOrCreate(
                    [
                        'academic_year_id' => $entry->academic_year_id,
                        'timetable_slot_id' => $entry->timetable_slot_id,
                        'standard_id' => $entry->standard_id,
                        'school_class_id' => $entry->school_class_id,
                        'day_of_week' => $day,
                    ],
                    ['subject_id' => $entry->subject_id, 'teacher_id' => $entry->teacher_id,]
                );
                $createdCount++;
            }
        }

        return response()->json(['success' => true, 'message' => 'બધા દિવસોમાં કોપી થઈ ગયું. ' . $createdCount . ' એન્ટ્રી ઉમેરાઈ.']);
    }

    public function getEntries(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_id' => 'required|exists:standards,id',
            'class_id' => 'required|exists:school_classes,id',
        ]);

        $entries = TimetableEntry::where('academic_year_id', $validated['academic_year_id'])
            ->where('standard_id', $validated['standard_id'])
            ->where('school_class_id', $validated['class_id'])
            ->with(['subject:id,name', 'teacher:id,name,teacher_id'])
            ->get()
            ->keyBy(fn($e) => $e->day_of_week . '-' . $e->timetable_slot_id);

        return response()->json(['entries' => $entries]);
    }

    public function clearEntries(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'ફક્ત એડમિન જ ટાઇમટેબલ સાફ કરી શકે છે.'], 403);
        }
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_id' => 'nullable|exists:standards,id',
            'school_class_id' => 'nullable|exists:school_classes,id',
            'day_of_week' => 'nullable|integer|between:1,6',
        ]);

        $query = TimetableEntry::where('academic_year_id', $validated['academic_year_id']);

        if (!empty($validated['standard_id'])) {
            $query->where('standard_id', $validated['standard_id']);
        }
        if (!empty($validated['school_class_id'])) {
            $query->where('school_class_id', $validated['school_class_id']);
        }
        if (!empty($validated['day_of_week'])) {
            $query->where('day_of_week', $validated['day_of_week']);
        }

        $query->delete();

        return response()->json(['success' => true, 'message' => 'ટાઇમટેબલ સાફ થયું.']);
    }
}
