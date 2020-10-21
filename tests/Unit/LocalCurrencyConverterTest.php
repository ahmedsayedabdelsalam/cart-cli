<?php

namespace Tests\Unit;

use App\Cart\Services\CurrencyConverter\Contracts\CurrencyConverter;
use App\Exceptions\InvalidCurrencyException;
use Tests\TestCase;

class LocalCurrencyConverterTest extends TestCase
{
    /** @test */
    function can_convert_from_usd_to_any_currency()
    {
        $amount = app(CurrencyConverter::class)
            ->amount(200)
            ->to('EGP')
            ->convert();

        $this->assertEquals(15.7 * 200, $amount);
    }

    /** @test */
    function it_throws_an_exception_when_setting_invalid_currency()
    {
        $this->expectException(InvalidCurrencyException::class);

        app(CurrencyConverter::class)
            ->amount(200)
            ->to('AAA')
            ->convert();
    }
}
