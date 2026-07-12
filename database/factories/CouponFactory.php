<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => Str::upper(fake()->unique()->bothify('SAVE##??')),
            'discount_type' => fake()->randomElement(['flat', 'percentage']),
            'discount_value' => fake()->randomFloat(2, 5, 50),
            'min_order_amount' => fake()->randomElement([0, 500, 1000, 2000]),
            'usage_limit' => fake()->optional()->numberBetween(10, 500),
            'times_used' => 0,
            'expires_at' => fake()->optional()->dateTimeBetween('now', '+6 months'),
            'is_active' => true,
        ];
    }

    public function flat(float $value = 100): static
    {
        return $this->state(fn (): array => [
            'discount_type' => 'flat',
            'discount_value' => $value,
        ]);
    }

    public function percentage(float $value = 10): static
    {
        return $this->state(fn (): array => [
            'discount_type' => 'percentage',
            'discount_value' => $value,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (): array => [
            'expires_at' => now()->subDay(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }

    public function exhausted(): static
    {
        return $this->state(fn (): array => [
            'usage_limit' => 1,
            'times_used' => 1,
        ]);
    }
}
