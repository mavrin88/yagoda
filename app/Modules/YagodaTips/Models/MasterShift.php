<?php

namespace app\Modules\YagodaTips\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterShift extends Model
{
    use HasFactory;

    protected $table = 'tips_master_shifts';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
