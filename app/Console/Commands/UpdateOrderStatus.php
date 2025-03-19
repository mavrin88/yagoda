<?php

namespace App\Console\Commands;

use App\Services\TochkaApiService;
use Illuminate\Console\Command;
use App\Models\OrderStatistics;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Carbon\Carbon;

class UpdateOrderStatus extends Command
{
    protected $signature = 'orders:update-status';

    protected $description = 'Update order status from API';
    protected $customerCode = '304590585';

    public function handle(TochkaApiService $tochkaApiService)
    {
        $today = Carbon::today();

        $data = [
            'customerCode' => $this->customerCode,
            'status' => 'APPROVED',
            'fromDate' => $today->toDateString(),
            'toDate' => $today->toDateString(),
        ];

        $response = $tochkaApiService->getPaymentOperationList($data);

        $orders = $response['Data']['Operation'];

        foreach ($orders as $orderData) {
            $orderStatistics = OrderStatistics::where('uuid', $orderData['purpose'])->where('status', 'new')->first();

            if ($orderStatistics) {
                $order = Order::find($orderStatistics->order_id);
                if ($order) {
                    try {
                        DB::transaction(function () use ($order, $orderStatistics) {
                            $order->update(['status' => 'ok']);
                            $orderStatistics->update(['status' => 'ok']);
                        });
                    } catch (\Exception $e) {
                        \Log::error('Failed to update order status: ' . $e->getMessage());
                    }
                }
            }
        }
    }
}
