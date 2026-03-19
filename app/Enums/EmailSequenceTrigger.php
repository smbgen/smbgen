<?php

namespace App\Enums;

enum EmailSequenceTrigger: string
{
    case LeadCapture = 'lead_capture';
    case ClientCreated = 'client_created';
    case Manual = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::LeadCapture => 'Lead Capture',
            self::ClientCreated => 'New Client',
            self::Manual => 'Manual Enroll',
        };
    }
}
