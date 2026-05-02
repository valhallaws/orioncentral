<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SatSyncData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sat:syncData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza las tablas del SAT';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $start = microtime(true);

        $this->info('Actualizando la base de datos del SAT...');
        $updateDb = $this->call('sat:downloadDatabase');

        if ($updateDb != Command::SUCCESS) {
            $this->error('No se actualizo la base de datos del SAT.');
            return Command::FAILURE;
        }

        $this->info('Leyendo catalogos del SAT');

        $catalogs = [
            ['tablaSat' => 'cfdi_40_formas_pago', 'tablaOrion' => 'sat_formas_pagos', 'uniqueKey' => 'clave'],
            ['tablaSat' => 'cfdi_40_metodos_pago', 'tablaOrion' => 'sat_metodos_pagos', 'uniqueKey' => 'clave'],
            ['tablaSat' => 'cfdi_40_usos_cfdi', 'tablaOrion' => 'sat_usos_comprobantes', 'uniqueKey' => 'clave'],
            ['tablaSat' => 'cfdi_40_regimenes_fiscales', 'tablaOrion' => 'sat_regimenes_fiscales', 'uniqueKey' => 'clave'],
            ['tablaSat' => 'cfdi_40_claves_unidades', 'tablaOrion' => 'sat_claves_unidades', 'uniqueKey' => 'clave'],
            ['tablaSat' => 'cfdi_40_codigos_postales', 'tablaOrion' => 'sat_codigos_postales', 'uniqueKey' => 'clave'],
            ['tablaSat' => 'cfdi_40_colonias', 'tablaOrion' => 'sat_colonias', 'uniqueKey' => ['colonia', 'codigo_postal']],
            ['tablaSat' => 'cfdi_40_estados', 'tablaOrion' => 'sat_estados', 'uniqueKey' => 'clave'],
            ['tablaSat' => 'cfdi_40_paises', 'tablaOrion' => 'sat_paises', 'uniqueKey' => 'clave'],
            ['tablaSat' => 'cfdi_40_periodicidades', 'tablaOrion' => 'sat_periodicidades', 'uniqueKey' => 'clave'],
            ['tablaSat' => 'cfdi_40_productos_servicios', 'tablaOrion' => 'sat_productos_servicios', 'uniqueKey' => 'clave'],
            ['tablaSat' => 'cfdi_40_tipos_factores', 'tablaOrion' => 'sat_tipos_factores', 'uniqueKey' => 'clave'],
            ['tablaSat' => 'cfdi_40_tipos_relaciones', 'tablaOrion' => 'sat_tipos_relaciones', 'uniqueKey' => 'clave'],
            ['tablaSat' => 'cfdi_40_reglas_tasa_cuota', 'tablaOrion' => 'sat_reglas_tasa_cuotas', 'uniqueKey' => ['tipo', 'valor', 'impuesto', 'factor']],
            ['tablaSat' => 'cfdi_40_monedas', 'tablaOrion' => 'sat_monedas', 'uniqueKey' => 'clave'],
            ['tablaSat' => 'cfdi_40_impuestos', 'tablaOrion' => 'sat_impuestos', 'uniqueKey' => 'clave'],
            ['tablaSat' => 'cfdi_40_tipos_comprobantes', 'tablaOrion' => 'sat_tipos_comprobantes', 'uniqueKey' => 'clave'],
            ['tablaSat' => 'nomina_bancos', 'tablaOrion' => 'sat_bancos', 'uniqueKey' => 'clave'],
            ['tablaSat' => 'cce_20_incoterms', 'tablaOrion' => 'sat_incoterms', 'uniqueKey' => 'clave']
        ];

        foreach ($catalogs as $catalog) {
            $this->newLine();

            $this->syncCatalog(
                tablaSat: $catalog['tablaSat'],
                tablaOrion: $catalog['tablaOrion'],
                uniqueKey: $catalog['uniqueKey']
            );
        }

        $this->newLine();
        $this->info('Sincronización completa');
        $end = microtime(true);
        $elapsedTime = round($end - $start, 2);

        $this->info('Tiempo de ejecución: ' . $elapsedTime . ' segundos');

        return Command::SUCCESS;
    }

    private function syncCatalog(string $tablaSat, string $tablaOrion, $uniqueKey) {
        $this->info("Sincronizando tabla: $tablaSat -> $tablaOrion");

        $chunkSize = 2000;

        $localColumns = DB::getSchemaBuilder()->getColumnListing($tablaOrion);

        $map = $this->columnMap($tablaOrion);

        //Contar registros
        $total = DB::connection('sqlite')->table($tablaSat)->count();
        $this->info("Registros totales: $total");

        if($total == 0) {
            $this->warn("No hay registros en la tabla $tablaSat, no se sincroniza");
            return;
        }

        $qry = DB::connection('sqlite')->table($tablaSat);

        if(is_array($uniqueKey)) {
            foreach($uniqueKey as $key) {
                $qry = $qry->orderBy($key);
            }
        } else {
            $qry = $qry->orderBy($uniqueKey);
        }

        $start = microtime(true);

        $qry->chunk($chunkSize, function ($records) use ($tablaOrion, $uniqueKey, $localColumns, $map) {
            if($tablaOrion == 'sat_reglas_tasa_cuotas')
                $this->info('llego la tabla destino');

            $insertData = $records->map(function ($row) use ($localColumns, $map) {
                $data = collect($row)->map(function ($value, $key) {
                    if($value === '') {
                        $value = null;
                    }

                    $booleanFields = ['aplica_fisica', 'aplica_moral', 'estimulo_frontera', 'traslado', 'retencion'];

                    if(in_array($key, $booleanFields)) {
                        return $value ? 1 : 0;
                    }

                    return $value;
                })->toArray();

                foreach($map as $from => $to) {
                    if(array_key_exists($from, $data)) {
                        $data[$to] = $data[$from];
                        unset($data[$from]);
                    }
                }

                $row = collect($data)
                    ->filter(fn($value, $key) => in_array($key, $localColumns))
                    ->toArray();

                return $row;
            })->toArray();

            DB::table($tablaOrion)->upsert(
                $insertData,
                is_array($uniqueKey) ? $uniqueKey : [$uniqueKey],
            );
        });

        $end = microtime(true);
        $elapsedTime = round($end - $start, 2);

        $tasa = $total / ($elapsedTime == 0 ? 0.001 : $elapsedTime);

        if($tasa > 1000) {
            $tasa = round($tasa / 1000, 2) . 'k';
        } else {
            $tasa = round($tasa, 2);
        }

        $this->info("$tablaSat -> $total registros -> $tablaOrion en: $elapsedTime" . "s ($tasa r/s)");
    }

    private function columnMap(string $tablaOrion): array {
        return match ($tablaOrion) {
            'sat_bancos' => [
                'id' => 'clave',
                'texto' => 'nombre'
            ],
            'sat_monedas', 'sat_claves_unidades', 'sat_formas_pagos', 'sat_metodos_pagos', 'sat_usos_comprobantes',
            'sat_regimenes_fiscales', 'sat_paises', 'sat_periodicidades', 'sat_productos_servicios', 'sat_tipos_relaciones',
            'sat_impuestos', 'sat_tipos_comprobantes', 'sat_incoterms' => [
                'id' => 'clave',
                'texto' => 'descripcion'
            ],
            'sat_colonias' => [
                'colonia' => 'clave',
                'texto' => 'descripcion'
            ],
            'sat_codigos_postales', 'sat_tipos_factores' => [
                'id' => 'clave',
            ],
            'sat_estados' => [
                'estado' => 'clave',
                'texto' => 'descripcion'
            ],

            default => []
        };
    }
}
