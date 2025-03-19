<?php

namespace App\Modules\YagodaTips\Services;

use App\Modules\YagodaTips\Models\Order;
use Illuminate\Support\Carbon;

class CoordinatorService
{
    /**
     * Получить все заказы
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllOrders()
    {
        return Order::all();
    }

    /**
     * Получить заказы за период и по ID группы
     *
     * @param string|null $startDate Начальная дата периода
     * @param string|null $endDate Конечная дата периода
     * @param int|null $groupId ID группы
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrdersByPeriodAndGroup($startDate = null, $endDate = null, $groupId = null)
    {
        $query = Order::query();

        if ($startDate) {
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        if ($groupId) {
            $query->where('group_id', $groupId);
        }

        return $query->get();
    }

    /**
     * Получить общую сумму чаевых у оплаченных заказов
     *
     * @param string|null $startDate Начальная дата периода
     * @param string|null $endDate Конечная дата периода
     * @param int|null $groupId ID группы
     * @return float
     */
    public function getTotalTipsForPaidOrders($startDate = null, $endDate = null, $groupId = null)
    {
        $query = Order::query()->where('status', 'ok');

        if ($startDate) {
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        if ($groupId) {
            $query->where('group_id', $groupId);
        }

        return $query->sum('tips');
    }

    /**
     * Получить общее количество оплаченных заказов
     *
     * @param string|null $startDate Начальная дата периода
     * @param string|null $endDate Конечная дата периода
     * @param int|null $groupId ID группы
     * @return int
     */
    public function getPaidOrdersCount($startDate = null, $endDate = null, $groupId = null)
    {
        $query = Order::query()->where('status', 'ok');

        if ($startDate) {
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        if ($groupId) {
            $query->where('group_id', $groupId);
        }

        return $query->count();
    }

    public function getOrderParticipantsByPeriodAndGroup($startDate = null, $endDate = null, $groupId = null)
    {
        $query = Order::query()->with(['orderParticipants', 'orderStatistics'])->where('status', 'ok');

        if ($startDate) {
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        if ($groupId) {
            $query->where('group_id', $groupId);
        }

        $orders = $query->get();

        return $orders->pluck('orderParticipants')->flatten()->unique('id');
    }
}
