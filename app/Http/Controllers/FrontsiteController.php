<?php

namespace App\Http\Controllers;

use App\Models\HomepageSection;
use App\Models\Page;
use App\Models\SchoolSetting;

class FrontsiteController extends Controller
{
    protected $school;

    public function __construct()
    {
        $this->school = SchoolSetting::find(1);
    }

    public function home()
    {
        $sections = HomepageSection::where('status', true)->orderBy('sort_order')->get();
        $school = $this->school;
        return view('frontsite.home', compact('sections', 'school'));
    }

    public function page($slug)
    {
        $page = Page::where('slug', $slug)->where('status', true)->firstOrFail();
        $school = $this->school;
        return view('frontsite.page', compact('page', 'school'));
    }
}
