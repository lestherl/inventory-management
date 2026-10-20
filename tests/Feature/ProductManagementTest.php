<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can create a product', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this->actingAs($admin)
        ->post('/products', [
            'name' => 'Laptop',
            'quantity' => 15,
            'price' => 24999.00,
        ])
        ->assertRedirect(route('products'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('products', [
        'name' => 'Laptop',
        'quantity' => 15,
        'price' => '24999.00',
    ]);
});
