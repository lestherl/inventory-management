<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('registration is not public', function () {
    $this->get('/register')->assertNotFound();
});

test('admin can view the users table', function () {
    $admin = User::factory()->create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'role' => 'admin',
    ]);

    $staff = User::factory()->create([
        'name' => 'Staff User',
        'email' => 'staff@example.com',
        'role' => 'staff',
    ]);

    $this->actingAs($admin)
        ->get('/admin/users')
        ->assertOk()
        ->assertSeeText('Users')
        ->assertSeeText($staff->name)
        ->assertSeeText($staff->email)
        ->assertSeeText($admin->role);
});
