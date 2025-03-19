<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationStatus extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_NEW_WITHOUT_SAVE = 'new_without_save';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_STOPED = 'stoped';
    public const STATUS_DELETED = 'deleted';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW => 'Новая',
            self::STATUS_NEW_WITHOUT_SAVE => 'Новая не сохраненная',
            self::STATUS_ACTIVE => 'Активная',
            self::STATUS_STOPED => 'Приостановлена',
            self::STATUS_DELETED => 'Удалена',
        ];
    }

    public function status()
    {
        return $this->hasMany(Organization::class);
    }
}
