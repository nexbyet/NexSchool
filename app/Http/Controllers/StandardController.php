<?php

namespace App\Http\Controllers;

use App\Models\Standard;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class StandardController extends Controller
{
    public function index()
    {
        $standards = Standard::with('classes')->orderBy('sort_order')->get();
        return view('standards.index', compact('standards'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:standards,name',
        ], ['name.unique' => 'આ નામનું ધોરણ પહેલેથી છે']);

        $maxSort = Standard::max('sort_order') ?? 0;
        $data['sort_order'] = $maxSort + 1;

        $standard = Standard::create($data);

        return response()->json([
            'success' => true,
            'message' => 'ધોરણ ઉમેરાયું',
            'standard' => $standard->load('classes'),
        ]);
    }

    public function show(Standard $standard)
    {
        return response()->json($standard->load('classes'));
    }

    public function update(Request $request, Standard $standard)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:standards,name,' . $standard->id,
        ], ['name.unique' => 'આ નામનું ધોરણ પહેલેથી છે']);

        $standard->update($data);

        return response()->json([
            'success' => true,
            'message' => 'ધોરણ સુધારાયું',
            'standard' => $standard->fresh()->load('classes'),
        ]);
    }

    public function destroy(Standard $standard)
    {
        $standard->delete();

        return response()->json([
            'success' => true,
            'message' => 'ધોરણ કાઢી નાખ્યું',
        ]);
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

        return response()->json([
            'success' => true,
            'message' => 'ક્રમ સચવાયો',
        ]);
    }

    // --- Class (વર્ગ) operations within a Standard ---

    public function storeClass(Request $request, Standard $standard)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:school_classes,name,NULL,id,standard_id,' . $standard->id,
        ], ['name.unique' => 'આ વર્ગમાં આ નામ પહેલેથી છે']);

        $maxSort = SchoolClass::where('standard_id', $standard->id)->max('sort_order') ?? 0;
        $data['sort_order'] = $maxSort + 1;
        $data['standard_id'] = $standard->id;

        $cls = SchoolClass::create($data);

        return response()->json([
            'success' => true,
            'message' => 'વર્ગ ઉમેરાયો',
            'class' => $cls,
        ]);
    }

    public function updateClass(Request $request, $classId)
    {
        $cls = SchoolClass::findOrFail($classId);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:school_classes,name,' . $classId . ',id,standard_id,' . $cls->standard_id,
        ], ['name.unique' => 'આ વર્ગમાં આ નામ પહેલેથી છે']);

        $cls->update($data);

        return response()->json([
            'success' => true,
            'message' => 'વર્ગ સુધારાયો',
            'class' => $cls,
        ]);
    }

    public function showClass($classId)
    {
        $cls = SchoolClass::findOrFail($classId);
        return response()->json($cls);
    }

    public function destroyClass($classId)
    {
        $cls = SchoolClass::findOrFail($classId);
        $cls->delete();

        return response()->json([
            'success' => true,
            'message' => 'વર્ગ કાઢી નાખ્યો',
        ]);
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

        return response()->json([
            'success' => true,
            'message' => 'વર્ગોનો ક્રમ સચવાયો',
        ]);
    }
}
