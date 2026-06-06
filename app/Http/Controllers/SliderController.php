<?php

namespace App\Http\Controllers;

use App\Models\SliderItem;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function index()
    {
        $slides = SliderItem::orderBy('sort_order')->get();
        return view('sliders.index', compact('slides'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title_gu' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'subtitle_gu' => 'nullable|string|max:255',
            'subtitle_en' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'link_url' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('sliders', 'public');
        }

        $maxSort = SliderItem::max('sort_order') ?? 0;
        $data['sort_order'] = $maxSort + 1;

        $slide = SliderItem::create($data);

        return response()->json(['success' => true, 'message' => 'સ્લાઇડ ઉમેરાઈ', 'slide' => $slide]);
    }

    public function show(SliderItem $sliderItem)
    {
        return response()->json($sliderItem);
    }

    public function update(Request $request, SliderItem $sliderItem)
    {
        $data = $request->validate([
            'title_gu' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'subtitle_gu' => 'nullable|string|max:255',
            'subtitle_en' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'link_url' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($sliderItem->image) {
                \Storage::disk('public')->delete($sliderItem->image);
            }
            $data['image'] = $request->file('image')->store('sliders', 'public');
        } else {
            unset($data['image']);
        }

        $sliderItem->update($data);

        return response()->json(['success' => true, 'message' => 'સ્લાઇડ અપડેટ થઈ']);
    }

    public function destroy(SliderItem $sliderItem)
    {
        if ($sliderItem->image) {
            \Storage::disk('public')->delete($sliderItem->image);
        }
        $sliderItem->delete();
        return response()->json(['success' => true, 'message' => 'સ્લાઇડ કાઢી નાખી']);
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:slider_items,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($data['items'] as $item) {
            SliderItem::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'ક્રમ સચવાયો']);
    }
}
