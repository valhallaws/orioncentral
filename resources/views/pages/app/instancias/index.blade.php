<?php

use App\Jobs\DeployInstanceJob;
use App\Models\Instancia;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

new #[Layout('layouts.app')]
class extends Component {
    use WithPagination;

    public string $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[On('instancia-guardada')]
    public function updateList()
    {
        // Solo para refrescar la tabla
    }

    public function delete(Instancia $instancia)
    {
        // Aquí se podría despachar un Job para eliminar la instancia del servidor
        $instancia->delete();
    }

    public function deploy(Instancia $instancia)
    {
        // Aquí se podría despachar el Job de despliegue manualmente
        DeployInstanceJob::dispatch($instancia);
    }

    public function with(): array
    {
        return [
            'instancias' => Instancia::query()
                ->with('cliente')
                ->when($this->search, function ($query) {
                    $query->where('alias', 'like', '%' . $this->search . '%')
                        ->orWhere('dominio', 'like', '%' . $this->search . '%');
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
                        Gestión de Instancias
                    </h3>
                    <p class="mt-1 text-sm text-secondary-500 dark:text-neutral-400">
                        Controla los despliegues de los entornos de tus clientes.
                    </p>
                </div>
                <x-ui.button variant="primary" wire:click="$dispatch('crear-instancia', null)">
                    Nueva Instancia
                </x-ui.button>
            </div>
        </x-slot>

        <div class="p-6 border-b border-secondary-200 dark:border-secondary-800">
            <div class="w-full sm:w-1/3">
                <x-ui.input
                    wire:model.live.debounce.300ms="search"
                    type="search"
                    placeholder="Buscar por Dominio o Alias..."
                />
            </div>
        </div>

        <x-ui.table>
            <x-slot name="headerSlot">
                <th class="px-6 py-3 font-medium">Instancia</th>
                <th class="px-6 py-3 font-medium">Cliente</th>
                <th class="px-6 py-3 font-medium">Git</th>
                <th class="px-6 py-3 font-medium">Estado</th>
                <th class="px-6 py-3 font-medium text-right">Acciones</th>
            </x-slot>

            @forelse($instancias as $instancia)
                <tr wire:key="instancia-{{ $instancia->id }}"
                    class="bg-white border-b dark:bg-secondary-900 dark:border-secondary-800 hover:bg-secondary-50 dark:hover:bg-secondary-800/50 transition-colors duration-150">
                    <td class="px-6 py-4">
                        <div class="font-medium text-secondary-900 dark:text-neutral-100">{{ $instancia->alias }}</div>
                        <a href="http://{{ $instancia->dominio }}" target="_blank"
                           class="text-xs text-primary-600 dark:text-primary-400 hover:underline">{{ $instancia->dominio }}</a>
                    </td>
                    <td class="px-6 py-4">
                        {{ $instancia->cliente->razon_social ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-mono">{{ $instancia->repositorio }}</div>
                        <div class="text-xs text-secondary-500 dark:text-neutral-400">Rama: <span
                                class="font-semibold">{{ $instancia->rama }}</span></div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $st = $instancia->estado;
                            $variant = match($st) {
                                'instalando' => 'primary',
                                'pendiente' => 'warning',
                                'activo' => 'success',
                                'suspendido' => 'danger',
                                default => 'secondary'
                            };
                        @endphp
                        <x-ui.badge wire:poll.5s
                            :variant="$instancia->estado === 'activo' ? 'success' : ($instancia->estado === 'suspendido' ? 'danger' : 'warning')"
                            rounded>
                            {{ ucfirst($instancia->estado) }}
                        </x-ui.badge>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                        <x-ui.button variant="primary" size="sm" wire:click="deploy({{ $instancia->id }})">Deploy
                        </x-ui.button>
                        <x-ui.button variant="ghost" size="sm"
                                     wire:click="$dispatch('editar-instancia', { id: {{ $instancia->id }} })">Editar
                        </x-ui.button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-secondary-500 dark:text-neutral-400">
                        No se han creado instancias aún.
                    </td>
                </tr>
            @endforelse
        </x-ui.table>

        @if($instancias->hasPages())
            <div class="p-6 border-t border-secondary-200 dark:border-secondary-800">
                {{ $instancias->links() }}
            </div>
        @endif
    </x-ui.card>

    <!-- Formulario Independiente -->
    <livewire:instancias.form-instancia/>
</div>
