<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Domicilios extends Model
{
    protected $guarded = [];

    public function domiciliable(): MorphTo
    {
        return $this->morphTo();
    }

    public function contactos(): MorphMany
    {
        return $this->morphMany(Contactos::class, 'contactable');
    }
}
