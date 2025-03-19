<?php

namespace App\Modules\YagodaTips\Services;

use App\Models\User;
use App\Models\Role;

class GroupService
{
    public function getUserGroups(User $user)
    {
        return $user->groups->map(function ($group) use ($user) {
            return [
                'organization_id' => $group->id,
                'organization_name' => $group->full_name,
                'organization_hide' => $group->hide,
                'organization_status' => $group->status,
                'role_id' => $group->pivot->role_id,
                'role_status' => $group->pivot->status,
                'role_name' => Role::find($group->pivot->role_id)->name,
                'tips' => true,
                'team' => $group->team,
            ];
        });
    }
}