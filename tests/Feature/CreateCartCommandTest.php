<?php

namespace Tests\Feature;

use App\Cart\Cart;
use App\Cart\Models\Product;
use Symfony\Component\Console\Exception\RuntimeException;
use Tests\TestCase;

class CreateCartCommandTest extends TestCase
{
    /** @test */
    public function command_requires_one_product_at_least()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "products").');

        $this->artisan('cart:create');
    }

    /** @test */
    public function it_returns_the_bill()
    {
        $subtotal = (2 * Product::findByName('T-shirt')->price) +
            Product::findByName('Shoes')->price +
            Product::findByName('Jacket')->price;
        $tax = $subtotal * Cart::TAX_PERCENTAGE;

        $this->artisan('cart:create T-shirt T-shirt Shoes Jacket')
            ->expectsOutput("Subtotal: $$subtotal")
            ->expectsOutput("Taxes: $$tax");
    }
}
