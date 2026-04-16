<?php

namespace App\Services\Social\Adapters;

/**
 * Normalised result returned by every platform adapter after a publish call.
 */
final class PublishResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $platformPostId = null,
        public readonly ?string $platformPostUrl = null,
        public readonly ?string $rawResponse = null,
        public readonly ?string $errorCode = null,
        public readonly ?string $errorMessage = null,
    ) {}

    public static function ok(string $platformPostId, ?string $platformPostUrl = null, ?string $rawResponse = null): self
    {
        return new self(
            success: true,
            platformPostId: $platformPostId,
            platformPostUrl: $platformPostUrl,
            rawResponse: $rawResponse,
        );
    }

    public static function fail(string $errorCode, string $errorMessage, ?string $rawResponse = null): self
    {
        return new self(
            success: false,
            rawResponse: $rawResponse,
            errorCode: $errorCode,
            errorMessage: $errorMessage,
        );
    }
}
