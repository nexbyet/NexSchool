<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Standard;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class StandardController extends Controller
{
    public function index()
    {
        $standards = Standard::with('classes')->orderBy('sort_order')->get();
        return response()->json($standards);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:standards,name',
        ]);

        $maxSort = Standard::max('sort_order') ?? 0;
        $data['sort_order'] = $maxSort + 1;

        $standard = Standard::create($data);

        return response()->json([
            'message' => 'Standard created',
            'standard' => $standard->load('classes'),
        ], 201);
    }

    public function show(Standard $standard)
    {
        return response()->json($standard->load('classes'));
    }

    public function update(Request $request, Standard $standard)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:standards,name,' . $standard->id,
        ]);

        $standard->update($data);

        return response()->json([
            'message' => 'Standard updated',
            'standard' => $standard->fresh()->load('classes'),
        ]);
    }

    public function destroy(Standard $standard)
    {
        $standard->delete();
        return response()->json(['message' => 'Standard deleted']);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'standards' => 'required|array',
            'standards.*.id' => 'required|exists:standards,id',
            'standards.*.sort_order' => 'required|integer',
        ]);

        foreach ($request->standards as $item) {
            Standard::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['message' => 'Order updated']);
    }

    // Class (Section) sub-resource

    public function storeClass(Request $request, Standard $standard)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:school_classes,name,NULL,id,standard_id,' . $standard->id,
        ]);

        $maxSort = SchoolClass::where('standard_id', $standard->id)->max('sort_order') ?? 0;
        $data['sort_order'] = $maxSort + 1;
        $data['standard_id'] = $standard->id;

        $cls = SchoolClass::create($data);

        return response()->json([
            'message' => 'Class created',
            'class' => $cls,
        ], 201);
    }

    public function updateClass(Request $request, $classId)
    {
        $cls = SchoolClass::findOrFail($classId);
        $data = $request->validate(['name' => 'required|string|max:255|unique:school_classes,name,' . $classId . ',id,standard_id,' . $cls->standard_id]);
        $cls->update($data);

        return response()->json([
            'message' => 'Class updated',
            'class' => $cls,
        ]);
    }

    public function destroyClass($classId)
    {
        SchoolClass::findOrFail($classId)->delete();
        return response()->json(['message' => 'Class deleted']);
    }

    public function reorderClasses(Request $request)
    {
        $request->validate([
            'classes' => 'required|array',
            'classes.*.id' => 'required|exists:school_classes,id',
            'classes.*.sort_order' => 'required|integer',
        ]);

        foreach ($request->classes as $item) {
            SchoolClass::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['message' => 'Classes order updated']);
    }
}
