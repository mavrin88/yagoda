<?php

namespace App\Repository;

use App\Models\Category;
use App\Models\Order;
use App\Models\Organization;
use App\Models\Setting;
use App\Services\VbrrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class VbrRepository
{
    protected $vbrService;
    public function __construct(VbrrService $vbrService)
    {
        $this->vbrService = $vbrService;
    }

    public function createOrder(Request $request)
    {
        $order = $this->getOrder($request->orderId);

        $totalAmount = $this->calculateTotalAmount($order, $request);

        $data = $this->preparePaymentData($order, $totalAmount, $request->input('tip.amount', 0), $request->input('commissionAmount', 0));

        $data = $this->calculatePrices($data);

        return $this->vbrService->registerPayment($data);
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

    private function preparePaymentData(Order $order, float $totalAmount, $tips, $commission)
    {
        return [
            'orderNumber' => $order->id . '-' . random_int(100, 999),
            'amount' => $totalAmount,
            'orderItems' => $this->getBasketResultTotal($order->orderItems, $tips, $commission),
        ];
    }

    private function getTipAmount(Request $request)
    {
        return $request->has('tip') ? $request->input('tip.amount') : 0;
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
        $basketMerchant = $this->getBasketMerchant($orderItems);

        $merchantOrder = $basketMerchant;
        $myOrder = $this->getBasketResultMy($tips, $commission, $basketMerchant);

        return array_merge($merchantOrder, $myOrder);
    }

    public function calculatePrices(array $orderData)
    {
        $totalPriceAfterDiscount = 0;
        $discountPercentage = Setting::where('key', 'acquiring_fee')->first()->value;

        foreach ($orderData['orderItems'] as &$item) {
            if (strpos($item['name'], 'Merchant') !== false) {
                $discountedPrice = $item['itemPrice'] * ($discountPercentage / 100);
                $item['itemPrice'] =  ($item['itemPrice'] - $discountedPrice) * 100;
                $totalPriceAfterDiscount += $discountedPrice;
            }

            if ($item['name'] === 'MyOrganization') {
                $item['itemPrice'] = ($item['itemPrice'] + $totalPriceAfterDiscount) * 100;
            }
        }

        return $orderData;
    }


    public function getBasketMerchant($orderItems)
    {
        $baskets = [];
        $positionIdCounter = 1;

        foreach ($orderItems as $orderItem) {
            $categoryId = $orderItem->product->category_id;
            $category = Category::findOrFail($categoryId);
            $billId = $category->bill_id;

            if (!isset($baskets[$billId])) {
                $baskets[$billId] = [
                    'positionId' => $positionIdCounter++,
                    'name' => 'Merchant_' . $billId,
                    'itemAttributes' => $this->getMerchantBank($category->bill),
                    'quantity' => [
                        'value' => 1.0,
                        'measure' => 'услуга'
                    ],
                    'itemCode' => null,
                    'itemPrice' => 0
                ];
            }

            $baskets[$billId]['itemCode'] = $billId . '_' . $orderItem['product_id'];
            $baskets[$billId]['itemPrice'] += $orderItem['product_price'] * $orderItem['quantity'];
        }

        return array_values($baskets);
    }

    public function getBasketResultMy($tips = 0, $commission = 0, $basketMerchant)
    {
        $totalAmount = $tips + $commission;

        $order = [
            'positionId' => count($basketMerchant) + 1,
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

    private function getMerchantBank($bill)
    {
        $selectedOrganizationId = Session::get('selected_organization_id');
        $organization = Organization::find($selectedOrganizationId);
//        dd($organization->bills);

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
//                [
//                    "name" => "supplier_info.bankBic",
//                    "value" => "1122334455"
//                ],
                [
                    "name" => "supplier_info.bankName",
                    "value" => $bill->bank_name
                ],
                [
                    "name" => "supplier_info.inn",
                    "value" => $organization->inn
                ],
//                [
//                    "name" => "supplier_info.commission",
//                    "value" => "400"
//                ],
                [
                    "name" => "supplier_info.kpp",
                    "value" => $organization->kpp
                ],
                [
                    "name" => "supplier_info.rs",
                    "value" => $bill->number
                ],
                [
                    "name" => "supplier_info.name",
                    "value" => $organization->name
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
}
