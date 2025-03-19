<?php

namespace App\Modules\YagodaTips\Repository;

use App\Modules\YagodaTips\Services\TochkaApiService;
use App\Modules\YagodaTips\Models\Order;
use App\Modules\YagodaTips\Models\OrderStatistics;
use Illuminate\Support\Str;

class TochkaRepository
{
    protected $customerCode = '304744832';
    protected $tochkaService;

    public function __construct(TochkaApiService $tochkaService)
    {
        $this->tochkaService = $tochkaService;
    }

    public function createOrder($request)
    {
        $order = Order::findOrFail($request->orderId);

        $group = $order->group;

        $uuid = 'tips_' . substr(Str::uuid()->toString(), 0, 31);

        $fullAmount = $request->tip + $request->commissionAmount;

        OrderStatistics::create([
            'order_id' => $request->orderId,
            'full_amount' => $fullAmount,
            'service_acquiring_commission' => $request->commissionAmount,
            'fee_consent' => $request->feeConsent,
            'tips_sum' => $request->tip,
            'uuid' => $uuid,
            'type_pay' => $request->type,
            'master_percentage' => $group->master_percentage,
            'admin_percentage' => $group->admin_percentage,
            'staff_percentage' => $group->staff_percentage,
            'organization_percentage' => $group->organization_percentage,
            'commission_for_using_the_service' => $group->commission_for_using_the_service,
            'email_receipt' => $request->email_receipt ?? '',
        ]);

        $order->update([
            'full_amount' => $fullAmount
        ]);

        $data = [
            'customerCode' => $this->customerCode,
            'amount' => number_format($fullAmount, 2, '.', ''),
            'purpose' => $uuid,
            'redirectUrl' => 'https://yagoda.team/PaySuccess?tips_order_id=' . $request->orderId,
            'failRedirectUrl' => 'https://example.com/fail',
            'paymentMode' => [$request->type],
            'saveCard' => true,
            'consumerId' => $uuid,
        ];

        return $this->tochkaService->createPayment($data);
    }
}
