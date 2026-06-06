<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\SchoolSetting;

class FrontsiteNoticeController extends Controller
{
    public function index()
    {
        $school = SchoolSetting::find(1);
        $notices = Notice::where('status', true)->where('date', '<=', now())
            ->orderBy('date', 'desc')
            ->paginate(20);
        return view('frontsite.notices.index', compact('school', 'notices'));
    }

    public function show($id)
    {
        $school = SchoolSetting::find(1);
        $notice = Notice::where('status', true)->where('date', '<=', now())
            ->findOrFail($id);
        $recent = Notice::where('status', true)->where('date', '<=', now())
            ->where('id', '!=', $id)
            ->orderBy('date', 'desc')
            ->take(5)->get();
        return view('frontsite.notices.show', compact('school', 'notice', 'recent'));
    }
}
