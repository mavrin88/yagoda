<?php

namespace App\Modules\YagodaTips\Models;

use App\Modules\YagodaTips\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;

    protected $table = 'tips_qr_codes';

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
