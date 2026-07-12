<?php

use App\Models\User;

test('guest can access checkout page', function () {
    $response = $this->get(route('shop.checkout'));

    // Redirects to cart when cart is empty, but not to login — proves guest access works
    $response->assertRedirect(route('shop.cart'));
});

test('authenticated customer can access checkout', function () {
    $user = User::factory()->create(['role' => 'customer']);

    $response = $this->actingAs($user)->get(route('shop.checkout'));

    // Redirects to cart when cart is empty, but not to login — proves auth works
    $response->assertRedirect(route('shop.cart'));
});

test('guest can post to checkout', function () {
    $response = $this->post(route('shop.checkout.store'), []);

    // Validation errors (not a login redirect) — proves guest access works
    $response->assertSessionHasErrors();
});

test('admin can access dashboard', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
});

test('customer cannot access dashboard', function () {
    $user = User::factory()->create(['role' => 'customer']);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertForbidden();
});

test('guest cannot access dashboard', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

test('new registered user defaults to customer role', function () {
    $this->post(route('register.store'), [
        'name' => 'New Customer',
        'email' => 'newcustomer@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertGuest();

    $user = User::where('email', 'newcustomer@example.com')->first();

    expect($user->role)->toBe('customer');
});
