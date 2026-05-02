<?php

namespace App\Livewire\Instancias;

use App\Jobs\DeployInstanceJob;
use App\Models\Cliente;
use App\Models\Instancia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class FormInstancia extends Component
{
    public bool $showModal = false;
    public bool $isEditing = false;

    public $model;

    public $clientes;

    public function render()
    {
        return view('livewire.instancias.form-instancia');
    }

    #[On('crear-instancia')]
    public function capture(){
        $this->loadClientes();
        $this->loadModel(null);
        $this->showModal = true;
    }

    #[On('editar-instancia')]
    public function open($id) {
        $this->loadClientes();
        $this->loadModel($id);

        $this->showModal = true;
        $this->isEditing = true;
    }

    private function loadClientes(): void {
        $this->clientes = Cliente::orderBy('alias')
            ->get()->pluck('concatenado', 'id')->prepend('Seleccione', '');
    }

    private function loadModel(?int $id): void
    {
        $instancia = Instancia::findOrNew($id)->toArray();

        $this->model = $instancia;
    }

    protected function rules(): array
    {
        return [
            'model.cliente_id' => 'required|exists:clientes,id',
            'model.alias' => 'required|string|unique:instancias,alias,' . ($this->model['id'] ?? null),
            'model.carpeta' => 'required|string|unique:instancias,carpeta,' . ($this->model['id'] ?? null),
            'model.dominio' => 'required|string|unique:instancias,dominio,' . ($this->model['id'] ?? null),
            'model.base_path' => 'required|string',
            'model.repositorio' => 'required|string',
            'model.rama' => 'required|string',
            'model.database_name' => 'required|string',
            'model.database_user' => 'required|string',
            'model.database_password' => 'required|string',
            'model.is_sandbox' => 'boolean'
        ];
    }

    protected function messages(): array
    {
        return [

        ];
    }

    public function updatedModelAlias($value)
    {
        if (!$this->isEditing && $value) {
            $slug = Str::slug($value, '');
            $this->model['carpeta'] = strtolower($slug);
            $this->model['base_path'] = '/var/www/' . strtolower($slug);
            $this->model['database_name'] = strtolower($slug) . '_db';
            $this->model['dominio'] = strtolower($slug) . '.orionerp.com.mx';
        }
    }

    public function save(): void
    {
        $this->validate();

        $instancia = Instancia::find($this->model['id'] ?? null);
        $isPersistant = $instancia !== null;

        DB::beginTransaction();

        try {
            if($isPersistant) {
                $instancia->update($this->model);
                DB::commit();
            } else {
                $this->model['deployed_at'] = now();
                $this->model['estado'] = 'pendiente';

                $newInstance = Instancia::create($this->model);
                DB::commit();

                DeployInstanceJob::dispatch($newInstance);
            }

            $this->dispatch('instancia-guardada');
            $this->reset(['model', 'clientes', 'isEditing', 'showModal']);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
