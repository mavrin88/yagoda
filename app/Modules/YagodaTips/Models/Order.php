<?php

namespace App\Modules\YagodaTips\Models;

use App\Modules\YagodaTips\Models\Group;
use App\Modules\YagodaTips\Models\OrderStatistics;
use App\Modules\YagodaTips\Models\OrderParticipant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'tips_orders';

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function orderStatistics()
    {
        return $this->hasMany(OrderStatistics::class);
    }

    public function orderParticipants()
    {
        return $this->hasMany(OrderParticipant::class);
    }

    /**
     * Получить всех пользователей с ролью "Мастер" в заказе
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function masters()
    {
        return $this->orderParticipants()
            ->with(['user.roles' => function ($query) {
                $query->where('slug', 'master');
            }])
            ->get()
            ->pluck('user')
            ->filter(function ($user) {
                return $user->roles->isNotEmpty();
            })
            ->values();
    }

//    public function qrCode()
//    {
//        return $this->belongsTo(QrCode::class);
//    }
//
//    public function user()
//    {
//        return $this->belongsTo(User::class);
//    }
}
