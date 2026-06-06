<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Standard;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('standards')->orderBy('name')->get();
        $standards = Standard::orderBy('sort_order')->get();
        return view('subjects.index', compact('subjects', 'standards'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name',
            'code' => 'nullable|string|max:50|unique:subjects,code',
        ], [
            'name.unique' => 'આ નામનો વિષય પહેલેથી છે',
            'code.unique' => 'આ કોડ પહેલેથી વપરાયો છે',
        ]);

        $subject = Subject::create($data);

        return response()->json([
            'success' => true,
            'message' => 'વિષય ઉમેરાયો',
            'subject' => $subject->load('standards'),
        ]);
    }

    public function show(Subject $subject)
    {
        return response()->json($subject->load('standards'));
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name,' . $subject->id,
            'code' => 'nullable|string|max:50|unique:subjects,code,' . $subject->id,
        ], [
            'name.unique' => 'આ નામનો વિષય પહેલેથી છે',
            'code.unique' => 'આ કોડ પહેલેથી વપરાયો છે',
        ]);

        $subject->update($data);

        return response()->json([
            'success' => true,
            'message' => 'વિષય સુધારાયો',
            'subject' => $subject->fresh()->load('standards'),
        ]);
    }

    public function destroy(Subject $subject)
    {
        $subject->standards()->detach();
        $subject->delete();

        return response()->json([
            'success' => true,
            'message' => 'વિષય કાઢી નાખ્યો',
        ]);
    }

    public function assignStandards(Request $request, Subject $subject)
    {
        $request->validate([
            'standard_ids' => 'present|array',
            'standard_ids.*' => 'exists:standards,id',
        ]);

        $syncData = [];
        foreach ($request->standard_ids as $stdId) {
            $pivot = $subject->standards()->where('standard_id', $stdId)->first();
            $syncData[$stdId] = ['sort_order' => $pivot ? $pivot->pivot->sort_order : 0];
        }
        $subject->standards()->sync($syncData);

        return response()->json([
            'success' => true,
            'message' => 'ધોરણો સોંપાયા',
            'subject' => $subject->fresh()->load('standards'),
        ]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'standard_id' => 'required|exists:standards,id',
            'subjects' => 'required|array',
            'subjects.*.id' => 'required|exists:subjects,id',
            'subjects.*.sort_order' => 'required|integer',
        ]);

        foreach ($request->subjects as $item) {
            \DB::table('standard_subject')
                ->where('standard_id', $request->standard_id)
                ->where('subject_id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'વિષયોનો ક્રમ સચવાયો',
        ]);
    }
}
