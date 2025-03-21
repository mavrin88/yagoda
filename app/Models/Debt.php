<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
