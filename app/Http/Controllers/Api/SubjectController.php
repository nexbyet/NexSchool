<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        return response()->json(Subject::with('standards')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name',
            'code' => 'nullable|string|max:50|unique:subjects,code',
            'description' => 'nullable|string',
            'credit_hours' => 'nullable|integer|min:1',
            'pass_mark' => 'nullable|integer|min:0',
            'total_mark' => 'nullable|integer|min:0',
            'status' => 'nullable|in:active,inactive',
        ]);

        $subject = Subject::create($validated);
        return response()->json($subject->load('standards'), 201);
    }

    public function show(Subject $subject)
    {
        return response()->json($subject->load('standards'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:subjects,name,' . $subject->id,
            'code' => 'nullable|string|max:50|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string',
            'credit_hours' => 'nullable|integer|min:1',
            'pass_mark' => 'nullable|integer|min:0',
            'total_mark' => 'nullable|integer|min:0',
            'status' => 'nullable|in:active,inactive',
        ]);

        $subject->update($validated);
        return response()->json($subject->load('standards'));
    }

    public function destroy(Subject $subject)
    {
        $subject->standards()->detach();
        $subject->delete();
        return response()->json(null, 204);
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
            'message' => 'Standards assigned',
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

        return response()->json(['message' => 'Subjects order updated']);
    }
}
