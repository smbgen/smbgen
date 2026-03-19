<?php

namespace App\Enums;

enum SocialPlatform: string
{
    case LinkedIn = 'linkedin';
    case Instagram = 'instagram';
    case X = 'x';
    case Facebook = 'facebook';

    public function label(): string
    {
        return match ($this) {
            self::LinkedIn => 'LinkedIn',
            self::Instagram => 'Instagram',
            self::X => 'X / Twitter',
            self::Facebook => 'Facebook',
        };
    }
}
