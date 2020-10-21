<?php

namespace Tests\Unit;

use App\Cart\Cart;
use App\Cart\Models\Product;
use App\Exceptions\InvalidCurrencyException;
use Tests\TestCase;

class CartTest extends TestCase
{
    /** @test */
    function throws_exception_when_add_missing_product_to_the_cart()
    {
        $this->expectException(InvalidCurrencyException::class);

        (app(Cart::class))->addItems(['some missing product']);
    }

    /** @test
     * @throws InvalidCurrencyException
     */
    function can_get_item_from_cart()
    {
        $cart = app(Cart::class);
        $cart->addItems(['shoes']);
        $product = Product::findByName('shoes');

        $this->assertEquals($product->id, $cart->getItem($product)->product->id);
        $this->assertEquals(1, $cart->getItem($product)->quantity);
    }

    /** @test
     * @throws InvalidCurrencyException
     */
    function can_increase_product_quantity()
    {
        $cart = app(Cart::class);
        $cart->addItems(['shoes', 'shoes']);
        $product = Product::findByName('shoes');

        $this->assertEquals($product->id, $cart->getItem($product)->product->id);
        $this->assertEquals(2, $cart->getItem($product)->quantity);
    }

    /** @test
     * @throws InvalidCurrencyException
     */
    function can_get_subtotal()
    {
        $cart1 = app(Cart::class);
        $cart1->addItems(['shoes', 'shoes']);
        $product1 = Product::findByName('shoes');

        $cart2 = app(Cart::class);
        $cart2->addItems(['shoes', 'shoes', 'pants']);
        $product2 = Product::findByName('pants');

        $this->assertEquals($product1->price * 2, $cart1->getSubTotal());
        $this->assertEquals($product1->price * 2 + $product2->price, $cart2->getSubTotal());
    }

    /** @test
     * @throws InvalidCurrencyException
     */
    function can_get_taxes()
    {
        $cart = app(Cart::class);
        $cart->addItems(['shoes', 'shoes', 'pants']);
        $subtotal = $cart->getSubTotal();

        $this->assertEquals($subtotal * Cart::TAX_PERCENTAGE, $cart->getTaxes());
    }

    /** @test
     * @throws InvalidCurrencyException
     */
    function can_get_total()
    {
        $cart = app(Cart::class);
        $cart->addItems(['shoes', 'shoes', 'pants']);
        $subtotal = $cart->getSubTotal();
        $taxes = $cart->getTaxes();

        $this->assertEquals($subtotal + $taxes, $cart->getTotal());
    }

    /** @test */
    function can_change_currency()
    {
        $cart = app(Cart::class);
        $cart->setCurrency('EGP');

        $this->assertEquals('EGP', $cart->getCurrency());
    }

    /** @test */
    function can_not_set_invalid_currency()
    {
        $this->expectException(InvalidCurrencyException::class);

        $cart = app(Cart::class);
        $cart->setCurrency('AAA');
    }

    /** @test */
    function can_get_currency_symbol()
    {
        $cart = app(Cart::class);
        $cart->setCurrency('EGP');

        $this->assertEquals('£', $cart->getCurrencySymbol());
    }
}
