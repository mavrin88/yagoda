<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'draft';
    public const STATUS_PROCESSING = 'new';
    public const STATUS_COMPLETED = 'ok';
    public const STATUS_CANCELLED = 'cancel';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Черновик',
            self::STATUS_PROCESSING => 'Новый',
            self::STATUS_COMPLETED => 'Оплачен',
            self::STATUS_CANCELLED => 'Отменен',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
