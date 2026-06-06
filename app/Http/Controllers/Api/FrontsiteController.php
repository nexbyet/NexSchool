<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdmissionInquiry;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\HomepageSection;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Notice;
use App\Models\Page;
use App\Models\SliderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FrontsiteController extends Controller
{
    // ─── PAGES ────────────────────────────────────────────────

    public function pages()
    {
        return response()->json(Page::orderBy('created_at', 'desc')->get());
    }

    public function storePage(Request $request)
    {
        $data = $request->validate([
            'title_gu' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'slug'     => 'nullable|string|max:255|unique:pages',
            'content_gu' => 'required|string',
            'content_en' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'status'   => 'required|in:draft,published',
        ]);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title_en'] ?: $data['title_gu']);
        }
        $page = Page::create($data);
        return response()->json(['success' => true, 'message' => 'પેજ બનાવાયું', 'page' => $page], 201);
    }

    public function showPage(Page $page)
    {
        return response()->json($page);
    }

    public function updatePage(Request $request, Page $page)
    {
        $data = $request->validate([
            'title_gu' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'slug'     => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'content_gu' => 'required|string',
            'content_en' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'status'   => 'required|in:draft,published',
        ]);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title_en'] ?: $data['title_gu']);
        }
        $page->update($data);
        return response()->json(['success' => true, 'message' => 'પેજ અપડેટ થયું', 'page' => $page->fresh()]);
    }

    public function destroyPage(Page $page)
    {
        $page->delete();
        return response()->json(['success' => true, 'message' => 'પેજ કાઢી નાખ્યું']);
    }

    // ─── MENUS ────────────────────────────────────────────────

    public function menus()
    {
        $menus = Menu::with('items.children', 'items.page')->get();
        return response()->json($menus);
    }

    public function storeMenu(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'location' => 'required|in:header,footer',
        ]);
        $data['status'] = true;
        $menu = Menu::create($data);
        return response()->json(['success' => true, 'message' => 'મેનુ બનાવાયો', 'menu' => $menu], 201);
    }

    public function updateMenu(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'location' => 'required|in:header,footer',
        ]);
        $menu->update($data);
        return response()->json(['success' => true, 'message' => 'મેનુ અપડેટ થયો']);
    }

    public function destroyMenu(Menu $menu)
    {
        MenuItem::where('menu_id', $menu->id)->delete();
        $menu->delete();
        return response()->json(['success' => true, 'message' => 'મેનુ કાઢી નાખ્યો']);
    }

    // ─── MENU ITEMS ───────────────────────────────────────────

    public function menuItems(Menu $menu)
    {
        return response()->json(['items' => $menu->activeItems()->with('page', 'children')->get()]);
    }

    public function storeMenuItem(Request $request)
    {
        $data = $request->validate([
            'menu_id'   => 'required|exists:menus,id',
            'parent_id' => 'nullable|exists:menu_items,id',
            'title_gu'  => 'required|string|max:255',
            'title_en'  => 'nullable|string|max:255',
            'url'       => 'nullable|string|max:255',
            'page_id'   => 'nullable|exists:pages,id',
            'target'    => 'required|in:_self,_blank',
        ]);
        $maxSort = MenuItem::where('menu_id', $data['menu_id'])->max('sort_order') ?? 0;
        $data['sort_order'] = $maxSort + 1;
        $data['status'] = true;
        $item = MenuItem::create($data)->load('page');
        return response()->json(['success' => true, 'message' => 'મેનુ આઇટમ ઉમેરાઈ', 'item' => $item], 201);
    }

    public function updateMenuItem(Request $request, MenuItem $menuItem)
    {
        $data = $request->validate([
            'title_gu' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'url'      => 'nullable|string|max:255',
            'page_id'  => 'nullable|exists:pages,id',
            'target'   => 'required|in:_self,_blank',
            'status'   => 'required|boolean',
        ]);
        $menuItem->update($data);
        return response()->json(['success' => true, 'message' => 'મેનુ આઇટમ અપડેટ થઈ']);
    }

    public function destroyMenuItem(MenuItem $menuItem)
    {
        MenuItem::where('parent_id', $menuItem->id)->delete();
        $menuItem->delete();
        return response()->json(['success' => true, 'message' => 'મેનુ આઇટમ કાઢી નાખી']);
    }

    public function reorderMenuItems(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.sort_order' => 'required|integer|min:0',
            'items.*.parent_id' => 'nullable|exists:menu_items,id',
        ]);
        foreach ($request->items as $item) {
            MenuItem::where('id', $item['id'])->update([
                'sort_order' => $item['sort_order'],
                'parent_id' => $item['parent_id'] ?? null,
            ]);
        }
        return response()->json(['success' => true, 'message' => 'ક્રમ સચવાયો']);
    }

    // ─── NOTICES ──────────────────────────────────────────────

    public function notices()
    {
        return response()->json(Notice::orderBy('date', 'desc')->get());
    }

    public function storeNotice(Request $request)
    {
        $data = $request->validate([
            'title_gu'   => 'required|string|max:255',
            'title_en'   => 'nullable|string|max:255',
            'content_gu' => 'nullable|string',
            'content_en' => 'nullable|string',
            'is_circular'=> 'required|boolean',
            'date'       => 'required|date',
        ]);
        if ($request->hasFile('file_path')) {
            $request->validate(['file_path' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120']);
            $data['file_path'] = $request->file('file_path')->store('notices', 'public');
        }
        $notice = Notice::create($data);
        return response()->json(['success' => true, 'message' => 'સૂચના ઉમેરાઈ', 'notice' => $notice], 201);
    }

    public function showNotice(Notice $notice)
    {
        return response()->json($notice);
    }

    public function updateNotice(Request $request, Notice $notice)
    {
        $data = $request->validate([
            'title_gu'   => 'required|string|max:255',
            'title_en'   => 'nullable|string|max:255',
            'content_gu' => 'nullable|string',
            'content_en' => 'nullable|string',
            'is_circular'=> 'required|boolean',
            'date'       => 'required|date',
        ]);
        if ($request->hasFile('file_path')) {
            $request->validate(['file_path' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120']);
            if ($notice->file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($notice->file_path);
            }
            $data['file_path'] = $request->file('file_path')->store('notices', 'public');
        }
        $notice->update($data);
        return response()->json(['success' => true, 'message' => 'સૂચના અપડેટ થઈ']);
    }

    public function destroyNotice(Notice $notice)
    {
        if ($notice->file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($notice->file_path);
        }
        $notice->delete();
        return response()->json(['success' => true, 'message' => 'સૂચના કાઢી નાખી']);
    }

    // ─── SLIDERS ──────────────────────────────────────────────

    public function sliders()
    {
        return response()->json(SliderItem::orderBy('sort_order')->get());
    }

    public function storeSlider(Request $request)
    {
        $data = $request->validate([
            'title_gu'    => 'required|string|max:255',
            'title_en'    => 'nullable|string|max:255',
            'subtitle_gu' => 'nullable|string|max:255',
            'subtitle_en' => 'nullable|string|max:255',
            'link_url'    => 'nullable|string|max:255',
        ]);
        $maxSort = SliderItem::max('sort_order') ?? 0;
        $data['sort_order'] = $maxSort + 1;
        if ($request->hasFile('image')) {
            $request->validate(['image' => 'image|mimes:jpeg,png,jpg,webp|max:5120']);
            $data['image'] = $request->file('image')->store('sliders', 'public');
        }
        $slide = SliderItem::create($data);
        return response()->json(['success' => true, 'message' => 'સ્લાઇડ ઉમેરાઈ', 'slide' => $slide], 201);
    }

    public function showSlider(SliderItem $sliderItem)
    {
        return response()->json($sliderItem);
    }

    public function updateSlider(Request $request, SliderItem $sliderItem)
    {
        $data = $request->validate([
            'title_gu'    => 'required|string|max:255',
            'title_en'    => 'nullable|string|max:255',
            'subtitle_gu' => 'nullable|string|max:255',
            'subtitle_en' => 'nullable|string|max:255',
            'link_url'    => 'nullable|string|max:255',
            'status'      => 'required|boolean',
        ]);
        if ($request->hasFile('image')) {
            $request->validate(['image' => 'image|mimes:jpeg,png,jpg,webp|max:5120']);
            if ($sliderItem->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($sliderItem->image);
            }
            $data['image'] = $request->file('image')->store('sliders', 'public');
        }
        $sliderItem->update($data);
        return response()->json(['success' => true, 'message' => 'સ્લાઇડ અપડેટ થઈ']);
    }

    public function destroySlider(SliderItem $sliderItem)
    {
        if ($sliderItem->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($sliderItem->image);
        }
        $sliderItem->delete();
        return response()->json(['success' => true, 'message' => 'સ્લાઇડ કાઢી નાખી']);
    }

    public function reorderSliders(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:slider_items,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);
        foreach ($request->items as $item) {
            SliderItem::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }
        return response()->json(['success' => true, 'message' => 'ક્રમ સચવાયો']);
    }

    // ─── GALLERIES ────────────────────────────────────────────

    public function galleries()
    {
        return response()->json(Gallery::with('images')->orderBy('sort_order')->get());
    }

    public function storeGallery(Request $request)
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
        return response()->json(['success' => true, 'message' => 'ગેલેરી બનાવાઈ', 'gallery' => $gallery], 201);
    }

    public function showGallery(Gallery $gallery)
    {
        return response()->json($gallery->load('images'));
    }

    public function updateGallery(Request $request, Gallery $gallery)
    {
        $data = $request->validate([
            'name_gu' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description_gu' => 'nullable|string',
            'description_en' => 'nullable|string',
            'status'   => 'required|boolean',
        ]);
        $gallery->update($data);
        return response()->json(['success' => true, 'message' => 'ગેલેરી અપડેટ થઈ']);
    }

    public function destroyGallery(Gallery $gallery)
    {
        foreach ($gallery->images as $img) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($img->image);
            $img->delete();
        }
        $gallery->delete();
        return response()->json(['success' => true, 'message' => 'ગેલેરી કાઢી નાખી']);
    }

    // ─── GALLERY IMAGES ───────────────────────────────────────

    public function storeGalleryImage(Request $request, Gallery $gallery)
    {
        $request->validate([
            'image'      => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'caption_gu' => 'nullable|string|max:255',
            'caption_en' => 'nullable|string|max:255',
        ]);
        $maxSort = GalleryImage::where('gallery_id', $gallery->id)->max('sort_order') ?? 0;
        $path = $request->file('image')->store('galleries', 'public');
        $img = $gallery->images()->create([
            'image' => $path,
            'caption_gu' => $request->caption_gu,
            'caption_en' => $request->caption_en,
            'sort_order' => $maxSort + 1,
        ]);
        return response()->json(['success' => true, 'message' => 'ફોટો ઉમેરાયો', 'image' => $img], 201);
    }

    public function destroyGalleryImage(GalleryImage $galleryImage)
    {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($galleryImage->image);
        $galleryImage->delete();
        return response()->json(['success' => true, 'message' => 'ફોટો કાઢી નાખ્યો']);
    }

    public function reorderGalleryImages(Request $request, Gallery $gallery)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:gallery_images,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);
        foreach ($request->items as $item) {
            GalleryImage::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }
        return response()->json(['success' => true, 'message' => 'ક્રમ સચવાયો']);
    }

    // ─── HOMEPAGE SECTIONS ────────────────────────────────────

    public function homepageSections()
    {
        return response()->json(HomepageSection::orderBy('sort_order')->get());
    }

    public function storeHomepageSection(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:notice_ticker,slider,about,features,stats,gallery,contact',
        ]);
        $maxSort = HomepageSection::max('sort_order') ?? 0;
        $section = HomepageSection::create([
            'type' => $data['type'],
            'content' => $request->content ?? [],
            'sort_order' => $maxSort + 1,
            'status' => true,
        ]);
        return response()->json(['success' => true, 'message' => 'સેક્શન ઉમેરાયું', 'section' => $section], 201);
    }

    public function showHomepageSection(HomepageSection $homepageSection)
    {
        return response()->json($homepageSection);
    }

    public function updateHomepageSection(Request $request, HomepageSection $homepageSection)
    {
        $data = $request->validate([
            'status'  => 'required|boolean',
            'content' => 'nullable|array',
        ]);
        $homepageSection->update($data);
        return response()->json(['success' => true, 'message' => 'સેક્શન અપડેટ થયું']);
    }

    public function updateHomepageSectionContent(Request $request, HomepageSection $homepageSection)
    {
        $request->validate(['content' => 'required|array']);
        $homepageSection->update(['content' => $request->content]);
        return response()->json(['success' => true, 'message' => 'કન્ટેન્ટ સચવાયું']);
    }

    public function destroyHomepageSection(HomepageSection $homepageSection)
    {
        $homepageSection->delete();
        return response()->json(['success' => true, 'message' => 'સેક્શન કાઢી નાખ્યું']);
    }

    public function reorderHomepageSections(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:homepage_sections,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);
        foreach ($request->items as $item) {
            HomepageSection::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }
        return response()->json(['success' => true, 'message' => 'ક્રમ સચવાયો']);
    }

    // ─── ADMISSION INQUIRIES ──────────────────────────────────

    public function admissionInquiries()
    {
        return response()->json(AdmissionInquiry::with('academicYear')->orderBy('created_at', 'desc')->get());
    }

    public function showAdmissionInquiry(AdmissionInquiry $admissionInquiry)
    {
        return response()->json($admissionInquiry->load('academicYear'));
    }

    public function approveAdmissionInquiry(Request $request, AdmissionInquiry $admissionInquiry)
    {
        $request->validate([
            'gr_number'        => 'nullable|string|max:50|unique:admission_inquiries,gr_number,' . $admissionInquiry->id,
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'admin_notes'      => 'nullable|string|max:1000',
        ]);
        $admissionInquiry->update([
            'status' => 'approved',
            'gr_number' => $request->gr_number,
            'academic_year_id' => $request->academic_year_id,
            'admin_notes' => $request->admin_notes,
            'approved_at' => now(),
        ]);
        return response()->json(['success' => true, 'message' => 'પ્રવેશ મંજૂર કર્યો.']);
    }

    public function rejectAdmissionInquiry(Request $request, AdmissionInquiry $admissionInquiry)
    {
        $request->validate(['admin_notes' => 'nullable|string|max:1000']);
        $admissionInquiry->update(['status' => 'rejected', 'admin_notes' => $request->admin_notes]);
        return response()->json(['success' => true, 'message' => 'અરજી નામંજૂર કરી.']);
    }

    public function destroyAdmissionInquiry(AdmissionInquiry $admissionInquiry)
    {
        $admissionInquiry->delete();
        return response()->json(['success' => true, 'message' => 'અરજી કાઢી નાખી.']);
    }
}
