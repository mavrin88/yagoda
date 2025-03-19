<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminController extends Controller
{
    public function indexAdmin(Request $request)
    {
        $user = Auth::user();

        $role_slug = Session::get('selected_organization_role_slug');
        $selected_entity_type = Session::get('selected_entity_type');

        if ($selected_entity_type == 'group') {
            return redirect()->route('tips.coord');
        }

        $save_new_organization_from_menu = $request->query('is_save_new_organization');

        if (!$role_slug && $user && $user->is_own_organization === 0) {
            return redirect()->route('organizations.choose');
        } elseif (!$role_slug && $user && $user->is_own_organization === 1) {
            return redirect()->route('main');
        } elseif ($role_slug && $user && $user->is_own_organization === 0 && $save_new_organization_from_menu) {
            return redirect()->route('organizations.choose');
        }

//        if (!$role_slug && $user) {
//            return redirect()->route('organizations.choose');
//        }else if (!$role_slug && $user && $user->is_own_organization === 1){
//            return redirect()->route('main');
//    }
        $roleConfig = config("roles.{$role_slug}", 'Anonymous/Landing');

        return Inertia::render($roleConfig);
    }


    public function indexMaster(): Response
    {
        return Inertia::render('Admin/Master');
    }

    public function orders(): Response
    {
        Carbon::setLocale('ru');

        $data = [
            'total_orders_sale' =>  number_format(2358000, 0, '', ' '),
            'total_orders_tips' => 66,
            'percent_tips' => 77,
            'medium_tips' => 6,
            'total_tips' => number_format(17, 0, '', ' '),
            'master_count' => 10,
            'date' => Carbon::now()->format('d M Y'),
            'orders' => [
                [
                    'price' => '2 222 500',
                    'master' => 'Игорь',
                    'tips' => '10 000',
                    'status' => 'ok',
                ],
                [
                    'price' => '1 124 500',
                    'master' => 'Леонардо Давинчи',
                    'tips' => '25 000',
                    'status' => 'cancel',
                ],
                [
                    'price' => '225 500',
                    'master' => 'Василий',
                    'tips' => '78 995',
                    'status' => 'cancel',
                ],
                [
                    'price' => '1 500',
                    'master' => 'Сергей',
                    'tips' => '20',
                    'status' => 'cancel',
                ],
            ],
        ];

        return Inertia::render('Admin/Orders', compact('data'));
    }

    public function bill(): Response
    {
        $data = [
            'total' =>  number_format(10400, 0, '', ' '),
            'discount' => 10,
            'discountedTotal' => 1440,
            'tips' => 900,
            'date' => '10 июля 2024 16:56',
            'statusName' => 'оплачено',
            'status' => 'ok',
            'billItems' => [
                [
                    'title' => 'Очень длинное название услуги, если вдруг нет ограничений',
                    'price' => 2500,
                    'quantity' => 1,
                    'amount' => 2500,
                ],
                [
                    'title' => 'Бритье бороды',
                    'price' => 7400,
                    'quantity' => 2,
                    'amount' => 7400,
                ],
            ],
            'user' => [
                'avatar' => '/img/content/avatar.png',
                'name' => 'Василий Сергеевич Рюриков-Корсаковский-Аполлонов'
            ],
        ];

        return Inertia::render('Admin/Bill/Index', compact('data'));
    }

    public function choiceMaster(): Response
    {
        $data = [
            'total' =>  number_format(10400, 0, '', ' '),
            'discount' => 10,
            'discountedTotal' => 1440,
            'mastersTotal' => 5,
            'positions' => 13,

            'billItems' => [
                [
                    'title' => 'Очень длинное название услуги, если вдруг нет ограничений',
                    'price' => 2500,
                    'quantity' => 1,
                    'amount' => 2500,
                ],
                [
                    'title' => 'Бритье бороды',
                    'price' => 7400,
                    'quantity' => 2,
                    'amount' => 7400,
                ],
            ],
            'masters' => [
                [
                    'id' => 110,
                    'name' => 'Епивстафиний',
                    'avatar' => '/img/content/avatar.png',
                ],
                [
                    'id' => 210,
                    'name' => 'Василий',
                    'avatar' => '/img/content/avatar.png',
                ]
            ],
        ];

        return Inertia::render('Admin/ChoiceMaster/Index', compact('data'));
    }

    public function ChoiceServices(): Response
    {
        $data = [
            'total' =>  number_format(12350, 0, '', ' '),
            'positions' => 16,
            'categories' => [
                [
                    'image' => '/img/demo/beard.png',
                    'title' => 'Борода',
                    'active' => true,
                ],
                [
                    'image' => '/img/demo/beard.png',
                    'title' => 'Стрижки',
                    'active' => false,
                ],
                [
                    'image' => '/img/demo/beard.png',
                    'title' => 'Маникюр Маникюр Маникюр',
                    'active' => false,
                ]
            ],
            'services' => [
                [
                    'icon' => '/img/content/icon_service.svg',
                    'name' => 'Стрижка модельная с навороченной укладкой',
                    'price' => 3100,
                    'quantity' => 1,
                    'active' => true,
                ],
                [
                    'icon' => '/img/content/icon_service.svg',
                    'name' => 'Стрижка модельная с навороченной укладкой',
                    'price' => 3100,
                    'quantity' => 0,
                    'active' => false,
                ]
            ],
        ];

        return Inertia::render('Admin/ChoiceServices/Index', compact('data'));
    }

    public function order()
    {
        $qrCode = QrCode::size(512)
            ->format('png')
            ->errorCorrection('M')
            ->generate(
                'https://ratewell.ru/vaddfpo12321',
            );

        $data = [
            'qrCode' => "data:image/png;base64," . base64_encode($qrCode),
            'qrItems' => [
                [
                    'count' => 22,
                    'name' => 'Ресепшен',
                ],
                [
                    'count' => 22,
                    'name' => 'Ресепшен',
                ],
                [
                    'count' => 22,
                    'name' => 'Ресепшен',
                ],
                [
                    'count' => 22,
                    'name' => 'Ресепшен',
                ]
            ]
        ];

        return Inertia::render('Admin/QrOrder/Index', compact('data'));
    }

    public function OrderSuccess()
    {
        $data = [
            'total' =>  number_format(12350, 0, '', ' '),
            'tips' =>  number_format(100, 0, '', ' '),
            'status' => 'ok',
            'statusName' => 'Оплачено !',
        ];

        return Inertia::render('Admin/OrderSuccess', compact('data'));
    }

    public function statistics()
    {
        $data = [
            'revenue' => 0,
            'equalizationFee' => 0,
            'savedEqualization' => 0,
            'totalOrders' => 0,
            'date' => '23.02.2024',
            'masters' => []
        ];

        return Inertia::render('Admin/Statistics/Index', compact('data'));
    }
}
