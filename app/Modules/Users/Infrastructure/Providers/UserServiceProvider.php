<?php

namespace App\Modules\Users\Infrastructure\Providers;

use App\Modules\Users\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Users\Http\Middleware\RoleMiddleware;
use App\Modules\Users\Infrastructure\Repositories\EloquentUserRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutes();
        $this->loadMigrations();

        $this->app['router']->aliasMiddleware('role', RoleMiddleware::class);
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
