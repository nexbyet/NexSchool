<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('items.children')->orderBy('id')->get();
        $pages = Page::where('status', 'published')->orderBy('title_gu')->get();
        return view('menus.index', compact('menus', 'pages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|in:header,footer',
        ]);

        $menu = Menu::create($data + ['status' => true]);

        return response()->json(['success' => true, 'message' => 'મેનુ બનાવાયો', 'menu' => $menu]);
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|in:header,footer',
        ]);

        $menu->update($data);

        return response()->json(['success' => true, 'message' => 'મેનુ અપડેટ થયો']);
    }

    public function destroy(Menu $menu)
    {
        $menu->items()->delete();
        $menu->delete();
        return response()->json(['success' => true, 'message' => 'મેનુ કાઢી નાખ્યો']);
    }

    public function storeItem(Request $request)
    {
        $data = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'parent_id' => 'nullable|exists:menu_items,id',
            'title_gu' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'page_id' => 'nullable|exists:pages,id',
            'target' => 'required|in:_self,_blank',
        ]);

        $maxSort = MenuItem::where('menu_id', $data['menu_id'])->max('sort_order') ?? 0;
        $data['sort_order'] = $maxSort + 1;

        $item = MenuItem::create($data + ['status' => true]);

        return response()->json(['success' => true, 'message' => 'મેનુ આઇટમ ઉમેરાઈ', 'item' => $item->load('page')]);
    }

    public function updateItem(Request $request, MenuItem $menuItem)
    {
        $data = $request->validate([
            'title_gu' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'page_id' => 'nullable|exists:pages,id',
            'target' => 'required|in:_self,_blank',
            'status' => 'required|boolean',
        ]);

        $menuItem->update($data);

        return response()->json(['success' => true, 'message' => 'મેનુ આઇટમ અપડેટ થઈ']);
    }

    public function destroyItem(MenuItem $menuItem)
    {
        $menuItem->children()->delete();
        $menuItem->delete();
        return response()->json(['success' => true, 'message' => 'મેનુ આઇટમ કાઢી નાખી']);
    }

    public function reorderItems(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.sort_order' => 'required|integer|min:0',
            'items.*.parent_id' => 'nullable|exists:menu_items,id',
        ]);

        foreach ($data['items'] as $item) {
            MenuItem::where('id', $item['id'])->update([
                'sort_order' => $item['sort_order'],
                'parent_id' => $item['parent_id'] ?? null,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'ક્રમ સચવાયો']);
    }

    public function getItems(Menu $menu)
    {
        $items = $menu->activeItems()->with('page', 'children')->get();
        return response()->json(['items' => $items]);
    }
}
