<?php

namespace App\Modules\YagodaTips\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MasterController extends Controller
{
    public function index()
    {
        $data = [
            'role_name' => 'Координатор',
            'group_name' => 'Салон бритва',
            'employees_on_shift_admin' => 5,
            'employees_on_shift_master' => 3,
            'employees_on_shift_staff' => 2,
            'translations' => 56,
            'translations_summ' => '153 000',
            'total_translations_summ' => '106 000',
            'employees' => [
                'admins' => [
                    'name' => 'АДМИНИСТРАТОРЫ',
                    'total_translations_summ' => '106 000',
                    'employees' => [
                        [
                            'name' => 'Василий',
                            'avatar' => '/img/content/avatar.png',
                            'translations' => 15,
                            'translations_summ' => '53 000',
                        ],
                        [
                            'name' => 'Василий',
                            'avatar' => '/img/content/avatar.png',
                            'translations' => 20,
                            'translations_summ' => '53 000',
                        ]
                    ]
                ],
            ]
        ];
        return Inertia::render('YagodaTips/Masters/Index', compact('data'));
    }

    public function qrList()
    {
        return Inertia::render('YagodaTips/Masters/Qr/Index');
    }
    public function single()
    {
        return Inertia::render('YagodaTips/Masters/Qr/Single/Index');
    }
    public function singleStand()
    {
        return Inertia::render('YagodaTips/Masters/Qr/SingleStand/Index');
    }
    public function add()
    {
        return Inertia::render('YagodaTips/Masters/Qr/Add/Index');
    }
    public function stands()
    {
        return Inertia::render('YagodaTips/Masters/Qr/Stands/Index');
    }
}
