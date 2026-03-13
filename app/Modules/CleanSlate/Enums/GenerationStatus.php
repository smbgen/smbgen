<?php

namespace App\Modules\CleanSlate\Enums;

enum GenerationStatus: string
{
    case Queued     = 'queued';
    case Generating = 'generating';
    case Complete   = 'complete';
    case Failed     = 'failed';
}
