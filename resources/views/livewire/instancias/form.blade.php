<?php

use Livewire\Component;
use App\Models\Instancia;
use App\Models\Cliente;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Illuminate\Support\Str;

new class extends Component {
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?Instancia $instancia = null;

    #[Validate('required|integer|exists:clientes,id')]
    public ?int $cliente_id = null;

    #[Validate('required|string|max:255')]
    public string $alias = '';

    #[Validate('required|string|max:255')]
    public string $carpeta = '';

    #[Validate('required|string|max:255')]
    public string $dominio = '';

    #[Validate('required|string|max:255')]
    public string $repositorio = '';

    #[Validate('required|string|max:255')]
    public string $rama = 'main';

    #[Validate('required|string|max:255')]
    public string $database_name = '';

    #[Validate('required|string|max:255')]
    public string $database_user = 'orion';

    #[Validate('required|string|max:255')]
    public string $database_password = '';

    #[Validate('boolean')]
    public bool $is_sandbox = false;

    // Load available clients for the dropdown
    public $clientes = [];

    private function loadClientes()
    {
        $this->clientes = Cliente::orderBy('razon_social')->get()->pluck('razon_social', 'id')->prepend('Seleccione', '');
    }

    // Auto-generate some fields when alias changes
    public function updatedAlias($value)
    {
        if (!$this->isEditing && $value) {
            $slug = Str::slug($value, '');
            $this->carpeta = strtolower($slug);
            $this->database_name = strtolower($slug) . '_db';
            $this->dominio = strtolower($slug) . '.orionerp.com';
        }
    }

    #[On('crear-instancia')]
    public function create()
    {
        $this->loadClientes();
        $this->isEditing = false;
        $this->instancia = null;
        $this->reset([
            'cliente_id', 'alias', 'carpeta', 'dominio', 'repositorio',
            'rama', 'database_name', 'database_password', 'is_sandbox'
        ]);
        $this->rama = 'main';
        $this->database_user = 'orion';
        $this->database_password = Str::random(12); // Contraseña segura para BD
        $this->resetValidation();
        $this->showModal = true;
    }

    #[On('editar-instancia')]
    public function edit(Instancia $instancia)
    {
        $this->loadClientes();
        $this->isEditing = true;
        $this->resetValidation();

        $this->instancia = $instancia;
        $this->cliente_id = $instancia->cliente_id;
        $this->alias = $instancia->alias;
        $this->carpeta = $instancia->carpeta;
        $this->dominio = $instancia->dominio;
        $this->repositorio = $instancia->repositorio;
        $this->rama = $instancia->rama;
        $this->database_name = $instancia->database_name;
        $this->database_user = $instancia->database_user;
        $this->database_password = $instancia->database_password;
        $this->is_sandbox = $instancia->is_sandbox;

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'cliente_id' => $this->cliente_id,
            'alias' => $this->alias,
            'carpeta' => $this->carpeta,
            'dominio' => $this->dominio,
            'repositorio' => $this->repositorio,
            'rama' => $this->rama,
            'database_name' => $this->database_name,
            'database_user' => $this->database_user,
            'database_password' => $this->database_password,
            'is_sandbox' => $this->is_sandbox,
            'estado' => 'pendiente', // O un estatus 'pendiente' hasta que el Job termine
            'fecha_instalacion' => $this-
        ];

        if ($this->instancia) {
            $this->instancia->update($data);
        } else {
            $instancia = Instancia::create($data);

            // Aquí lanzaríamos el Job en el futuro:
            // DeployInstanceJob::dispatch($instancia);
        }

        $this->showModal = false;
        $this->dispatch('instancia-guardada');
    }
};
?>
<div>
    <x-ui.modal wire:model="showModal" maxWidth="2xl">
        <form wire:submit="save" id="instancia-form">
            <x-slot name="header">
                <h3 class="text-lg font-semibold text-secondary-900 dark:text-neutral-100">
                    {{ $isEditing ? 'Editar Instancia' : 'Desplegar Nueva Instancia' }}
                </h3>
            </x-slot>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <x-ui.select wire:model="cliente_id" label="Cliente Asociado">
                            @foreach($clientes as $id => $razon)
                                <option value="{{ $id }}">{{ $razon }}</option>
                            @endforeach
                        </x-ui.select>
                    </div>

                    <x-ui.input wire:model.live.debounce.500ms="alias" label="Alias (Identificador)" placeholder="Ej. Sucursal Norte" />

                    <div class="flex items-center mt-6">
                        <label class="flex items-center space-x-2 text-sm text-secondary-700 dark:text-neutral-300">
                            <input type="checkbox" wire:model="is_sandbox" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:ring-primary-500 bg-white dark:bg-secondary-900">
                            <span>Es un entorno Sandbox (Pruebas)</span>
                        </label>
                    </div>

                    <x-ui.input wire:model="dominio" label="Dominio (URL)" placeholder="ejemplo.orionerp.com" />
                    <x-ui.input wire:model="carpeta" label="Directorio (Se creará en /var/www/)" />

                    <div class="md:col-span-2 border-t border-secondary-200 dark:border-secondary-800 pt-4 mt-2">
                        <h4 class="text-sm font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wider mb-4">Configuración Git & Base de Datos</h4>
                    </div>

                    <x-ui.input wire:model="repositorio" label="Repositorio Git (SSH)" placeholder="git@github.com:usuario/repo.git" />
                    <x-ui.input wire:model="rama" label="Rama (Branch)" placeholder="main" />

                    <x-ui.input wire:model="database_name" label="Nombre de BD MySQL" />
                    <div class="flex gap-2">
                        <div class="w-1/2">
                            <x-ui.input wire:model="database_user" label="Usuario DB" />
                        </div>
                        <div class="w-1/2">
                            <x-ui.input wire:model="database_password" label="Contraseña DB" />
                        </div>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end space-x-3">
                    <x-ui.button variant="ghost" type="button" wire:click="$set('showModal', false)">Cancelar</x-ui.button>
                    <x-ui.button variant="primary" type="submit" form="instancia-form">
                        {{ $isEditing ? 'Actualizar' : 'Guardar e Iniciar Deploy' }}
                    </x-ui.button>
                </div>
            </x-slot>
        </form>
    </x-ui.modal>
</div>
