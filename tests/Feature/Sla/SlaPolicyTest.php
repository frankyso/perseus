<?php

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\SlaPolicy;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;

test('sla policy is automatically applied to ticket on creation', function () {
    SlaPolicy::factory()->create([
        'priority' => TicketPriority::High->value,
        'first_response_hours' => 4,
        'resolution_hours' => 24,
    ]);

    $ticket = Ticket::factory()->create(['priority' => TicketPriority::High]);

    expect($ticket->sla_policy_id)->not->toBeNull();
    expect($ticket->first_response_due_at)->not->toBeNull();
    expect($ticket->resolution_due_at)->not->toBeNull();
});

test('ticket without matching sla policy has no sla', function () {
    $ticket = Ticket::factory()->create(['priority' => TicketPriority::Low]);

    expect($ticket->sla_policy_id)->toBeNull();
    expect($ticket->sla_status)->toBe('no_sla');
});

test('first response is recorded when agent replies', function () {
    $sla = SlaPolicy::factory()->create([
        'priority' => TicketPriority::Medium->value,
        'first_response_hours' => 8,
        'resolution_hours' => 48,
    ]);

    $customer = User::factory()->create();
    $agent = User::factory()->create();

    $ticket = Ticket::factory()->create([
        'priority' => TicketPriority::Medium,
        'user_id' => $customer->id,
    ]);

    expect($ticket->first_response_at)->toBeNull();

    TicketReply::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $agent->id,
        'is_internal_note' => false,
    ]);

    expect($ticket->refresh()->first_response_at)->not->toBeNull();
    expect($ticket->first_response_breached)->toBeFalse();
});

test('customer reply does not count as first response', function () {
    SlaPolicy::factory()->create([
        'priority' => TicketPriority::Medium->value,
        'first_response_hours' => 8,
        'resolution_hours' => 48,
    ]);

    $customer = User::factory()->create();
    $ticket = Ticket::factory()->create([
        'priority' => TicketPriority::Medium,
        'user_id' => $customer->id,
    ]);

    TicketReply::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $customer->id,
        'is_internal_note' => false,
    ]);

    expect($ticket->refresh()->first_response_at)->toBeNull();
});

test('internal note does not count as first response', function () {
    SlaPolicy::factory()->create([
        'priority' => TicketPriority::Medium->value,
        'first_response_hours' => 8,
        'resolution_hours' => 48,
    ]);

    $customer = User::factory()->create();
    $agent = User::factory()->create();
    $ticket = Ticket::factory()->create([
        'priority' => TicketPriority::Medium,
        'user_id' => $customer->id,
    ]);

    TicketReply::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $agent->id,
        'is_internal_note' => true,
    ]);

    expect($ticket->refresh()->first_response_at)->toBeNull();
});

test('sla status shows on_track when within deadline', function () {
    SlaPolicy::factory()->create([
        'priority' => TicketPriority::Medium->value,
        'first_response_hours' => 8,
        'resolution_hours' => 48,
    ]);

    $ticket = Ticket::factory()->create(['priority' => TicketPriority::Medium]);

    expect($ticket->sla_status)->toBe('on_track');
});

test('sla status shows breached when past deadline', function () {
    SlaPolicy::factory()->create([
        'priority' => TicketPriority::Urgent->value,
        'first_response_hours' => 1,
        'resolution_hours' => 4,
    ]);

    $ticket = Ticket::factory()->create([
        'priority' => TicketPriority::Urgent,
        'status' => TicketStatus::Open,
    ]);

    $ticket->update([
        'first_response_due_at' => now()->subHour(),
        'resolution_due_at' => now()->subHour(),
    ]);

    expect($ticket->refresh()->sla_status)->toBe('breached');
});

test('find by priority returns active sla policy', function () {
    SlaPolicy::factory()->create([
        'priority' => TicketPriority::High->value,
        'first_response_hours' => 4,
        'resolution_hours' => 24,
        'is_active' => true,
    ]);

    SlaPolicy::factory()->create([
        'priority' => TicketPriority::Low->value,
        'first_response_hours' => 24,
        'resolution_hours' => 72,
        'is_active' => false,
    ]);

    expect(SlaPolicy::findByPriority(TicketPriority::High->value))->not->toBeNull();
    expect(SlaPolicy::findByPriority(TicketPriority::Low->value))->toBeNull();
});
