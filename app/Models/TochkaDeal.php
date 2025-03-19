<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TochkaDeal extends Model
{
    protected $casts = [
        'deal_prepare_data' => 'array',
        'deal_data' => 'array'
    ];

    public function unidentifiedPayments()
    {
        return $this->belongsTo(UnidentifiedPayment::class);
    }
}
