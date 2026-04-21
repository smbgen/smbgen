<?php

use App\Models\User;
use App\Services\StripeService;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Config::set('services.stripe.secret', '');
});

test('StripeService reports unconfigured when secret key is missing', function () {
    $service = new StripeService;

    expect($service->isConfigured())->toBeFalse();
});

test('StripeService reports unconfigured when secret key does not start with sk_', function () {
    Config::set('services.stripe.secret', 'invalid-key');

    $service = new StripeService;

    expect($service->isConfigured())->toBeFalse();
});

test('StripeService reports configured when valid test key is provided', function () {
    Config::set('services.stripe.secret', 'sk_test_fakekeyfortesting123');

    $service = new StripeService;

    expect($service->isConfigured())->toBeTrue();
});

test('findOrCreateCustomer returns null when service is unconfigured', function () {
    $user = User::factory()->create();

    $service = new StripeService;

    expect($service->findOrCreateCustomer($user))->toBeNull();
});

test('createCheckoutSession returns null when service is unconfigured', function () {
    $user = User::factory()->create();
    $invoice = \App\Models\Invoice::create([
        'user_id' => $user->id,
        'status' => \App\Models\Invoice::STATUS_SENT,
        'currency' => 'usd',
        'total_amount' => 5000,
    ]);

    $service = new StripeService;

    $result = $service->createCheckoutSession(
        $invoice,
        route('payment.success'),
        route('payment.cancel')
    );

    expect($result)->toBeNull();
});

test('stripe webhook endpoint returns 400 when signature is invalid', function () {
    $response = $this->postJson('/stripe/webhook', [], [
        'Stripe-Signature' => 'invalid-signature',
    ]);

    $response->assertStatus(400);
});

test('stripe webhook endpoint returns 400 with no signature header', function () {
    $response = $this->postJson('/stripe/webhook', [
        'type' => 'checkout.session.completed',
    ]);

    $response->assertStatus(400);
});

test('stripe webhook endpoint is excluded from CSRF protection', function () {
    $response = $this->post('/stripe/webhook', []);

    // Should not get 419 (CSRF) - should get 400 (bad signature) or 200
    expect($response->status())->not->toBe(419);
});

test('StripeService testConnection returns failure when unconfigured', function () {
    $service = new StripeService;

    $result = $service->testConnection();

    expect($result)->toBeArray();
    expect($result['success'])->toBeFalse();
});
