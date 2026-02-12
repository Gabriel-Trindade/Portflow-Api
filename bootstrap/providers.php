<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Modules\Users\Infrastructure\Providers\UserServiceProvider::class,
    App\Modules\Containers\Infrastructure\Providers\ContainerServiceProvider::class,
    App\Modules\Ships\Infrastructure\Providers\ShipServiceProvider::class,
];

