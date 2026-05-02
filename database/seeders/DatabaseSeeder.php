<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Artisan::call('sat:syncData');

        User::updateOrCreate(['username' => 'root'], [
            'username' => 'root',
            'name' => 'Usuario Root',
            'password' => bcrypt('Mirabal2010_!'),
            'email_verified_at' => now(),
            'email' => 'mfdzmirabal@gmail.com',
            'is_active' => true,
        ]);

        $this->call(CondicionPagoSeeder::class);
    }
}
