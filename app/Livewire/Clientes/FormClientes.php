<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use App\Models\CondicionPago;
use App\Models\Domicilios;
use App\Models\SAT\SatCodigosPostales;
use App\Models\SAT\SatColonias;
use App\Models\SAT\SatEstados;
use App\Models\SAT\SatFormasPagos;
use App\Models\SAT\SatMetodosPagos;
use App\Models\SAT\SatMonedas;
use App\Models\SAT\SatPaises;
use App\Models\SAT\SatRegimenesFiscales;
use App\Models\SAT\SatUsosComprobantes;
use App\Rules\SatPagoRule;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class FormClientes extends Component
{
    public bool $showModal = false;
    public bool $isEditing = false;
    public $model = [], $dom = [], $con = [];

    public $condicionesPago, $formasPago, $regimenes, $metodosPago, $usosCfdi, $monedas, $bancos;
    public $colonias, $estados, $paises;

    private function initCliente(): void
    {
        $this->model = [
            'id' => null,
            'alias' => null,
            'razon_social' => null,
            'rfc' => null,
            'id_tributario' => null,
            'condicion_pago_id' => null,
            'regimen_id' => null,
            'forma_pago_id' => null,
            'metodo_pago_id' => null,
            'uso_cfdi_id' => null,
            'moneda_id' => null,
        ];
            //Domicilio
        $this->dom = [
            'id' => null,
            'alias' => 'Fiscal',
            'calle' => null,
            'exterior' => null,
            'interior' => null,
            'codigo_postal' => null,
            'colonia_id' => '',
            'municipio' => null,
            'estado_id' => null,
            'pais_id' => null,
            'referencia' => null,
            'telefono' => null,
            'es_fiscal' => true,
        ];

        //Contacto
        $this->con = [
            'id' => null,
            'nombre' => null,
            'email' => null,
            'telefono' => null,
        ];
    }

    protected function rules(): array
    {
        return [
            'model.alias' => 'required|unique:clientes,alias,' . $this->model['id'] . ',id',
            'model.razon_social' => 'required|unique:clientes,razon_social,' . $this->model['id'] . ',id',
            'model.rfc' => [
                'required',
                'regex:^([A-Z]{3,4}[0-9]{6}[A-Z0-9]{3})$^'
            ],
            'model.id_tributario' => 'nullable|numeric',
            'model.regimen_id' => 'required',
            'model.condicion_pago_id' => 'required',
            'model.forma_pago_id' => [
                'required',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $condPago = CondicionPago::find($this->model['condicion_pago_id'] ?? null);
                    $formaPago = SatFormasPagos::find($this->model['forma_pago_id'] ?? null);

                    if($condPago->dias === 0 && $formaPago->clave === '99') {
                        $fail('Sin crédito no puede haber forma de pago 99 - Por definir.');
                        return;
                    }

                    if($condPago->dias > 0 && $formaPago->clave !== '99') {
                        $fail('Con crédito debe haber forma de pago 99 - Por definir.');
                    }
                }
            ],
            'model.metodo_pago_id' => [
                'required',
                new SatPagoRule
            ],
            'model.uso_cfdi_id' => 'required',

            'dom.calle' => 'required',
            'dom.exterior' => 'nullable',
            'dom.interior' => 'nullable',
            'dom.codigo_postal' => 'required|string|exists:sat_codigos_postales,clave',
            'dom.colonia_id' => 'required|exists:sat_colonias,id',
            'dom.municipio' => 'required|string',
            'dom.estado_id' => 'required|exists:sat_estados,id',
            'dom.pais_id' => 'required|exists:sat_paises,id',

            'con.nombre' => 'required',
            'con.email' => 'required|email',
            'con.telefono' => 'required|size:10'
        ];
    }

    protected function messages(): array
    {
        return [
            'model.alias.required' => 'El alias es obligatorio',
            'model.alias.unique' => 'El alias ya está en uso',
            'model.razon_social.required' => 'La razón social es obligatoria',
            'model.razon_social.unique' => 'La razón social ya está en uso',
            'model.rfc.required' => 'El RFC es obligatorio',
            'model.rfc.regex' => 'El formato de RFC no es válido',
            'model.regimen_id.required' => 'El régimen fiscal es obligatorio',
            'model.uso_cfdi_id.required' => 'El uso del CFDI es obligatorio',
            'model.metodo_pago_id.required' => 'El método de pago es obligatorio',
            'model.forma_pago_id.required' => 'La forma de pago es obligatoria',
            'model.condicion_pago_id.required' => 'La condición de pago es obligatoria',
            'model.moneda_id.required' => 'La moneda es obligatoria',

            'dom.calle.required' => 'La calle es obligatorio',
            'dom.codigo_postal.required' => 'El código postal es obligatorio',
            'dom.codigo_postal.exists' => 'El código postal no existe en el catálogo',
            'dom.colonia_id.required' => 'La colona es obligatoria',
            'dom.colonia_id.exists' => 'La colonia no existe en el catálogo',
            'dom.municipio.required' => 'El municipio es obligatorio',
            'dom.estado_id.required' => 'El estado es obligatorio',
            'dom.estado_id.exists' => 'El estado no existe en el catálogo',
            'dom.pais_id.required' => 'El país es obligatorio',
            'dom.pais_id.exists' => 'El país no existe',

            'con.nombre.required' => 'El nombre es obligatorio',
            'con.email.required' => 'El email es obligatorio',
            'con.email.email' => 'El email no es válido',
            'con.telefono.required' => 'El teléfono es obligatorio',
            'con.telefono.size' => 'El teléfono debe tener 10 dígitos',
        ];
    }

    public function render()
    {
        return view('livewire.clientes.form-clientes')
            ->layout('layouts.app');
    }

    private function loadCombos(): void
    {
        $this->condicionesPago = CondicionPago::orderBy('dias')->get()
            ->pluck('nombre', 'id')->prepend('Seleccione', '');
        $this->formasPago = SatFormasPagos::orderBy('clave')->get()
            ->pluck('concatenado', 'id')->prepend('Seleccione', '');
        $this->regimenes = SatRegimenesFiscales::orderBy('clave')->get()
            ->pluck('concatenado', 'id')->prepend('Seleccione', '');
        $this->metodosPago = SatMetodosPagos::orderBy('clave')->get()
            ->pluck('concatenado', 'id')->prepend('Seleccione', '');
        $this->usosCfdi = SatUsosComprobantes::orderBy('clave')->get()
            ->pluck('concatenado', 'id')->prepend('Seleccione', '');
        $this->monedas = SatMonedas::whereIn('clave', ['USD', 'MXN'])->orderBy('clave')
            ->get()->pluck('clave', 'id')->prepend('Seleccione', '');

        $this->colonias = collect()->prepend('No existe en el catálogo', );
        $this->estados = SatEstados::where('pais', 'MEX')->orderBy('descripcion')->get()
            ->pluck('clave', 'id')->prepend('Seleccione', '');
        $this->paises = SatPaises::orderBy('clave')->get()
            ->pluck('clave', 'id')->prepend('Seleccione', '');
    }

    public function updatedModelCondicionPagoId($value): void
    {
        $condicion = CondicionPago::find($value);

        if(($condicion?->dias ?? 0) > 0) {
            $this->model['forma_pago_id'] = SatFormasPagos::whereClave('99')->first()->id;
        } else {
            $this->model['forma_pago_id'] = SatFormasPagos::where('clave', '!=', '99')->first()->id;
        }
    }

    public function loadColonias(?string $val): void
    {
        $codigo = $val;

        $this->colonias = SatColonias::where('codigo_postal', $codigo)->orderBy('descripcion')
            ->get()
            ->pluck('descripcion', 'id')->prepend('No existe en el catálogo', '');
    }

    public function loadEstados(): void
    {
        $codigo = SatCodigosPostales::whereClave($this->dom['codigo_postal'])->first();
        $this->dom['estado_id'] = SatEstados::firstWhere('clave', $codigo->estado);
    }

    public function updatedDomColoniaId($val): void
    {
        $colonia = SatColonias::find($val);
        $codigoPostal = SatCodigosPostales::firstWhere('clave', $colonia->codigo_postal);

        $estado = SatEstados::firstWhere('clave', $codigoPostal?->estado);
        $pais = SatPaises::firstWhere('clave', $estado?->pais);

        $this->dom['estado_id'] = (string) $estado?->id ?? '';
        $this->dom['pais_id'] = (string) $pais?->id ?? '';
    }

    #[On('crear-cliente')]
    public function create()
    {
        $this->loadCombos();

        $this->isEditing = false;
        $this->initCliente();

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save() {
        $this->validate();

        if (! isset($this->model['id'])) {
            $this->model['id'] = null;
        }

        if (! isset($this->dom['id'])) {
            $this->dom['id'] = null;
        }

        if (! isset($this->con['id'])) {
            $this->con['id'] = null;
        }

        DB::beginTransaction();

        try {
            $cliente = Cliente::updateOrCreate(['id' => $this->model['id']], collect($this->model)->except('id')->all());

            $domicilio = Domicilios::firstOrCreate(['id' => $this->dom['id']], [
                'id' => $this->dom['id'],
                'domiciliable_type' => Cliente::class,
                'domiciliable_id' => $cliente->id,
                'alias' => $this->dom['alias'],
                'calle' => $this->dom['calle'],
                'exterior' => $this->dom['exterior'],
                'interior' => $this->dom['interior'],
                'codigo_postal' => $this->dom['codigo_postal'],
                'referencia' => $this->dom['referencia'],
                'colonia_id' => $this->dom['colonia_id'],
                'municipio' => $this->dom['municipio'],
                'estado_id' => $this->dom['estado_id'],
                'pais_id' => $this->dom['pais_id'],
                'telefono' => $this->dom['telefono'],
                'es_fiscal' => $this->dom['es_fiscal']
            ]);

            $domicilio->contactos()->firstOrCreate(['id' => $this->con['id']], [
                'id' => $this->con['id'],
                'nombre' => $this->con['nombre'],
                'telefono' => $this->con['telefono'],
                'email' => $this->con['email']
            ]);

            DB::commit();

            $this->showModal = false;
            $this->resetValidation();
            $this->reset();

            $this->dispatch('cliente-guardado');
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }
}
