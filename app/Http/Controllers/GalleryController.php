<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::with('images')->orderBy('sort_order')->get();
        return view('galleries.index', compact('galleries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_gu' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description_gu' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);

        $maxSort = Gallery::max('sort_order') ?? 0;
        $data['sort_order'] = $maxSort + 1;

        $gallery = Gallery::create($data);

        return response()->json(['success' => true, 'message' => 'ગેલેરી બનાવાઈ', 'gallery' => $gallery]);
    }

    public function show(Gallery $gallery)
    {
        $gallery->load('images');
        return response()->json($gallery);
    }

    public function update(Request $request, Gallery $gallery)
    {
        $data = $request->validate([
            'name_gu' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description_gu' => 'nullable|string',
            'description_en' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $gallery->update($data);

        return response()->json(['success' => true, 'message' => 'ગેલેરી અપડેટ થઈ']);
    }

    public function destroy(Gallery $gallery)
    {
        foreach ($gallery->images as $img) {
            \Storage::disk('public')->delete($img->image);
        }
        $gallery->images()->delete();
        $gallery->delete();
        return response()->json(['success' => true, 'message' => 'ગેલેરી કાઢી નાખી']);
    }

    public function storeImage(Request $request, Gallery $gallery)
    {
        $data = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'caption_gu' => 'nullable|string|max:255',
            'caption_en' => 'nullable|string|max:255',
        ]);

        $data['image'] = $request->file('image')->store('galleries', 'public');
        $maxSort = GalleryImage::where('gallery_id', $gallery->id)->max('sort_order') ?? 0;
        $data['sort_order'] = $maxSort + 1;
        $data['gallery_id'] = $gallery->id;

        $image = GalleryImage::create($data);

        return response()->json(['success' => true, 'message' => 'ફોટો ઉમેરાયો', 'image' => $image]);
    }

    public function destroyImage(GalleryImage $galleryImage)
    {
        \Storage::disk('public')->delete($galleryImage->image);
        $galleryImage->delete();
        return response()->json(['success' => true, 'message' => 'ફોટો કાઢી નાખ્યો']);
    }

    public function reorderImages(Request $request, Gallery $gallery)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:gallery_images,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($data['items'] as $item) {
            GalleryImage::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'ક્રમ સચવાયો']);
    }
}
