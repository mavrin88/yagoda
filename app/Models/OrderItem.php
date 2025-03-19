<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * product_price: цена товара без скидки, которая была на момент формирования заказа
     * discounted_total: цена, которая была на момент формирования заказа минус скидка
     */


    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_price',
        'quantity',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getProductPriceAttribute($value)
    {
        return number_format($value, 0, '.', '');
    }
}
