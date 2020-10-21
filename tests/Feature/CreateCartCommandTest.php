<?php

namespace Tests\Feature;

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
}
