<?php

namespace App\Enums;

enum ManagedSiteStatus: string
{
    case Building = 'building';
    case Active = 'active';
    case Paused = 'paused';
}
