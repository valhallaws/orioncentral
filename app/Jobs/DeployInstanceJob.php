<?php

namespace App\Jobs;

use App\Models\Instancia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;

class DeployInstanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Instancia $instancia)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $process = config('app.debug') ? 'bash ' . base_path('dummyDeploy.sh') : 'sudo deploy-sitio';

        $this->instancia->update(['estado' => 'instalando']);

        try {
            $result = Process::run("{$process} " .
                "{$this->instancia->alias} " .
                "{$this->instancia->base_path} " .
                "{$this->instancia->dominio} " .
                "{$this->instancia->repositorio} " .
                "{$this->instancia->rama} " .
                "{$this->instancia->database_user} " .
                "{$this->instancia->database_name} " .
                "{$this->instancia->database_password}"
            );

            if($result->failed()) {
                $this->instancia->update(['estado' => 'pendiente']);
                return;
            }

            $this->instancia->update(['estado' => 'activo', 'deployed_at' => now()]);
        } catch (\Throwable $e) {
            $this->instancia->update(['estado' => 'pendiente']);
        }
    }
}
