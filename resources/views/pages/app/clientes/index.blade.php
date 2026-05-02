<?php

use App\Models\Cliente;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public string $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[On('cliente-guardado')]
    public function updateList()
    {
        // Solo para refrescar la tabla
    }

    public function delete(Cliente $cliente)
    {
        $cliente->delete();
    }

    public function with(): array
    {
        return [
            'clientes' => Cliente::query()
                ->with('domicilios') // Cargamos los domicilios para evitar N+1
                ->when($this->search, function ($query) {
                    $query->where('alias', 'like', '%' . $this->search . '%')
                          ->orWhere('razon_social', 'like', '%' . $this->search . '%')
                          ->orWhere('rfc', 'like', '%' . $this->search . '%');
                })
                ->orderBy('id', 'desc')
                ->paginate(10),
        ];
    }
};
?>
<div>
    <x-ui.card noPadding>
        <x-slot name="header">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-secondary-900 dark:text-neutral-100">
                        Directorio de Clientes
                    </h3>
                    <p class="mt-1 text-sm text-secondary-500 dark:text-neutral-400">
                        Gestiona los clientes, sus domicilios fiscales y contactos.
                    </p>
                </div>
                <x-ui.button variant="primary" wire:click="$dispatch('crear-cliente')">
                    Nuevo Cliente
                </x-ui.button>
            </div>
        </x-slot>

        <div class="p-6 border-b border-secondary-200 dark:border-secondary-800">
            <div class="w-full sm:w-1/3">
                <x-ui.input
                    wire:model.live.debounce.300ms="search"
                    type="search"
                    placeholder="Buscar por RFC, Razón Social o Alias..."
                />
            </div>
        </div>

        <x-ui.table>
            <x-slot name="headerSlot">
                <th class="px-6 py-3 font-medium">Cliente</th>
                <th class="px-6 py-3 font-medium">RFC</th>
                <th class="px-6 py-3 font-medium">Domicilio Fiscal</th>
                <th class="px-6 py-3 font-medium text-right">Acciones</th>
            </x-slot>

            @forelse($clientes as $cliente)
                <tr wire:key="cliente-{{ $cliente->id }}" class="bg-white border-b dark:bg-secondary-900 dark:border-secondary-800 hover:bg-secondary-50 dark:hover:bg-secondary-800/50 transition-colors duration-150">
                    <td class="px-6 py-4">
                        <div class="font-medium text-secondary-900 dark:text-neutral-100">{{ $cliente->razon_social }}</div>
                        <div class="text-xs text-secondary-500 dark:text-neutral-400">Alias: {{ $cliente->alias }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-mono text-sm">{{ $cliente->rfc }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            // Buscamos el domicilio fiscal directamente en la colección en memoria
                            $fiscal = $cliente->domicilios->where('es_fiscal', true)->first();
                        @endphp

                        @if($fiscal)
                            <div class="text-sm">{{ $fiscal->calle }} {{ $fiscal->exterior }}, {{ $fiscal->municipio }}</div>
                            <div class="text-xs text-secondary-500 dark:text-neutral-400">CP: {{ $fiscal->codigo_postal }}</div>
                        @else
                            <x-ui.badge variant="warning" rounded>Sin Domicilio Fiscal</x-ui.badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                        <x-ui.button variant="ghost" size="sm" wire:click="$dispatch('editar-cliente', { cliente: {{ $cliente->id }} })">Editar</x-ui.button>
                        <x-ui.button
                            variant="ghost"
                            size="sm"
                            class="text-danger-500 hover:text-danger-600 dark:text-danger-400"
                            wire:confirm="¿Estás seguro de eliminar el cliente {{ $cliente->alias }}? Esta acción no se puede deshacer."
                            wire:click="delete({{ $cliente->id }})"
                        >
                            Eliminar
                        </x-ui.button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-secondary-500 dark:text-neutral-400">
                        No se encontraron clientes.
                    </td>
                </tr>
            @endforelse
        </x-ui.table>

        @if($clientes->hasPages())
            <div class="p-6 border-t border-secondary-200 dark:border-secondary-800">
                {{ $clientes->links() }}
            </div>
        @endif
    </x-ui.card>

    <!-- Formulario Independiente -->
    <livewire:clientes.form-clientes />
</div>
