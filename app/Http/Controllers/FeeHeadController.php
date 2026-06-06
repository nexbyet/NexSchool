<?php

namespace App\Http\Controllers;

use App\Models\FeeHead;
use Illuminate\Http\Request;

class FeeHeadController extends Controller
{
    public function index()
    {
        $heads = FeeHead::orderBy('sort_order')->get();
        return view('fees.heads.index', compact('heads'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_gu' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        if (!isset($data['sort_order']) || $data['sort_order'] === null || $data['sort_order'] === '') {
            $data['sort_order'] = FeeHead::max('sort_order') + 1;
        }

        $feeHead = FeeHead::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Fee head created',
            'fee_head' => $feeHead,
        ]);
    }

    public function show($id)
    {
        return response()->json(FeeHead::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $feeHead = FeeHead::findOrFail($id);

        $data = $request->validate([
            'name_gu' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        if (!isset($data['sort_order']) || $data['sort_order'] === null || $data['sort_order'] === '') {
            $data['sort_order'] = FeeHead::max('sort_order') + 1;
        }

        $feeHead->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Fee head updated',
            'fee_head' => $feeHead->fresh(),
        ]);
    }

    public function destroy($id)
    {
        FeeHead::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fee head deleted',
        ]);
    }
}
