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
            self::Basic        => 'Starter',
            self::Professional => 'Pro',
            self::Executive    => 'Agency',
        };
    }

    public function priceMonthly(): int
    {
        return match ($this) {
            self::Basic        => 4900,
            self::Professional => 14900,
            self::Executive    => 39900,
        };
    }

    public function stripePriceId(): ?string
    {
        return config("cleanslate.stripe.prices.{$this->value}");
    }
}
