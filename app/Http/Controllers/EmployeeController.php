<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function index()
    {
        $data = [
        'revenue' => 0,
        'withTips' => 0,
        'percentageWithTips' => 0,
        'date' => '23.02.2024',
        'masters' => []
    ];

        return Inertia::render('Employee/Tips/Index', compact('data'));
    }
}
