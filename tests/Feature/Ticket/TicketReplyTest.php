<?php

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('authenticated user can reply to own ticket', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $response = $this
        ->actingAs($user)
        ->post(route('tickets.replies.store', $ticket), [
            'body' => 'This is a reply to the ticket.',
        ]);

    $response->assertRedirect();

    expect($ticket->replies)->toHaveCount(1);
    expect($ticket->replies->first()->body)->toBe('This is a reply to the ticket.');
});

test('authenticated user cannot reply to another users ticket', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $otherUser->id]);

    $response = $this
        ->actingAs($user)
        ->post(route('tickets.replies.store', $ticket), [
            'body' => 'Attempting to reply to someone else ticket.',
        ]);

    $response->assertForbidden();
});

test('reply requires body field', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $response = $this
        ->actingAs($user)
        ->post(route('tickets.replies.store', $ticket), []);

    $response->assertSessionHasErrors('body');
});

test('user can reply with file attachments', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $response = $this
        ->actingAs($user)
        ->post(route('tickets.replies.store', $ticket), [
            'body' => 'Reply with attachments.',
            'attachments' => [
                UploadedFile::fake()->create('notes.pdf', 512, 'application/pdf'),
            ],
        ]);

    $response->assertRedirect();

    $reply = $ticket->replies()->first();
    expect($reply)->not->toBeNull();
    expect($ticket->attachments)->toHaveCount(1);
});

test('reply is associated with correct ticket and user', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $this
        ->actingAs($user)
        ->post(route('tickets.replies.store', $ticket), [
            'body' => 'A reply to verify associations.',
        ]);

    $reply = $ticket->replies()->first();

    expect($reply->ticket_id)->toBe($ticket->id);
    expect($reply->user_id)->toBe($user->id);
});
