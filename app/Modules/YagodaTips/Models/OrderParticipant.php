<?php

namespace App\Modules\YagodaTips\Models;

use App\Models\User;
use App\Modules\YagodaTips\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderParticipant extends Model
{
    use HasFactory;

    protected $table = 'tips_order_participants';

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
