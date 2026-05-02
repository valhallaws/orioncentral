<?php

namespace App\Rules;

use App\Models\CondicionPago;
use App\Models\SAT\SatFormasPagos;
use App\Models\SAT\SatMetodosPagos;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class SatPagoRule implements ValidationRule, DataAwareRule
{
    protected array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $formaPago = SatFormasPagos::find(data_get($this->data, 'model.forma_pago_id'));
        $metodoPago = SatMetodosPagos::find(data_get($this->data, 'model.metodo_pago_id'));

        if (!$formaPago || !$metodoPago) {
            return;
        }

        $formaEsPorDefinir = $formaPago->clave === '99';
        $metodoEsPue = $metodoPago->clave === 'PUE';
        $metodoEsPpd = $metodoPago->clave === 'PPD';

        if ($formaEsPorDefinir && !$metodoEsPpd) {
            $fail('La forma de pago 99 - Por definir requiere el método de pago PPD.');
            return;
        }

        if ($metodoEsPue && $formaEsPorDefinir) {
            $fail('El método PUE no puede tener forma de pago 99 - Por definir.');
            return;
        }

        if ($metodoEsPpd && !$formaEsPorDefinir) {
            $fail('El método PPD debe tener forma de pago 99 - Por definir.');
        }
    }
}
