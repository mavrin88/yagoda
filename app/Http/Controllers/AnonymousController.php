<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Inertia\Inertia;

class AnonymousController extends Controller
{
    public function connect() {
        return Inertia::render('Anonymous/Connect/Index');

    }

}
