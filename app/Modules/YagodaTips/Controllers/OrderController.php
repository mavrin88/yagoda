<?php

namespace App\Modules\YagodaTips\Controllers;

use App\Models\Role;
use App\Modules\YagodaTips\Models\MasterShift;
use App\Http\Controllers\Controller;
use App\Modules\YagodaTips\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        $order = Order::create([
            'group_id' => $request->groupId,
            'status' => 'draft',
            'full_amount' => 0,
            'tips' => 0,
            'qr_code_id' => null,
        ]);

        // Добавляем участников заказа (мастера, администратор, сотрудники)
        $this->addMasterParticipants($order, $request);
        $this->addAdminParticipants($order);
        $this->addEmployeeParticipants($order);

        return response()->json([
            'success' => true,
            'orderId' => $order->id,
        ]);
    }

    /**
     * Добавляет участников заказа с ролью мастера.
     */
    protected function addMasterParticipants(Order $order, Request $request)
    {
        $roleMaster = Role::where('slug', 'master')->first();

        $order->orderParticipants()->create([
            'order_id' => $order->id,
            'user_id' => $request['master_id'],
            'role_id' => $roleMaster->id,
        ]);
    }

    /**
     * Добавляет участников заказа с ролью админ.
     */
    protected function addAdminParticipants(Order $order)
    {
        $roleAdmin = Role::where('slug', 'admin')->first();

        $orderShifts = MasterShift::where('group_id', $order->group_id)
            ->where('role_id', $roleAdmin->id)
            ->get();

        foreach ($orderShifts as $orderShift) {
            $order->orderParticipants()->create([
                'order_id' => $order->id,
                'user_id' => $orderShift->user_id,
                'role_id' => $roleAdmin->id,
            ]);
        }
    }

    /**
     * Добавляет участников заказа с ролью сотрудника.
     */
    protected function addEmployeeParticipants(Order $order)
    {
        $roleEmployee = Role::where('slug', 'employee')->first();

        $orderShifts = MasterShift::where('group_id', $order->group_id)
            ->where('role_id', $roleEmployee->id)
            ->get();

        foreach ($orderShifts as $orderShift) {
            $order->orderParticipants()->create([
                'order_id' => $order->id,
                'user_id' => $orderShift->user_id,
                'role_id' => $roleEmployee->id,
            ]);
        }
    }
}
