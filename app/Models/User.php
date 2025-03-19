<?php

namespace App\Models;

use App\Modules\YagodaTips\Models\Group;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'owner' => 'boolean',
            'email_verified_at' => 'datetime',
        ];
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function getNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::needsRehash($password) ? Hash::make($password) : $password;
    }

    public function isDemoUser()
    {
        return $this->email === 'johndoe@example.com';
    }

    public function scopeOrderByName($query)
    {
        $query->orderBy('last_name')->orderBy('first_name');
    }

    public function scopeWhereRole($query, $role)
    {
        switch ($role) {
            case 'user': return $query->where('owner', false);
            case 'owner': return $query->where('owner', true);
        }
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        })->when($filters['role'] ?? null, function ($query, $role) {
            $query->whereRole($role);
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_user')
            ->withPivot('role_id', 'status');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'tips_group_user')
            ->withPivot('role_id', 'status');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'organization_user');
    }

//    public function scopeAdministrators($query, $organizationId)
//    {
//        return $query->whereHas('roles', function ($query) {
//            $query->where('slug', 'admin');
//        })
//            ->whereHas('organizations', function ($query) use ($organizationId) {
//                $query->where('organization_id', $organizationId);
//            })
//            ->get(['id', 'first_name', 'last_name', 'phone', 'photo_path', 'encrypted_first_name']);
//    }
//
//    public function scopeMasters($query, $organizationId)
//    {
//        return $query->whereHas('roles', function ($query) {
//            $query->where('slug', 'master');
//        })
//            ->whereHas('organizations', function ($query) use ($organizationId) {
//                $query->where('organization_id', $organizationId);
//            })
//            ->where(function ($query) {
//                $query->where('status', '!=', 'deleted')
//                    ->orWhereNull('status');
//            })
//            ->get(['id', 'first_name', 'last_name', 'phone', 'photo_path', 'encrypted_first_name']);
//    }
//
//    public function scopeStaff($query, $organizationId)
//    {
//        return $query->whereHas('roles', function ($query) {
//            $query->where('slug', 'employee');
//        })
//            ->whereHas('organizations', function ($query) use ($organizationId) {
//                $query->where('organization_id', $organizationId);
//            })
//            ->where(function ($query) {
//                $query->where('status', '!=', 'deleted')
//                    ->orWhereNull('status');
//            })
//            ->get(['id', 'first_name', 'last_name', 'phone', 'photo_path', 'encrypted_first_name']);
//    }



    public function scopeAdministrators($query, $organizationId)
    {
        $organizationUsers = DB::table('organization_user')
            ->where('organization_id', $organizationId)
            ->where('role_id', 3)
            ->where('status', 'active')
            ->get();

        $userIds = $organizationUsers->pluck('user_id');

        return User::whereIn('id', $userIds)->get();
    }

    public function scopeMasters($query, $organizationId)
    {
        $organizationUsers = DB::table('organization_user')
            ->where('organization_id', $organizationId)
            ->where('role_id', 4)
            ->where('status', 'active')
            ->get();

        $userIds = $organizationUsers->pluck('user_id');

        return User::whereIn('id', $userIds)->get();
    }

    public function scopeStaff($query, $organizationId)
    {
        $organizationUsers = DB::table('organization_user')
            ->where('organization_id', $organizationId)
            ->where('role_id', 5)
            ->where('status', 'active')
            ->get();

        $userIds = $organizationUsers->pluck('user_id');

        return User::whereIn('id', $userIds)->get();
    }

    public function scopeTipsAdministrators($query, $organizationId)
    {
        $groupUsers = DB::table('tips_group_user')
            ->where('group_id', $organizationId)
            ->where('role_id', 3)
            ->where('status', 'active')
            ->get();

        $userIds = $groupUsers->pluck('user_id');

        return User::whereIn('id', $userIds)->get();
    }

    public function scopeTipsMasters($query, $organizationId)
    {
        $groupUsers = DB::table('tips_group_user')
            ->where('group_id', $organizationId)
            ->where('role_id', 4)
            ->where('status', 'active')
            ->get();

        $userIds = $groupUsers->pluck('user_id');

        return User::whereIn('id', $userIds)->get();
    }

    public function scopeTipsStaff($query, $organizationId)
    {
        $groupUsers = DB::table('tips_group_user')
            ->where('group_id', $organizationId)
            ->where('role_id', 5)
            ->where('status', 'active')
            ->get();

        $userIds = $groupUsers->pluck('user_id');

        return User::whereIn('id', $userIds)->get();
    }

    public function incrementLoginCount()
    {
        $this->increment('login_count');
    }

    public function orders()
    {
        return $this->hasMany(OrderParticipant::class);
    }

    public function bills(): BelongsToMany
    {
        return $this->belongsToMany(Bill::class, 'user_bill');
    }

    /**
     * Проверка на доступ к организации
     *
     * @param $organizationId
     *
     * @return bool
     */
    public function hasAccessToOrganization($organizationId): bool {
        //todo: Проверка на роль?
        return DB::table('organization_user')
            ->where('user_id', $this->id)
            ->where('organization_id', $organizationId)
            ->exists();
    }

}
