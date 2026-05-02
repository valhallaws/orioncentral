<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Contactos extends Model
{
    protected $guarded = [];

    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }
}
