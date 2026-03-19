<?php

use App\Enums\DealStage;
use App\Models\Client;
use App\Models\Deal;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'company_administrator']);
});

test('admin can view surge pipeline', function () {
    Deal::factory()->count(3)->create();

    $this->actingAs($this->admin)
        ->get(route('admin.surge.index'))
        ->assertOk()
        ->assertViewIs('admin.surge.index')
        ->assertViewHas('dealsByStage');
});

test('guest cannot access surge pipeline', function () {
    $this->get(route('admin.surge.index'))
        ->assertRedirect(route('login'));
});

test('admin can create a deal', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.surge.store'), [
            'title' => 'Big Contract',
            'value' => '5000.00',
            'stage' => 'new',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('deals', ['title' => 'Big Contract', 'stage' => 'new']);
});

test('creating deal validates required fields', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.surge.store'), [])
        ->assertSessionHasErrors(['title', 'value', 'stage']);
});

test('admin can move a deal to a new stage', function () {
    $deal = Deal::factory()->create(['stage' => DealStage::New]);

    $this->actingAs($this->admin)
        ->patch(route('admin.surge.update', $deal), ['stage' => 'qualified'])
        ->assertRedirect();

    expect($deal->fresh()->stage)->toBe(DealStage::Qualified);
});

test('deal stage update validates stage value', function () {
    $deal = Deal::factory()->create();

    $this->actingAs($this->admin)
        ->patch(route('admin.surge.update', $deal), ['stage' => 'not_a_real_stage'])
        ->assertSessionHasErrors(['stage']);
});

test('admin can delete a deal', function () {
    $deal = Deal::factory()->create();

    $this->actingAs($this->admin)
        ->delete(route('admin.surge.destroy', $deal))
        ->assertRedirect();

    $this->assertModelMissing($deal);
});

test('score lead job updates lead score', function () {
    $client = Client::factory()->create();

    dispatch(new \App\Jobs\ScoreLeadJob($client));

    expect($client->fresh()->lead_score)->toBeGreaterThanOrEqual(0);
});
