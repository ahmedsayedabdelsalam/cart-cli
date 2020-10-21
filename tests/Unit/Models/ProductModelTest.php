<?php

namespace Tests\Unit\Models;

use App\Cart\Models\Product;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
    /** @test */
    function can_find_product_by_name()
    {
        $product = Product::findByName('shoes');

        $this->assertEqualsIgnoringCase('shoes', $product->name);
    }

    /** @test */
    function it_returns_null_when_can_not_find_product_by_name()
    {
        $product = Product::findByName('some missing product');

        $this->assertNull($product);
    }
}
