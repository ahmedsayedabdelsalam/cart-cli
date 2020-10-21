<?php

namespace Tests\Unit\Models;

use App\Cart\Models\Offer;
use App\Cart\Models\Product;
use Tests\TestCase;

class OfferModelTest extends TestCase
{
    /** @test */
    function can_find_offers_on_products()
    {
        $offers = Offer::findOffersOnProducts(['shoes']);

        $this->assertEqualsIgnoringCase('shoes', $offers->first()->when_you_buy);
    }

    /** @test */
    function it_returns_null_when_can_not_find_product_by_name()
    {
        $offers = Offer::findOffersOnProducts(['not found product']);

        $this->assertEmpty($offers);
    }
}
