<?php

namespace App\Repository;

use App\Models\Order;
use App\Models\OrderStatistics;
use App\Models\Setting;
use App\Services\TochkaApiService;
use Illuminate\Support\Str;

class TochkaRepository
{
    protected $tochkaService;
    public function __construct(TochkaApiService $tochkaService)
    {
        $this->tochkaService = $tochkaService;
    }

    public function createOrder($request)
    {
        $order = Order::findOrFail($request->orderId);

        $organization = $order->organization;

        $uuid = Str::uuid()->toString();

        $commissionForService = Setting::where('key', 'commission_for_using_the_service')->first()->value;

        $totalAmount = $order->total_amount + $request->tip + $request->commissionAmount;

        if ($request->comp_source == 'tips') {
            $totalAmount = $order->total_amount + $request->tip;

            if ($request->commissionAmount > $request->tip) {
                $totalAmount += $request->commissionAmount;
            }
        }

        OrderStatistics::create([
            'order_id' => $order->id,
            'total_amount' => $order->total_amount,
            'full_amount' => $totalAmount,
            'service_acquiring_commission' => $request->commissionAmount,
            'fee_consent' => $request->feeConsent,
            'service_commission' => 0,
            'discount' => $request->discount,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'tips_percent' => $request->tip_percent,
            'tips_sum' => $request->tip,
            'uuid' => $uuid,
            'type_pay' => $request->type,
            'acquiring_fee' => $organization->acquiring_fee,
            'master_percentage' => $organization->master_percentage,
            'admin_percentage' => $organization->admin_percentage,
            'staff_percentage' => $organization->staff_percentage,
            'organization_percentage' => $organization->organization_percentage,
            'commission_for_using_the_service' => $commissionForService,
            'email_receipt' => $request->email_receipt,
            'source_of_compensation' => $organization->comp_source,
        ]);

        $order->update(['full_amount' => $totalAmount]);

        $paymentData = [
            'customerCode' => '304744832',
            'amount' => number_format($totalAmount, 2, '.', ''),
            'purpose' => $uuid, // Уникальный идентификатор
            'redirectUrl' => 'https://yagoda.team/PaySuccess?order_id=' . $order->id,
            'failRedirectUrl' => 'https://example.com/fail',
            'paymentMode' => [$request->type],
            'saveCard' => true,
            'consumerId' => $uuid,
        ];

        return $this->tochkaService->createPayment($paymentData);
    }
}
