<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    use HasFactory;


    /**
     * total_amount: сумма товаров со скидкой
     * full_amount: сумма товаров со скидкой + сумма чаевых + комиссия
     */



    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function status()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function orderParticipants()
    {
        return $this->hasMany(OrderParticipant::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function qrCode()
    {
        return $this->belongsTo(QrCode::class);
    }

    public function getTotalAmountAttribute($value)
    {
//        return number_format($value, 0, '.', '');
        return $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderStatistics()
    {
        return $this->hasMany(OrderStatistics::class);
    }
    public function scopeFilteredByOrganizationAndStatusAndDate($query, $organizationId, $Date)
    {
        return $query->where('organization_id', $organizationId)
            ->where('status', OrderStatus::STATUS_COMPLETED)
            ->whereDate('created_at', $Date);
    }

    public function scopeFilteredByOrganizationStatusAndDateRange($query, $organizationId, $startDate, $endDate)
    {
        return $query->where('organization_id', $organizationId)
            ->where('status', OrderStatus::STATUS_COMPLETED)
            ->whereBetween('created_at', [$startDate, $endDate]);
    }
    public static function getCompletedOrdersWithStatisticsAndItemsAndDate($date, $organizationId)
    {
        return self::whereDate('created_at', $date)
            ->where('organization_id', $organizationId)
            ->where('status', OrderStatus::STATUS_COMPLETED)
            ->with(['orderStatistics', 'orderItems'])
            ->get();
    }

    public static function getCompletedOrdersWithStatisticsAndItemsAndDateRange($startDate, $endDate, $organizationId)
    {

       return self::whereBetween('created_at', [$startDate, $endDate])
            ->where('organization_id', $organizationId)
            ->where('status', OrderStatus::STATUS_COMPLETED)
            ->with(['orderStatistics', 'orderItems'])
            ->get();

    }


}
