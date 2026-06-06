<?php

namespace App\Http\Controllers;

use App\Models\HomepageSection;
use Illuminate\Http\Request;

class HomepageSectionController extends Controller
{
    protected $sectionTypes = [
        'notice_ticker', 'slider', 'about', 'features', 'stats', 'gallery', 'contact',
    ];

    public function index()
    {
        $sections = HomepageSection::orderBy('sort_order')->get();
        return view('homepage-sections.index', compact('sections'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:' . implode(',', $this->sectionTypes),
            'content' => 'nullable|array',
        ]);

        $maxSort = HomepageSection::max('sort_order') ?? 0;
        $data['sort_order'] = $maxSort + 1;

        $section = HomepageSection::create($data);

        return response()->json(['success' => true, 'message' => 'સેક્શન ઉમેરાયું', 'section' => $section]);
    }

    public function show(HomepageSection $homepageSection)
    {
        return response()->json($homepageSection);
    }

    public function update(Request $request, HomepageSection $homepageSection)
    {
        $data = $request->validate([
            'status' => 'required|boolean',
            'content' => 'nullable|array',
        ]);

        if ($request->has('type')) {
            $data['type'] = $request->input('type');
        }

        $homepageSection->update($data);

        return response()->json(['success' => true, 'message' => 'સેક્શન અપડેટ થયું']);
    }

    public function updateContent(Request $request, HomepageSection $homepageSection)
    {
        $data = $request->validate([
            'content' => 'required|array',
        ]);

        $homepageSection->update(['content' => $data['content']]);

        return response()->json(['success' => true, 'message' => 'કન્ટેન્ટ સચવાયું']);
    }

    public function destroy(HomepageSection $homepageSection)
    {
        $homepageSection->delete();
        return response()->json(['success' => true, 'message' => 'સેક્શન કાઢી નાખ્યું']);
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:homepage_sections,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($data['items'] as $item) {
            HomepageSection::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'ક્રમ સચવાયો']);
    }
}
