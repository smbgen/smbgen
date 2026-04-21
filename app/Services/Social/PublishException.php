<?php

namespace App\Services\Social;

use RuntimeException;

/**
 * Thrown by platform adapters when a publish attempt fails.
 */
class PublishException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly string $errorCode = 'PUBLISH_FAILED',
        public readonly ?string $rawResponse = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
