<?php

namespace App\Modules\SaasProductModule\Enums;

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
            self::Basic        => 30000,
            self::Professional => 75000,
            self::Executive    => 150000,
        };
    }

    public function stripePriceId(): ?string
    {
        return config("saasproductmodule.stripe.prices.{$this->value}");
    }
}
