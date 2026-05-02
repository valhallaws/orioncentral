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
                    <x-ui.select wire:model="model.cliente_id" label="Cliente Asociado" :options="$clientes" />
                </div>

                <x-ui.input wire:model.live.debounce.500ms="model.alias" label="Alias (Identificador)" placeholder="Ej. Sucursal Norte" />

                <div class="flex items-center mt-6">
                    <label class="flex items-center space-x-2 text-sm text-secondary-700 dark:text-neutral-300">
                        <input type="checkbox" wire:model="model.is_sandbox" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:ring-primary-500 bg-white dark:bg-secondary-900">
                        <span>Es un entorno Sandbox (Pruebas)</span>
                    </label>
                </div>

                <x-ui.input wire:model="model.dominio" label="Dominio (URL)" placeholder="ejemplo.orionerp.com" />
                <x-ui.input wire:model="model.base_path" label="Directorio (Se creará en /var/www/)" />

                <div class="md:col-span-2 border-t border-secondary-200 dark:border-secondary-800 pt-4 mt-2">
                    <h4 class="text-sm font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wider mb-4">Configuración Git & Base de Datos</h4>
                </div>

                <x-ui.input wire:model="model.repositorio" label="Repositorio Git (SSH)" placeholder="git@github.com:usuario/repo.git" />
                <x-ui.input wire:model="model.rama" label="Rama (Branch)" placeholder="main" />

                <x-ui.input wire:model="model.database_name" label="Nombre de BD MySQL" />
                <div class="flex gap-2">
                    <div class="w-1/2">
                        <x-ui.input wire:model="model.database_user" label="Usuario DB" />
                    </div>
                    <div class="w-1/2">
                        <x-ui.input wire:model="model.database_password" label="Contraseña DB" />
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
