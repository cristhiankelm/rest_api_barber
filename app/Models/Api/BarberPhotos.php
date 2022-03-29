<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarberPhotos extends Model
{
    use HasFactory;

    protected $table = 'barber_photos';
    public $timestamps = false;

    public function getUrlAttribute($value): string
    {
        return url("media/avatars/{$value}");
    }
}
