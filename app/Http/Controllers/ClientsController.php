<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStatistics;
use App\Models\Role;
use App\Models\Organization;
use App\Models\QrCode;
use App\Modules\YagodaTips\Models\Group;
use App\Modules\YagodaTips\Models\QrCode as QrCodeTips;
use App\Modules\YagodaTips\Models\Setting as SettingTips;
use App\Models\Setting;
use App\Modules\YagodaTips\Services\ShiftService;
use App\Repository\TochkaRepository;
use App\Repository\VbrRepository;
use App\Services\VbrrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\URL;

class ClientsController extends Controller
{
    protected $shiftService;
    public function __construct(ShiftService $shiftService)
    {
        $this->shiftService = $shiftService;
    }

    public function handleLink($link)
    {
        $qrCode = $this->getQrCodeByLink($link);

        if ($qrCode) {
            return $this->getYagodaPagePay($qrCode);
        }

        $qrCode = $this->getQrCodeByLinkTips($link);

        if ($qrCode) {
            return $this->getYagodaTipsPagePay($qrCode);
        }

        return redirect('/');
    }

    private function getYagodaPagePay($qrCode)
    {
        if ($qrCode->orders->isEmpty()) {
            return redirect('/');
        }

        $order = $qrCode->orders->first();

        $organization = Organization::find($order->organization_id);

        $organization->logo_path = $organization->logo_path ? URL::route('image', ['path' => $organization->logo_path]) : null;
        $organization->photo_path = $organization->photo_path ? URL::route('image', ['path' => $organization->photo_path]) : null;

        $settings = [];
        $settingsAll = Setting::get();
        if ($settingsAll) {
            foreach($settingsAll as $setting) {
                $settings[$setting->key] = $setting->value;
            }
        }

        $orderParticipants = $this->getOrderParticipants($qrCode);

        $this->updateOrderAsOpen($order);

        $data = $this->prepareData($organization, $order, $order->orderItems, $orderParticipants, $settings);

        return Inertia::render('Client/Index', compact('data'));
    }

    private function getYagodaTipsPagePay($qrCode)
    {
        $group = Group::find($qrCode->group_id);

        if (!$group) {
            return redirect('/');
        }

        $group->logo_path = $group->logo_path ? URL::route('image', ['path' => $group->logo_path]) : null;
        $group->photo_path = $group->photo_path ? URL::route('image', ['path' => $group->photo_path]) : null;

        $settings = [];

        $settingsAll = SettingTips::get();

        if ($settingsAll) {
            foreach($settingsAll as $setting) {
                $settings[$setting->key] = $setting->value;
            }
        }

        $activeMasters = $this->shiftService->getAllShifts();

        $data = [];

        if ($activeMasters && $activeMasters['masters']) {
            $data['masters'] = $activeMasters['masters'];
        } else {
            $data['masters'] = $this->shiftService->getAllMasters();
        }

        return Inertia::render('YagodaTips/Client/Masters/Index', compact('data','group', 'settings'));
    }

    private function getQrCodeByLink($link)
    {
        return QrCode::where(function($query) use ($link) {
            $query->where('link', 'https://yagoda-pay.ru/order/' . $link)
                ->orWhere('link', 'https://yagoda.team/order/' . $link);
        })
            ->first();
    }

    private function getQrCodeByLinkTips($link)
    {
        return QrCodeTips::where(function($query) use ($link) {
            $query->where('link', 'https://yagoda-pay.ru/order/' . $link)
                ->orWhere('link', 'https://yagoda.team/order/' . $link);
        })
            ->first();
    }

    private function getOrderParticipants($qrCode)
    {
        $role = Role::where('slug', 'master')->first();

        $order = $qrCode->orders->first();

        $order->load(['orderParticipants' => function($query) use ($role) {
            $query->where('role_id', $role->id);
        }, 'orderParticipants.user']);

        $participants = $order->orderParticipants;

        $userData = [];

        foreach ($participants as $participant) {
            $userData[] = [
                'id' => $participant->id,
                'image' => $participant->user->photo_path ? URL::route('image', ['path' => $participant->user->photo_path]) : null,
                'first_name' => $participant->user->encrypted_first_name ? Crypt::decryptString($participant->user->encrypted_first_name) : null,
                'last_name' => $participant->user->last_name,
                'purpose_tips' => $participant->user->purpose_tips
            ];
        }

        return $userData;
    }

    private function updateOrderAsOpen($order)
    {
        $order->update([
            'is_open' => true
        ]);
    }

    private function prepareData($organization, $order, $orderItems, $orderParticipants, $settings)
    {
        return [
            'organization' => $organization,
            'order' => $order,
            'orderItems' => $this->getOrderItems($orderItems),
            'orderParticipants' => $orderParticipants,
            'settings' => $settings,
        ];
    }

    private function getOrderItems($orderItems)
    {
        $products = [];

        foreach ($orderItems as $tem) {
            $products[] = [
                'id' => $tem->product->id,
                'name' => $tem->product->name,
                'quantity' => $tem->quantity,
                'price' => $tem->discounted_total,
                'image' => $tem->product->image ? URL::route('image', ['path' => $tem->product->image]) : null
            ];
        }

        return $products;
    }

    public function makePayment(Request $request, VbrRepository $vbr, TochkaRepository $repository)
    {
        return $repository->createOrder($request);
//        return $vbr->createOrder($request);
    }

    private function getOrder($orderId)
    {
        return Order::findOrFail($orderId);
    }

    private function calculateTotalAmount(Order $order, Request $request)
    {
        $tipAmount = $this->getTipAmount($request);

        $commissionAmount = $request->input('commissionAmount', 0);

        return ($order->total_amount + $commissionAmount + $tipAmount) * 100;
    }

    private function getTipAmount(Request $request)
    {
        return $request->has('tip') ? $request->input('tip.amount') : 0;
    }

    private function preparePaymentData(Order $order, float $totalAmount, $tips, $commission)
    {
        return [
            'orderNumber' => $order->id . '-' . random_int(100, 999),
            'amount' => $totalAmount,
            'orderItems' => $this->getBasketResultTotal($order->orderItems, $tips, $commission),
        ];
    }

    private function handlePaymentResponse(Order $order, array $paymentResponse)
    {
        if ($paymentResponse['success']) {
            $order->update(['status' => 'paid']);
            return $paymentResponse;
        } else {
            return response()->json(['error' => $paymentResponse['error']], 400);
        }
    }

    private function getBasketResultTotal($orderItems, $tips = 0, $commission = 0)
    {
        $merchantOrder = $this->getBasketMerchant($orderItems);
        $myOrder = $this->getBasketResultMy($tips, $commission);

        return array_merge($merchantOrder, $myOrder);
    }

    public function calculatePrices(array $orderData)
    {
        $discountPercentage = Setting::where('key', 'acquiring_fee')->first()->value;
        $discountAmount = 0;

        foreach ($orderData['orderItems'] as &$item) {
            if ($item['name'] === 'Merchant') {
                $discountAmount = $item['itemPrice'] * ($discountPercentage / 100);
                $item['itemPrice'] = round($item['itemPrice'] - $discountAmount, 1) * 100;
            } elseif ($item['name'] === 'MyOrganization') {
                $item['itemPrice'] = round($item['itemPrice'] + $discountAmount, 1) * 100;
            }
        }

        return $orderData;
    }


    public function getBasketMerchant($orderItems)
    {
        $order = [
            'positionId' => 1,
            'name' => 'Merchant',
            'itemAttributes' => $this->getMerchantBank(),
            'quantity' => [
                'value' => 1.0,
                'measure' => 'услуга'
            ],
            'itemCode' => null,
            'itemPrice' => 0
        ];

        foreach ($orderItems as $item) {
            $order['itemCode'] = $item['product_id'];
            $order['itemPrice'] += $item['product_price'] * $item['quantity'];
        }

        return [$order];
    }

    public function getBasketResultMy($tips = 0, $commission = 0)
    {
        $totalAmount = $tips + $commission;

        $order = [
            'positionId' => 2,
            'name' => 'MyOrganization',
            'itemDetails' => $this->getMyOrganizationBank(),
            'quantity' => [
                'value' => 1.0,
                'measure' => 'услуга'
            ],
            'itemCode' => 5,
            'itemPrice' => $totalAmount
        ];

        return [$order];
    }

    private function getMerchantBank()
    {
        $selectedOrganizationId = Session::get('selected_organization_id');
        $organization = Organization::find($selectedOrganizationId);
        dd($organization);

        return [
            "attributes" => [
//                [
//                    "name" => "supplier_info.payerName",
//                    "value" => "Иванов Иван Иванович"
//                ],
//                [
//                    "name" => "supplier_info.documentId",
//                    "value" => "3fe896b5-f064-4662-8143-c48fd68a5b33"
//                ],
//                [
//                    "name" => "supplier_info.payerLs",
//                    "value" => "20ОХ995870-01-3913"
//                ],
//                [
//                    "name" => "supplier_info.ls",
//                    "value" => "20ОХ995870-01-3913"
//                ],
                [
                    "name" => "supplier_info.bankBic",
                    "value" => "1122334455"
                ],
                [
                    "name" => "supplier_info.bankName",
                    "value" => "TEST_bank13"
                ],
                [
                    "name" => "supplier_info.inn",
                    "value" => "1234567890"
                ],
                [
                    "name" => "supplier_info.commission",
                    "value" => "400"
                ],
                [
                    "name" => "supplier_info.kpp",
                    "value" => 123456789
                ],
                [
                    "name" => "supplier_info.rs",
                    "value" => "49712810218350099999"
                ],
                [
                    "name" => "supplier_info.name",
                    "value" => "Своя организация"
                ]
            ]
        ];
    }

    private function getMyOrganizationBank()
    {
        return [
            'itemDetailsParams' => [
                [
                    'name' => 'providerid',
                    'value' => '1'
                ],
                [
                    'name' => 'accountnumber',
                    'value' => '20ОХ995870-01-3913'
                ],
                [
                    'name' => 'servicename',
                    'value' => ''
                ]
            ]
        ];
    }































//    public function getBasketResultFinal($orderItems)
//    {
//        $order = [];
//
//        foreach ($orderItems as $index => $item) {
//            $order[] = [
//                'positionId' => $index + 1,
//                'name' => $item['product_name'],
//                'itemDetails' => [
//                    'itemDetailsParams' => [
//                        [
//                            'name' => 'providerid',
//                            'value' => '1'
//                        ],
//                        [
//                            'name' => 'accountnumber',
//                            'value' => '20ОХ995870-01-3913'
//                        ],
//                        [
//                            'name' => 'servicename',
//                            'value' => ''
//                        ]
//                    ]
//                ],
//                'quantity' => [
//                    'value' => $item['quantity'],
//                    'measure' => 'услуга'
//                ],
//                'itemCode' => $item['product_id'],
//                'itemPrice' => $item['product_price'] * 100,
//                'itemAttributes' => [
//                    'attributes' => [
//                        [
//                            'name' => 'supplier_info.bankBic',
//                            'value' => '12345678901'
//                        ],
//                        [
//                            'name' => 'supplier_info.bankName',
//                            'value' => 'TEST_bank4'
//                        ],
//                        [
//                            'name' => 'supplier_info.rs',
//                            'value' => '09876543210987654321'
//                        ]
//                    ]
//                ]
//            ];
//        }
//
//        $order[] = [
//            'positionId' => count($order) + 1,
//            'name' => 'Чаевые',
//            'quantity' => [
//                'value' => 1,
//                'measure' => 'услуга'
//            ],
//            'itemCode' => 'tip',
//            'itemPrice' => 2550 * 100,
//        ];
//
//        $order[] = [
//            'positionId' => count($order) + 1,
//            'name' => 'Комиссия сервиса',
//            'quantity' => [
//                'value' => 1,
//                'measure' => 'услуга'
//            ],
//            'itemCode' => 'commission',
//            'itemPrice' => 489 * 100,
//        ];
//
//        return $order;
//    }

    public function makePaymentTest(Request $request)
    {
//        $order = Order::findOrFail($request->orderId);
//
        $vbr = new VbrrService();
//
//        $vbr->registerPayment(115000, 313      ,  $this->getBasketResultTest());

        $vbr->registerP2P();
    }


    public function getBasketResultTest()
    {
        $order = [
            [
                'positionId' => 1,
                'name' => 'Услуга_1',
                'itemDetails' => [
                    'itemDetailsParams' => [
                        [
                            'name' => 'providerid',
                            'value' => '1'
                        ],
                        [
                            'name' => 'accountnumber',
                            'value' => '20ОХ995870-01-3913'
                        ],
                        [
                            'name' => 'servicename',
                            'value' => ''
                        ]
                    ]
                ],
                'quantity' => [
                    'value' => 1.0,
                    'measure' => 'услуга'
                ],
                'itemCode' => 21546,
                'itemPrice' => 99900,
                'itemAttributes' => [
                    'attributes' => [
                        [
                            'name' => 'supplier_info.bankBic',
                            'value' => '12345678901'
                        ],
                        [
                            'name' => 'supplier_info.bankName',
                            'value' => 'TEST_bank4'
                        ],
                        [
                            'name' => 'supplier_info.rs',
                            'value' => '12345678901234567890'
                        ]
                    ]
                ]
            ],
            [
                'positionId' => 2,
                'name' => 'Usluga2',
                'itemDetails' => [
                    'itemDetailsParams' => [
                        [
                            'name' => 'providerid',
                            'value' => '1'
                        ],
                        [
                            'name' => 'accountnumber',
                            'value' => '20ОХ995870-01-4811'
                        ],
                        [
                            'name' => 'servicename',
                            'value' => ''
                        ]
                    ]
                ],
                'quantity' => [
                    'value' => 1.0,
                    'measure' => 'услуга'
                ],
                'itemCode' => 33879,
                'itemPrice' => 15100,
                'itemAttributes' => [
                    'attributes' => [
                        [
                            'name' => 'supplier_info.bankBic',
                            'value' => '10987654321'
                        ],
                        [
                            'name' => 'supplier_info.bankName',
                            'value' => 'TEST_bank7'
                        ],
                        [
                            'name' => 'supplier_info.rs',
                            'value' => '09876543210987654321'
                        ]
                    ]
                ]
            ]
        ];

//            if (isset($item['itemAttributes'])) {
//                $order[$key]['itemAttributes'] = $item['itemAttributes'];
//            }


//            $order[1] = [
//                'positionId' => $positionId + 1,
//                'name' => 'Комиссия',
//                'quantity' => array(
//                    'value' => 1,
//                    'measure' => 'шт.',
//                ),
//                'itemAmount' => 50 * 100,
//                'itemCode' => $positionId + 1,
//                'itemPrice' => 50 * 100,
//            ];
//
//            $order[2] = [
//                'positionId' => $positionId + 2,
//                'name' => 'Чаевые',
//                'quantity' => array(
//                    'value' => 1,
//                    'measure' => 'шт.',
//                ),
//                'itemAmount' => 300 * 100,
//                'itemCode' => $positionId + 2,
//                'itemPrice' => 300 * 100,
//            ];


        return $order;
    }


    public function getBasketResult($items)
    {

        foreach ($items as $key => $item) {

            $positionId = $key + 1;
            $order[$key] = [
                'positionId' => $positionId,
                'name' => $item['product_name'],
                'quantity' => array(
                    'value' => $item['quantity'],
                    'measure' => 'шт.',
                ),
                'itemAmount' => round(($item['product_price'] * 100 * $item['quantity'])),
                'itemCode' => $item['id'] . "." . $positionId,
                'itemPrice' => round(($item['product_price'] * 100 * $item['quantity'])),
            ];

//            if (isset($item['itemAttributes'])) {
//                $order[$key]['itemAttributes'] = $item['itemAttributes'];
//            }
        }

        $order[1] = [
            'positionId' => $positionId + 1,
            'name' => 'Комиссия',
            'quantity' => array(
                'value' => 1,
                'measure' => 'шт.',
            ),
            'itemAmount' => 50 * 100,
            'itemCode' => $positionId + 1,
            'itemPrice' => 50 * 100,
        ];

        $order[2] = [
            'positionId' => $positionId + 2,
            'name' => 'Чаевые',
            'quantity' => array(
                'value' => 1,
                'measure' => 'шт.',
            ),
            'itemAmount' => 300 * 100,
            'itemCode' => $positionId + 2,
            'itemPrice' => 300 * 100,
        ];


        return $order;
    }

    public function PaySuccess(Request $request)
    {
        $orderId = $request->query('order_id');

        $order = Order::find($orderId);
        $orderStatistic = OrderStatistics::where('order_id', $orderId)->first();
        $organization = $order->organization;

        $order->organization->photo_path = $order->organization->photo_path ? URL::route('image', ['path' => $order->organization->photo_path]) : null;
        $order->organization->logo_path = $order->organization->logo_path ? URL::route('image', ['path' => $order->organization->logo_path]) : null;

        return Inertia::render('Client/PaySuccess/Index', compact('order', 'organization', 'orderStatistic'));
    }

    public function saveEmailInPaySuccess(Request $request)
    {
        $order = OrderStatistics::where('order_id', $request->orderId)->where('status', 'ok')->first();

        if ($order) {
            $order->email_receipt = $request->email;
            $order->save();
        }
    }

    public function test(Request $request)
    {
        Log::info(dump($request->all()));
//        return Inertia::render('Client/PaySuccess/Index');
    }
}
