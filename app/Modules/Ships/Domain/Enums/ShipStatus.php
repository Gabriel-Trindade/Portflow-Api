<?php

namespace App\Modules\Ships\Domain\Enums;

enum ShipStatus: string
{
    case Scheduled = 'scheduled';
    case Docked = 'docked';
    case Finalized = 'finalized';
}
