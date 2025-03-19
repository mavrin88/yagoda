<?php

namespace App\Modules\YagodaTips\Models;

use App\Modules\YagodaTips\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatistics extends Model
{
    use HasFactory;

    protected $table = 'tips_order_statistics';

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
