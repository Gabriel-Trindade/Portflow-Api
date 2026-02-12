<?php

namespace App\Modules\Containers\Domain\Enums;

enum ContainerStatus: string
{
    case AwaitingUnloading = 'awaiting_unloading';
    case Unloaded = 'unloaded';
    case InYard = 'in_yard';
    case Released = 'released';
}
