<div>
    <x-ui.modal wire:model="showModal" maxWidth="3xl">
        <form id="formCliente" wire:submit.prevent="save">
            <x-slot name="header">
                <h3 class="text-lg font-semibold text-secondary-900 dark:text-neutral-100">
                    {{ $isEditing ? 'Editar Cliente' : 'Registrar Nuevo Cliente' }}
                </h3>
            </x-slot>

            <div class="space-y-8">
                <!-- SECCIÓN 1: CLIENTE -->
                <section>
                    <h4 class="text-sm font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wider mb-4 border-b border-secondary-200 dark:border-secondary-700 pb-2">
                        1. Datos del Cliente
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-2">
                        <span class="md:col-span-2">
                            <x-ui.input wire:model="model.alias" name="model.alias" label="Alias (Nombre Comercial)" placeholder="Mi Empresa" required/>
                        </span>
                        <span class="col-start-1 col-span-2">
                            <x-ui.input wire:model="model.razon_social" name="model.razon_social" label="Razón Social" placeholder="Empresa S.A. de C.V." required/>
                        </span>
                        <span class="col-span-1">
                            <x-ui.input wire:model="model.rfc" name="model.rfc" label="RFC" placeholder="XAXX010101000" required/>
                        </span>
                        <span class="col-span-1">
                            <x-ui.input wire:model="model.id_tributario" label="ID Tributario" placeholder="Clientes extranjeros"/>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-ui.select :options="$regimenes" name="model.regimen_id" wire:model="model.regimen_id" label="Régimen Fiscal" required />
                        <x-ui.select :options="$usosCfdi" name="model.uso_cfdi_id" wire:model="model.uso_cfdi_id" label="Uso de CFDI" required />
                        <x-ui.select :options="$condicionesPago" name="model.condicion_pago_id" wire:model.live="model.condicion_pago_id" label="Condición Pago" required />
                        <x-ui.select :options="$formasPago" name="model.forma_pago_id" wire:model="model.forma_pago_id" label="Forma Pago" required />
                        <x-ui.select :options="$metodosPago" name="model.metodo_pago_id" wire:model="model.metodo_pago_id" label="Método Pago" required />
                        <x-ui.select :options="$monedas" name="model.moneda_id" wire:model="model.moneda_id" label="Moneda" required />
                    </div>
                </section>

                <!-- SECCIÓN 2: DOMICILIO FISCAL -->
                <section>
                    <h4 class="text-sm font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wider mb-4 border-b border-secondary-200 dark:border-secondary-700 pb-2">
                        2. Domicilio Fiscal
                    </h4>

                    <div class="grid grid-cols-3 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <x-ui.input label="Calle" wire:model="dom.calle" name="dom.calle" required/>
                        </div>
                        <div class="col-span-1">
                            <x-ui.input wire:model="exterior" label="Núm. Ext." />
                        </div>
                        <div class="col-span-1">
                            <x-ui.input wire:model="interior" label="Interior" />
                        </div>
                        <div class="col-span-1">
                            <x-ui.input wire:model="dom.codigo_postal" @blur="$wire.loadColonias($event.target.value)" label="Código Postal" required/>
                        </div>
                        <div class="col-span-2 md:col-span-3">
                            <x-ui.select label="Colonia" wire:model.live="dom.colonia_id" :options="$colonias" required/>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-2">
                        <div class="md:col-span-2">
                            <x-ui.input wire:model="dom.municipio" label="Municipio / Alcaldía" />
                        </div>
                        <x-ui.select wire:model="dom.estado_id" label="Estado" :options="$estados" disabled />
                        <x-ui.select wire:model="dom.pais_id" label="País" :options="$paises" disabled />
                    </div>
                </section>

                <!-- SECCIÓN 3: CONTACTO -->
                <section>
                    <h4 class="text-sm font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wider mb-4 border-b border-secondary-200 dark:border-secondary-700 pb-2">
                        3. Contacto Principal
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui.input wire:model="con.nombre" name="con.nombre" label="Nombre Completo" />
                        <x-ui.input wire:model="con.email" name="con.email" type="email" label="Correo Electrónico" />
                        <x-ui.input wire:model="con.telefono" name="con.telefono" label="Teléfono" />
                    </div>
                </section>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end space-x-3">
                    <x-ui.button variant="ghost" type="button" wire:click="$set('showModal', false)">Cancelar</x-ui.button>
                    <x-ui.button variant="primary" type="submit" form="formCliente">
                        {{ $isEditing ? 'Actualizar Cliente' : 'Guardar Cliente' }}
                    </x-ui.button>
                </div>
            </x-slot>
        </form>
    </x-ui.modal>
</div>
