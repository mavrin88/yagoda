<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'full_name',
        'phone',
        'contact_name',
        'contact_phone',
        'inn',
        'legal_address',
        'registration_number',
        'kpp',
        'acquiring_fee',
        'photo_path',
        'tips_1',
        'tips_2',
        'tips_3',
        'tips_4',
        'logo_path',
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%'.$search.'%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }

    public function activityType()
    {
        return $this->belongsTo(ActivityType::class, 'activity_type_id');
    }

    public function organizationForm()
    {
        return $this->belongsTo(OrganizationForm::class, 'form_id');
    }

    public function categories()
    {
        return $this->belongsTo(Category::class, 'organization_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_user')
            ->withPivot('role');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function organizationReviews(): HasMany
    {
        return $this->hasMany(OrganizationReview::class);
    }

    public function shiftMasters()
    {
        return $this->hasMany(ShiftMaster::class);
    }

    public function areSettingsFilled()
    {
        return !is_null($this->name) &&
            !is_null($this->full_name) &&
            !is_null($this->phone) &&
            !is_null($this->contact_name) &&
            !is_null($this->contact_phone) &&
            !is_null($this->inn) &&
            !is_null($this->legal_address) &&
            !is_null($this->registration_number);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class);
    }
}
