<?php

namespace App\Modules\Containers\Infrastructure\Providers;

use App\Modules\Containers\Domain\Repositories\ContainerRepositoryInterface;
use App\Modules\Containers\Infrastructure\Repositories\EloquentContainerRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ContainerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ContainerRepositoryInterface::class, EloquentContainerRepository::class);
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
