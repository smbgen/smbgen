<?php

namespace App\Modules\CleanSlate\Enums;

enum SubscriptionTier: string
{
    case Basic        = 'basic';
    case Professional = 'professional';
    case Executive    = 'executive';

    public function label(): string
    {
        return match ($this) {
            self::Basic        => 'Basic',
            self::Professional => 'Professional',
            self::Executive    => 'Executive',
        };
    }

    public function priceMonthly(): int
    {
        return match ($this) {
            self::Basic        => 9900,
            self::Professional => 24900,
            self::Executive    => 49900,
        };
    }

    public function stripePriceId(): ?string
    {
        return config("cleanslate.stripe.prices.{$this->value}");
    }
}
