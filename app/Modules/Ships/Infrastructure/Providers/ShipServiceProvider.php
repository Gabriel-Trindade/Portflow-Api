<?php

namespace App\Modules\Ships\Infrastructure\Providers;

use App\Modules\Ships\Domain\Repositories\ShipRepositoryInterface;
use App\Modules\Ships\Infrastructure\Repositories\EloquentShipRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ShipServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ShipRepositoryInterface::class, EloquentShipRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutes();
        $this->loadMigrations();
    }

    private function loadRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->group(__DIR__ . '/../../Routes/api.php');
    }

    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../Database/Migrations');
    }
}
