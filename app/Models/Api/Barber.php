<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barber extends Model
{
    use HasFactory;

    protected $table = 'barbers';
    public $timestamps = false;

    /**
     * @param $value
     * @return string
     */
    public function getAvatarAttribute($value): string
    {
        return url("media/avatars/{$value}");
    }
}
