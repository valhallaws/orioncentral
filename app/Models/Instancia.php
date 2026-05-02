<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Instancia extends Model
{
    //
    protected $guarded = [];

    public function cliente(): belongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function getConcatenadoAttribute()
    {
        return $this->cliente->concatenado;
    }
}
