<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarberAvailability extends Model
{
    use HasFactory;

    protected $table = 'barber_availability';
    public $timestamps = false;
}
