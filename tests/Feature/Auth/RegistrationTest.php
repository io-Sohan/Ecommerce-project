<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register and are redirected to login with success message', function () {
    Event::fake();

    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    Event::assertDispatched(Registered::class);

    $this->assertGuest();
    $response->assertRedirect(route('login'));
    $response->assertSessionHas('status');

    $user = User::where('email', 'test@example.com')->first();
    expect($user)->not->toBeNull();
});

test('new users can register with phone number', function () {
    Event::fake();

    $response = $this->post(route('register.store'), [
        'name' => 'Phone User',
        'email' => 'phoneuser@example.com',
        'phone' => '01712345678',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    Event::assertDispatched(Registered::class);

    $this->assertGuest();

    $user = User::where('email', 'phoneuser@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->phone)->toBe('01712345678');
});
