<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('created_at', 'desc')->get();
        return view('pages.index', compact('pages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title_gu' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages',
            'content_gu' => 'required|string',
            'content_en' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title_en'] ?: $data['title_gu']);
        }

        $page = Page::create($data);

        return response()->json(['success' => true, 'message' => 'પેજ બનાવાયું', 'page' => $page]);
    }

    public function show(Page $page)
    {
        return response()->json($page);
    }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'title_gu' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'content_gu' => 'required|string',
            'content_en' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title_en'] ?: $data['title_gu']);
        }

        $page->update($data);

        return response()->json(['success' => true, 'message' => 'પેજ અપડેટ થયું', 'page' => $page]);
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return response()->json(['success' => true, 'message' => 'પેજ કાઢી નાખ્યું']);
    }
}
