<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::orderBy('date', 'desc')->get();
        return view('notice-board.index', compact('notices'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title_gu' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'content_gu' => 'nullable|string',
            'content_en' => 'nullable|string',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'is_circular' => 'required|boolean',
            'date' => 'required|date',
        ]);

        if ($request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('notices', 'public');
        }

        $notice = Notice::create($data);

        return response()->json(['success' => true, 'message' => 'સૂચના ઉમેરાઈ', 'notice' => $notice]);
    }

    public function show(Notice $notice)
    {
        return response()->json($notice);
    }

    public function update(Request $request, Notice $notice)
    {
        $data = $request->validate([
            'title_gu' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'content_gu' => 'nullable|string',
            'content_en' => 'nullable|string',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'is_circular' => 'required|boolean',
            'date' => 'required|date',
        ]);

        if ($request->hasFile('file_path')) {
            if ($notice->file_path) {
                \Storage::disk('public')->delete($notice->file_path);
            }
            $data['file_path'] = $request->file('file_path')->store('notices', 'public');
        } else {
            unset($data['file_path']);
        }

        $notice->update($data);

        return response()->json(['success' => true, 'message' => 'સૂચના અપડેટ થઈ']);
    }

    public function destroy(Notice $notice)
    {
        if ($notice->file_path) {
            \Storage::disk('public')->delete($notice->file_path);
        }
        $notice->delete();
        return response()->json(['success' => true, 'message' => 'સૂચના કાઢી નાખી']);
    }
}
