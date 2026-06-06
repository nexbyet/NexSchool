<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function index()
    {
        $defaults = [
            'primary'       => '#059669',
            'primary_hover' => '#047857',
            'header_from'   => '#059669',
            'header_to'     => '#0d9488',
            'footer_bg'     => '#111827',
            'accent'        => '#d97706',
        ];
        $saved = Setting::getValue('frontsite_theme');
        $theme = $saved ? array_merge($defaults, json_decode($saved, true)) : $defaults;
        return view('settings.theme', compact('theme'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'primary'       => 'required|string|max:20',
            'primary_hover' => 'required|string|max:20',
            'header_from'   => 'required|string|max:20',
            'header_to'     => 'required|string|max:20',
            'footer_bg'     => 'required|string|max:20',
            'accent'        => 'required|string|max:20',
        ]);

        Setting::setValue('frontsite_theme', json_encode($data, JSON_UNESCAPED_UNICODE), 'frontsite');

        return response()->json(['success' => true, 'message' => 'રંગ થીમ સચવાઈ.']);
    }
}
