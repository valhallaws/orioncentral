<?php

namespace App\Models\SAT;

use Illuminate\Database\Eloquent\Model;

class SatFormasPagos extends Model
{
    //
    public function getConcatenadoAttribute():string {
        return "{$this->clave} - {$this->descripcion}";
    }
}
