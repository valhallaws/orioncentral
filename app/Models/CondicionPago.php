<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CondicionPago extends Model
{
    protected $fillable = ['nombre', 'dias', 'is_system'];

}
