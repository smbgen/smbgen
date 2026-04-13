<?php

namespace App\Modules\SaasProductModule\Enums;

enum ScanStatus: string
{
    case Pending    = 'pending';
    case Running    = 'running';
    case Completed  = 'completed';
    case Failed     = 'failed';
}
