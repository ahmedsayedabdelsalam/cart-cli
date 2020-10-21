<?php


namespace App\Cart;


use App\Cart\Models\Offer;
use App\Cart\Models\Product;
use App\Cart\Services\CurrencyConverter\Contracts\CurrencyConverter;
use App\Exceptions\InvalidCurrencyException;

class Cart
{
    private array $items = [];

    const TAX_PERCENTAGE = 0.14;

    private array $discounts = [];

    private CurrencyConverter $currencyConverter;

    private string $currency = 'USD';

    public function __construct(CurrencyConverter $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }

    public function addItems(array $items)
    {
        collect($items)->each(function ($item) {
            if (!$product = Product::findByName($item))
                throw new InvalidCurrencyException("Can't find Product with name $item");

            $this->addItem($product);
        });

        $this->setDiscounts();
    }

    public function addItem(Product $product)
    {
        $item = $this->getItem($product);

        if ($item) {
            $item->quantity++;
            return;
        }

        $this->items[] = (object)[
            'product' => $product,
            'quantity' => 1
        ];
    }

    public function getItem(Product $product)
    {
        return $this->getItems()->first(fn($item) => $item->product->id === $product->id);
    }

    public function getItems()
    {
        return collect($this->items);
    }

    public function getDiscounts()
    {
        return $this->discounts;
    }

    public function getSubTotal()
    {
        $amount = $this->getItems()->reduce(function ($current, $next) {
            return $current + $next->quantity * $next->product->price;
        }, 0);

        return $this->convertToUserCurrency($amount);
    }

    public function getTaxes()
    {
        return $this->getSubTotal() * static::TAX_PERCENTAGE;
    }

    public function getTotal()
    {
        return $this->getSubTotal() + $this->getTaxes();
    }

    protected function convertToUserCurrency($amount)
    {
        return $this->currencyConverter->amount($amount)->convert();
    }

    public function setCurrency(string $currency)
    {
        $this->currencyConverter->to($currency);

        $this->currency = $currency;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getCurrencySymbol()
    {
        return $this->currencyConverter->selectedCurrency()->symbol;
    }

    private function setDiscounts()
    {
        $offers = Offer::findOffersOnProducts(
            $this->getItems()->map(fn($item) => $item->product->name)->toArray()
        );

        $this->discounts = $offers->filter(function ($offer) {
            $item = $this->getItem(Product::findByName($offer->when_you_buy));
            return $item->quantity === $offer->amount_to_buy;
        })->toArray();
    }
}
