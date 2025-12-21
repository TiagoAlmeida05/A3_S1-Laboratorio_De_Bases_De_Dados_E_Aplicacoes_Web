<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WebsiteContent;

class PageController extends Controller
{
    private function getPage($name)
    {
        $page = WebsiteContent::where('name', $name)->first();

        if (!$page) {
            abort(404, 'ERROR 404. NOT FOUND');
        }
        return $page;
    }

    public function about()
    {
        return view('pages.show', ['page' => $this->getPage('About Us')]);
    }

    public function terms()
    {
        return view('pages.show', ['page' => $this->getPage('Terms of Service')]);    }

    public function privacy()
    {
        return view('pages.show', ['page' => $this->getPage('Privacy Policy')]);
    }

    public function faq(){
        return view('pages.show', ['page' => $this->getPage('FAQ')]);
    }

    public function contacts() {
        return view('pages.show', ['page' => $this->getPage('Contact us')]);
    }
}