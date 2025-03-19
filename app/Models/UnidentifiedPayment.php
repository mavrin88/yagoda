<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidentifiedPayment extends Model
{
    protected $fillable = [
        'payment_id',
        'type',
        'status',
    ];

    protected $casts = [
        'details' => 'array',
        'identification_payment' => 'array',
        'payments_aproved_list' => 'array',
        'unidentified_payments_prepare_data' => 'array',
        'deal_prepare_data' => 'array',
        'deal_data' => 'array'
    ];

    public function deals()
    {
        return $this->hasMany(TochkaDeal::class);
    }
}
