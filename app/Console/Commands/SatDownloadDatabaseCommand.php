<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class SatDownloadDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sat:downloadDatabase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Descarga la base de datos del SAT proporcionada por phpcfdi/resources-sat-catalogs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔎 Consultando versión más reciente...');

        $apiUrl = config('services.sat.db_url', 'https://api.github.com/repos/phpcfdi/resources-sat-catalogs/releases/latest');
        $response = Http::get($apiUrl);

        if ($response->failed()) {
            $this->error('❌ No se pudo consultar la API de GitHub.');

            return Command::FAILURE;
        }

        $json = $response->json();
        $latestTag = $json['tag_name'] ?? null;

        if (! $latestTag) {
            $this->error('❌ No se encontró el tag_name en la release.');

            return Command::FAILURE;
        }

        $this->info("🌐 Último tag en GitHub: $latestTag");
        $localTag = env('SAT_LATEST_TAG');

        if ($localTag === $latestTag && File::exists(database_path('catalogs.db'))) {
            $this->info('👌 Ya tienes la última versión. No se descargará nada.');

            return Command::SUCCESS;
        }

        // Buscar el archivo bz2 dentro de assets
        $asset = collect($json['assets'] ?? [])
            ->firstWhere('name', 'catalogs.db.bz2');

        if (! $asset) {
            $this->error('❌ No se encontró el archivo catalogs.db.bz2 en la release.');

            return Command::FAILURE;
        }

        $downloadUrl = $asset['browser_download_url'];
        $tempPath = storage_path('app/catalogs.db.bz2');
        $finalPath = database_path('catalogs.db');

        $this->info('📥 Descargando archivo catalogs.db.bz2...');

        $fileRequest = Http::withOptions(['stream' => true])->get($downloadUrl);

        if ($fileRequest->failed()) {
            $this->error('❌ Error al descargar el archivo.');

            return Command::FAILURE;
        }

        // Guardar archivo bz2
        File::put($tempPath, $fileRequest->body());

        File::delete($finalPath);

        $this->info('📦 Descomprimiendo archivo...');

        $this->decompressBz2($tempPath, $finalPath);

        // Borrar archivo temporal
        File::delete($tempPath);

        // Actualizar el .env
        $this->updateEnvTag($latestTag);

        $this->info('✅ Base de datos de catálogos actualizada correctamente.');

        return Command::SUCCESS;
    }

    private function decompressBz2(string $source, string $destination)
    {
        $bz = bzopen($source, 'r');

        if (! $bz) {
            throw new \Exception('No se pudo abrir el archivo .bz2');
        }

        $outFile = fopen($destination, 'c+');

        while (! feof($bz)) {
            fwrite($outFile, bzread($bz, 4096));
        }

        fclose($outFile);
        bzclose($bz);
    }

    private function updateEnvTag(string $tag)
    {
        $path = base_path('.env');
        $content = file_get_contents($path);

        $content = preg_replace(
            '/^SAT_LATEST_TAG=.*$/m',
            "SAT_LATEST_TAG={$tag}",
            $content
        );

        file_put_contents($path, $content);
    }
}
