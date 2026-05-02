<?php

namespace App\Models\SAT;

use Illuminate\Database\Eloquent\Model;

class SatRegimenesFiscales extends Model
{
    public function getConcatenadoAttribute()
    {
        return "{$this->clave} - {$this->descripcion}";
    }
}
