<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|Form create(array $attributes = [])
 * @property false|mixed|string $payload
 * @property string $type
 */
class Form extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['type', 'payload'];

}
