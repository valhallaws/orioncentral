<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Domicilios;

class Cliente extends Model
{
    /** @use HasFactory<\Database\Factories\ClienteFactory> */
    use HasFactory;

    protected $guarded = [];

    public function getConcatenadoAttribute()
    {
        return $this->alias . ' - ' . $this->razon_social;
    }

    public function domicilios(): MorphMany
    {
        return $this->morphMany(Domicilios::class, 'domiciliable');
    }

    public function contactos(): MorphMany
    {
        return $this->morphMany(Contactos::class, 'contactable');
    }

    public function domicilioFiscal()
    {
        return $this->morphOne(Domicilios::class, 'domiciliable')
            ->where('es_fiscal', true)->first();
    }

    public function instancias(): hasMany
    {
        return $this->hasMany(Instancia::class);
    }
}
