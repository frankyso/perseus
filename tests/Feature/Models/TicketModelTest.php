<?php

use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketReply;
use App\Models\User;

test('ticket belongs to user', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    expect($ticket->user->id)->toBe($user->id);
});

test('ticket belongs to department', function () {
    $department = Department::factory()->create();
    $ticket = Ticket::factory()->create(['department_id' => $department->id]);

    expect($ticket->department->id)->toBe($department->id);
});

test('ticket has many replies', function () {
    $ticket = Ticket::factory()->create();
    TicketReply::factory()->count(3)->create(['ticket_id' => $ticket->id]);

    expect($ticket->replies)->toHaveCount(3);
});

test('ticket has many attachments', function () {
    $ticket = Ticket::factory()->create();
    TicketAttachment::factory()->count(2)->create(['ticket_id' => $ticket->id]);

    expect($ticket->attachments)->toHaveCount(2);
});

test('ticket isOpen returns true for open status', function () {
    expect(Ticket::factory()->create(['status' => 'open'])->isOpen())->toBeTrue();
    expect(Ticket::factory()->create(['status' => 'in_progress'])->isOpen())->toBeTrue();
    expect(Ticket::factory()->create(['status' => 'waiting_reply'])->isOpen())->toBeTrue();
});

test('ticket isOpen returns false for resolved and closed', function () {
    expect(Ticket::factory()->resolved()->create()->isOpen())->toBeFalse();
    expect(Ticket::factory()->closed()->create()->isOpen())->toBeFalse();
});

test('ticket auto-generates reference on creation', function () {
    $ticket = Ticket::factory()->create(['reference' => null]);

    expect($ticket->reference)->toStartWith('TKT-');
    expect(strlen($ticket->reference))->toBeGreaterThan(4);
});
