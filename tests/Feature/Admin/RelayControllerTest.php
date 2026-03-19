<?php

use App\Models\EmailSequence;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'company_administrator']);
});

test('admin can view relay index', function () {
    EmailSequence::factory()->count(2)->create();

    $this->actingAs($this->admin)
        ->get(route('admin.relay.index'))
        ->assertOk()
        ->assertViewIs('admin.relay.index')
        ->assertViewHas('sequences');
});

test('guest cannot access relay index', function () {
    $this->get(route('admin.relay.index'))
        ->assertRedirect(route('login'));
});

test('admin can create an email sequence', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.relay.store'), [
            'name' => 'Welcome Sequence',
            'trigger' => 'manual',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('email_sequences', ['name' => 'Welcome Sequence']);
});

test('creating sequence validates required fields', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.relay.store'), [])
        ->assertSessionHasErrors(['name', 'trigger']);
});

test('admin can enroll a contact in a sequence', function () {
    Queue::fake();
    $sequence = EmailSequence::factory()->create(['status' => 'active']);

    $this->actingAs($this->admin)
        ->post(route('admin.relay.enroll', $sequence), [
            'email' => 'contact@example.com',
            'contact_name' => 'Test Contact',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('email_sequence_enrollments', [
        'email_sequence_id' => $sequence->id,
        'email' => 'contact@example.com',
        'status' => 'active',
    ]);
});

test('enrollment validates email', function () {
    $sequence = EmailSequence::factory()->create();

    $this->actingAs($this->admin)
        ->post(route('admin.relay.enroll', $sequence), ['email' => 'not-an-email'])
        ->assertSessionHasErrors(['email']);
});

test('admin can delete a sequence', function () {
    $sequence = EmailSequence::factory()->create();

    $this->actingAs($this->admin)
        ->delete(route('admin.relay.destroy', $sequence))
        ->assertRedirect();

    $this->assertModelMissing($sequence);
});
