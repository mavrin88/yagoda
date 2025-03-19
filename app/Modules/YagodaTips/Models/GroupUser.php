<?php

namespace App\Modules\YagodaTips\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
    protected $table = 'tips_group_user';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
