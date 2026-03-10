<?php

use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guest cannot access tickets index', function () {
    $response = $this->get(route('tickets.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated user can view tickets index', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('tickets.index'));

    $response->assertOk();
});

test('authenticated user can view create ticket page', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('tickets.create'));

    $response->assertOk();
});

test('authenticated user can create a ticket with valid data', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();
    $category = TicketCategory::factory()->create(['department_id' => $department->id]);

    $response = $this
        ->actingAs($user)
        ->post(route('tickets.store'), [
            'subject' => 'Test ticket subject',
            'description' => 'Test ticket description with details.',
            'priority' => 'medium',
            'department_id' => $department->id,
            'category_id' => $category->id,
        ]);

    $ticket = Ticket::query()->where('user_id', $user->id)->first();

    expect($ticket)->not->toBeNull();
    expect($ticket->subject)->toBe('Test ticket subject');
    expect($ticket->status)->toBe('open');

    $response->assertRedirect(route('tickets.show', $ticket));
});

test('authenticated user can create a ticket with file attachments', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $department = Department::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('tickets.store'), [
            'subject' => 'Ticket with attachments',
            'description' => 'Description for ticket with attachments.',
            'priority' => 'high',
            'department_id' => $department->id,
            'attachments' => [
                UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf'),
                UploadedFile::fake()->image('screenshot.jpg', 640, 480),
            ],
        ]);

    $ticket = Ticket::query()->where('user_id', $user->id)->first();

    expect($ticket)->not->toBeNull();
    expect($ticket->attachments)->toHaveCount(2);

    $response->assertRedirect(route('tickets.show', $ticket));
});

test('ticket creation validates required fields', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('tickets.store'), []);

    $response->assertSessionHasErrors(['subject', 'description', 'priority']);
});

test('ticket creation validates file size and type', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('tickets.store'), [
            'subject' => 'Test',
            'description' => 'Test description',
            'priority' => 'low',
            'attachments' => [
                UploadedFile::fake()->create('malware.exe', 1024, 'application/x-msdownload'),
            ],
        ]);

    $response->assertSessionHasErrors('attachments.0');
});

test('authenticated user can view own ticket', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $response = $this
        ->actingAs($user)
        ->get(route('tickets.show', $ticket));

    $response->assertOk();
});

test('authenticated user cannot view another users ticket', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $otherUser->id]);

    $response = $this
        ->actingAs($user)
        ->get(route('tickets.show', $ticket));

    $response->assertForbidden();
});

test('ticket gets auto-generated reference number', function () {
    $user = User::factory()->create();

    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    expect($ticket->reference)->toStartWith('TKT-');
    expect($ticket->reference)->not->toBeEmpty();
});

test('ticket list only shows users own tickets', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Ticket::factory()->count(3)->create(['user_id' => $user->id]);
    Ticket::factory()->count(2)->create(['user_id' => $otherUser->id]);

    $response = $this
        ->actingAs($user)
        ->get(route('tickets.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('tickets/index')
        ->has('tickets.data', 3)
    );
});
