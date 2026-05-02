<?php

namespace App\Models\SAT;

use Illuminate\Database\Eloquent\Model;

class SatMetodosPagos extends Model
{
    //
    public function getConcatenadoAttribute():string {
        return "{$this->clave} - {$this->descripcion}";
    }
}
