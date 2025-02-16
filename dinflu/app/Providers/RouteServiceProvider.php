<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define os caminhos padrão do sistema.
     */
    public const HOME = '/home';

    /**
     * Registra os serviços de rota.
     */
    public function boot(): void
    {
        parent::boot();

        // Carrega as rotas da API
        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('routes/api.php'));

        // Carrega as rotas web (caso esteja usando web também)
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }
}
