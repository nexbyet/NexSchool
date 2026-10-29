<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ResultPublication;
use App\Models\SchoolClass;
use App\Models\Standard;
use Illuminate\Http\Request;

class ResultPublicationController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $standards = Standard::orderBy('sort_order')->get();
        $publications = ResultPublication::with(['academicYear', 'standard', 'schoolClass'])
            ->orderByDesc('published_at')
            ->get();

        return view('exams.publish.index', compact('academicYears', 'standards', 'publications'));
    }

    public function classes(Standard $standard)
    {
        return response()->json($standard->classes()->orderBy('name')->get(['id', 'name']));
    }

    public function publish(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_id' => 'required',
            'school_class_id' => 'required',
            'semester' => 'required|in:1,2,both',
        ]);

        $pairs = $this->resolvePairs($data['standard_id'], $data['school_class_id']);

        foreach ($pairs as [$stdId, $classId]) {
            ResultPublication::updateOrCreate(
                [
                    'academic_year_id' => $data['academic_year_id'],
                    'standard_id' => $stdId,
                    'school_class_id' => $classId,
                    'semester' => $data['semester'],
                ],
                ['published_at' => now()]
            );
        }

        return response()->json(['success' => true, 'message' => count($pairs) . ' result(s) published.']);
    }

    public function unpublish(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'standard_id' => 'required',
            'school_class_id' => 'required',
            'semester' => 'required|in:1,2,both',
        ]);

        $pairs = $this->resolvePairs($data['standard_id'], $data['school_class_id']);

        $deleted = 0;
        foreach ($pairs as [$stdId, $classId]) {
            $deleted += ResultPublication::where('academic_year_id', $data['academic_year_id'])
                ->where('standard_id', $stdId)
                ->where('school_class_id', $classId)
                ->where('semester', $data['semester'])
                ->delete();
        }

        return response()->json(['success' => true, 'message' => $deleted . ' publication(s) removed.']);
    }

    private function resolvePairs($standardId, $classId): array
    {
        // standard_id / school_class_id can be 'all', a single id, or a comma-separated list.
        $standardIds = $standardId === 'all'
            ? Standard::orderBy('sort_order')->pluck('id')->all()
            : (is_array($standardId) ? $standardId : explode(',', (string) $standardId));

        $pairs = [];
        foreach ($standardIds as $sid) {
            $sid = (int) $sid;
            if ($sid <= 0) continue;

            $classIds = $classId === 'all'
                ? SchoolClass::where('standard_id', $sid)->orderBy('name')->pluck('id')->all()
                : (is_array($classId) ? $classId : explode(',', (string) $classId));

            foreach ($classIds as $cid) {
                $cid = (int) $cid;
                if ($cid <= 0) continue;
                $pairs[] = [$sid, $cid];
            }
        }

        // de-duplicate
        $unique = [];
        foreach ($pairs as $p) {
            $key = $p[0] . '-' . $p[1];
            $unique[$key] = $p;
        }
        return array_values($unique);
    }
}