<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Inertia\Inertia;

class PageController extends Controller
{
    public function single($slug) {

        $data = [];

        if ($slug) {
            $slug = htmlspecialchars($slug, ENT_QUOTES);
            $page = Page::where('slug', $slug)->select('title', 'text')->first();
            if ($page) {
                $data = $page;
            }
        }

        return Inertia::render('Text/Index', ['data' => $data]);

    }

    public function singleApi($slug) {

        $data = [];

        if ($slug) {
            $slug = htmlspecialchars($slug, ENT_QUOTES);
            $page = Page::where('slug', $slug)->select('title', 'text')->first();
            if ($page) {
                $data = $page;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
