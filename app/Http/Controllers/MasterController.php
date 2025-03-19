<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class MasterController extends Controller
{
    public function statistics()
    {
        return Inertia::render('Master/Statistics/Index');
    }

    public function getFilterStatistics(Request $request)
    {
        dd($request->all());
    }
}
