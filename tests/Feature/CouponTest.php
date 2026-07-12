<?php

use App\Models\Coupon;
use App\Models\User;
use App\Services\CouponService;
use Inertia\Testing\AssertableInertia as Assert;

// ─── Model Validation ──────────────────────────────────────────────────

test('coupon calculates flat discount correctly', function () {
    $coupon = Coupon::factory()->flat(150)->make();

    expect($coupon->calculateDiscount(1000))->toBe(150.0)
        ->and($coupon->calculateDiscount(100))->toBe(100.0); // can't exceed subtotal
});

test('coupon calculates percentage discount correctly', function () {
    $coupon = Coupon::factory()->percentage(10)->make();

    expect($coupon->calculateDiscount(2000))->toBe(200.0);
});

test('coupon is invalid when inactive', function () {
    $coupon = Coupon::factory()->inactive()->make();

    expect($coupon->isValid(5000))->toBeFalse();
});

test('coupon is invalid when expired', function () {
    $coupon = Coupon::factory()->expired()->make();

    expect($coupon->isValid(5000))->toBeFalse();
});

test('coupon is invalid when usage limit exhausted', function () {
    $coupon = Coupon::factory()->exhausted()->make();

    expect($coupon->isValid(5000))->toBeFalse();
});

test('coupon is invalid when subtotal is below minimum', function () {
    $coupon = Coupon::factory()->state(['min_order_amount' => 1000])->make();

    expect($coupon->isValid(500))->toBeFalse()
        ->and($coupon->isValid(1000))->toBeTrue();
});

// ─── CouponService ─────────────────────────────────────────────────────

test('coupon service returns invalid for unknown code', function () {
    $service = app(CouponService::class);

    $result = $service->validate('NONEXISTENT', 1000);

    expect($result['valid'])->toBeFalse()
        ->and($result['message'])->toBe('Invalid coupon code.');
});

test('coupon service validates a valid coupon', function () {
    Coupon::factory()->flat(100)->create(['code' => 'FLAT100', 'min_order_amount' => 0]);

    $service = app(CouponService::class);
    $result = $service->validate('FLAT100', 500);

    expect($result['valid'])->toBeTrue()
        ->and($result['discount'])->toBe(100.0)
        ->and($result['coupon']->code)->toBe('FLAT100');
});

// ─── Storefront Coupon Apply ────────────────────────────────────────────

test('storefront coupon apply returns discount info for valid coupon', function () {
    Coupon::factory()->percentage(10)->create(['code' => 'SAVE10', 'min_order_amount' => 0]);

    $this->postJson(route('shop.coupon.apply'), [
        'code' => 'SAVE10',
        'subtotal' => 2000,
    ])
        ->assertOk()
        ->assertJson([
            'valid' => true,
            'coupon_code' => 'SAVE10',
            'discount_amount' => 200,
        ]);
});

test('storefront coupon apply returns error for invalid coupon', function () {
    $this->postJson(route('shop.coupon.apply'), [
        'code' => 'BADCODE',
        'subtotal' => 1000,
    ])
        ->assertStatus(422)
        ->assertJson(['valid' => false]);
});

// ─── Admin CRUD ────────────────────────────────────────────────────────

test('guests cannot access admin coupons', function () {
    $this->get(route('admin.coupons.index'))
        ->assertRedirect(route('login'));
});

test('customers cannot access admin coupons', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.coupons.index'))
        ->assertForbidden();
});

test('admins can view the coupons index', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Coupon::factory()->count(3)->create();

    $this->actingAs($admin)
        ->get(route('admin.coupons.index'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('admin/coupons/Index')
                ->has('coupons', 3)
        );
});

test('admins can create a coupon', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->post(route('admin.coupons.store'), [
            'code' => 'NEWCODE',
            'discount_type' => 'flat',
            'discount_value' => 50,
            'min_order_amount' => 500,
            'usage_limit' => 100,
            'expires_at' => now()->addMonth()->toDateString(),
            'is_active' => true,
        ])
        ->assertRedirect(route('admin.coupons.index'));

    $this->assertDatabaseHas('coupons', [
        'code' => 'NEWCODE',
        'discount_type' => 'flat',
        'discount_value' => 50,
    ]);
});

test('admins can update a coupon', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $coupon = Coupon::factory()->create(['code' => 'OLD']);

    $this->actingAs($admin)
        ->put(route('admin.coupons.update', $coupon), [
            'code' => 'UPDATED',
            'discount_type' => 'percentage',
            'discount_value' => 25,
            'min_order_amount' => 0,
            'is_active' => true,
        ])
        ->assertRedirect(route('admin.coupons.index'));

    expect($coupon->fresh()->code)->toBe('UPDATED');
});

test('admins can delete a coupon', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $coupon = Coupon::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.coupons.destroy', $coupon))
        ->assertRedirect(route('admin.coupons.index'));

    $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
});
