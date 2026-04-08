<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'buyer_id' => User::factory(),
            'item_id' => Item::factory(),
            'address_id' => Address::factory(),
            'price' => $this->faker->numberBetween(1000, 10000),
            'qty' => 1,
            'status' => 'paid',
            'ordered_at' => now(),
        ];
    }
}
