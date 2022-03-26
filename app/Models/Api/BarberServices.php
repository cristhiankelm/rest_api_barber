<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarberServices extends Model
{
    use HasFactory;

    protected $table = 'barber_services';
    public $timestamps = false;
}
