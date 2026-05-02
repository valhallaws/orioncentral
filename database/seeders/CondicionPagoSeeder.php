<?php

namespace Database\Seeders;

use App\Models\CondicionPago;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CondicionPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = config('defaults.condiciones_pago') ?? [];

        foreach($data as $condicion) {
            CondicionPago::updateOrCreate(['dias' => $condicion['dias'], 'is_system' => true], $condicion);
        }
    }
}
