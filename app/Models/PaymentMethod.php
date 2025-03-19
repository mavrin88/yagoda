<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    public const METHOD_CASH = 'cash';
    public const METHOD_CARD = 'card';
    public const METHOD_SBP = 'sbp';

    public static function getPaymentMethods(): array
    {
        return [
            self::METHOD_CASH => 'Наличные',
            self::METHOD_CARD => 'Карта',
            self::METHOD_SBP => 'СБП',
        ];
    }


    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
