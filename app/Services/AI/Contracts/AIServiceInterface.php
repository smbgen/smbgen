<?php

namespace App\Services\AI\Contracts;

interface AIServiceInterface
{
    public function isAvailable(): bool;

    public function complete(string $systemPrompt, string $userPrompt, array $options = []): string;

    public function generateBlogPost(string $prompt, ?string $customSystemPrompt = null): string;

    public function generateSEOMetadata(string $title, string $content, ?string $customSystemPrompt = null): array;

    public function improveContent(string $content, ?string $customSystemPrompt = null): string;

    public function generateIndustryVariant(string $content, string $industry, ?string $customSystemPrompt = null): string;

    public function generateLandingPage(string $prompt, ?string $customSystemPrompt = null): string;

    public function generateHomePage(string $prompt, ?string $customSystemPrompt = null): string;

    public function generateBrandPositioning(string $prompt, ?string $customSystemPrompt = null): string;

    public function getUsageStats(): array;
}
