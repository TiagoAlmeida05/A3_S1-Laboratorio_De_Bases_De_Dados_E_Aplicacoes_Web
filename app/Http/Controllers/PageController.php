<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WebsiteContent;

class PageController extends Controller
{
    public function about()
    {
        $page = WebsiteContent::where('name', 'About Us')->first();

        if (!$page) {
            abort(404, 'ERROR 404. NOT FOUND');
        }

        return view('pages.show', compact('page'));
    }

    public function terms()
    {
        $page = WebsiteContent::where('name', 'Terms of Service')->first();

        if (!$page) {
            abort(404);
        }

        return view('pages.show', compact('page'));
    }

    public function privacy()
    {
        $page = WebsiteContent::where('name', 'Privacy Policy')->first();

        if (!$page) {
            abort(404);
        }

        return view('pages.show', compact('page'));
    }
}