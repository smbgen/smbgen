<?php

namespace App\Modules\SaasProductModule\Enums;

enum RemovalStatus: string
{
    case Pending    = 'pending';
    case Submitted  = 'submitted';
    case Confirmed  = 'confirmed';
    case Failed     = 'failed';
    case Skipped    = 'skipped';
}
