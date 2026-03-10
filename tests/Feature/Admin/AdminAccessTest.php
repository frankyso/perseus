<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

test('regular user cannot access admin panel', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/admin');

    $response->assertForbidden();
});

test('user with super_admin role can access admin panel', function () {
    Role::findOrCreate('super_admin', 'web');
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    $response = $this
        ->actingAs($user)
        ->get('/admin');

    $response->assertOk();
});

test('user with agent role can access admin panel', function () {
    Role::findOrCreate('agent', 'web');
    $user = User::factory()->create();
    $user->assignRole('agent');

    $response = $this
        ->actingAs($user)
        ->get('/admin');

    $response->assertOk();
});
