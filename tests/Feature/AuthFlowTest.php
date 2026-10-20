<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the login and registration forms', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('name="email"', false)
        ->assertSee('Sign in');

    $this->get(route('register'))
        ->assertOk()
        ->assertSee('name="role"', false)
        ->assertSee('Create account');
});

it('creates cashier and staff accounts and redirects to their dashboards', function (string $role) {
    $response = $this->followingRedirects()->post(route('register'), [
        'name' => 'Jordan Lee',
        'email' => "{$role}@example.com",
        'password' => 'secure-password',
        'password_confirmation' => 'secure-password',
        'role' => $role,
    ]);

    $response->assertSee(ucfirst($role).' dashboard');
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => "{$role}@example.com",
        'role' => $role,
    ]);
})->with(['cashier', 'staff']);

it('does not allow public registration as an admin', function () {
    $response = $this->post(route('register'), [
        'name' => 'Jordan Lee',
        'email' => 'admin@example.com',
        'password' => 'secure-password',
        'password_confirmation' => 'secure-password',
        'role' => 'admin',
    ]);

    $response->assertSessionHasErrors('role');
    $this->assertDatabaseMissing('users', ['email' => 'admin@example.com']);
    $this->assertGuest();
});

it('redirects signed-in users to the dashboard for their role', function (string $role) {
    $user = User::factory()->create(['role' => $role]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertRedirect(route("{$role}.dashboard"));
})->with(['admin', 'staff', 'cashier']);

it('authenticates with valid credentials and rejects invalid credentials', function () {
    $user = User::factory()->create([
        'email' => 'cashier@example.com',
        'role' => 'cashier',
        'password' => 'secure-password',
    ]);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'incorrect-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();

    $this->followingRedirects()->post(route('login'), [
        'email' => $user->email,
        'password' => 'secure-password',
    ])->assertSee('Cashier dashboard');

    $this->assertAuthenticatedAs($user);
});

it('forbids users from opening another role dashboard', function () {
    $staff = User::factory()->create(['role' => 'staff']);

    $this->actingAs($staff)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

it('logs users out and invalidates authenticated access', function () {
    $user = User::factory()->create(['role' => 'staff']);

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});
