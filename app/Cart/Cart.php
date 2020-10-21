<?php


namespace App\Cart;


use App\Cart\Models\Offer;
use App\Cart\Models\Product;
use App\Cart\Services\CurrencyConverter\Contracts\CurrencyConverter;
use App\Exceptions\InvalidCurrencyException;
use Illuminate\Support\Collection;

class Cart
{
    /**
     * Items added to the cart.
     *
     * @var array $items
     */
    private array $items = [];

    /**
     * Tax Rate.
     */
    const TAX_PERCENTAGE = 0.14;

    /**
     * Items added to the cart.
     *
     * @var Collection $offers
     */
    private $offers;

    /**
     * Applied discounts.
     *
     * @var array $discounts
     */
    private array $discounts = [];

    /**
     * Currey Converter.
     *
     * @var CurrencyConverter $currencyConverter
     */
    private CurrencyConverter $currencyConverter;

    /**
     * User Currency.
     *
     * @var string
     */
    private string $currency = 'USD';

    /**
     * Cart constructor.
     * @param CurrencyConverter $currencyConverter
     */
    public function __construct(CurrencyConverter $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * Add Products to The Cart and Calculate the Discounts.
     *
     * @param array $items
     * @throws InvalidCurrencyException
     */
    public function addItems(array $items)
    {
        collect($items)->each(function ($item) {
            if (!$product = Product::findByName($item))
                throw new InvalidCurrencyException("Can't find Product with name $item");

            $this->addItem($product);
        });

        $this->setAppliedOffers();
        $this->setDiscounts();
    }

    /**
     * Add new item to the cart or increase the Quantity.
     *
     * @param Product $product
     */
    public function addItem(Product $product)
    {
        $item = $this->getItem($product);

        if ($item) {
            $item->quantity++;
            return;
        }

        $this->items[] = (object)[
            'product' => $product,
            'quantity' => 1,
            'offer_applied_on' => 0,
            'used_in_offers' => 0
        ];
    }

    /**
     * Find item in the Cart with the Product ID.
     *
     * @param Product $product
     * @return mixed
     */
    public function getItem(Product $product)
    {
        return $this->getItems()->first(fn($item) => $item->product->id === $product->id);
    }

    /**
     * Get Collection of Items in the Cart.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getItems()
    {
        return collect($this->items);
    }

    /**
     * Set User Currency.
     *
     * @param string $currency
     */
    public function setCurrency(string $currency)
    {
        $this->currencyConverter->to($currency);

        $this->currency = $currency;
    }

    /**
     * Get User Currency.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Get Selected Currency Symbol.
     *
     * @return mixed
     */
    public function getCurrencySymbol()
    {
        return $this->currencyConverter->selectedCurrency()->symbol;
    }

    /**
     * Get Collection of Applied Discounts.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDiscounts()
    {
        return collect($this->discounts)
            ->each(fn($discount) => $discount->amount = $this->convertToUserCurrency($discount->amount));
    }

    /**
     * Get Total Price of Items before Tax and Discounts.
     * Converted To User Currency.
     *
     * @return mixed
     */
    public function getSubTotal()
    {
        $amount = $this->getItems()->reduce(function ($current, $next) {
            return $current + $next->quantity * $next->product->price;
        }, 0);

        return $this->convertToUserCurrency($amount);
    }

    /**
     * Get Tax Amount Converted to User Currency.
     *
     * @return float|int
     */
    public function getTaxes()
    {
        return $this->getSubTotal() * static::TAX_PERCENTAGE;
    }

    /**
     * Get Total Amount The User Has To Pay
     * Converted to User Currency.
     *
     * @return float|int|mixed
     */
    public function getTotal()
    {
        return $this->getSubTotal() - $this->totalDiscountAmount() + $this->getTaxes();
    }

    /**
     * Concert any amount from USD to User selected Currency.
     *
     * @param $amount
     * @return mixed
     */
    protected function convertToUserCurrency($amount)
    {
        return $this->currencyConverter->amount($amount)->convert();
    }

    /**
     * Find the Offers that matches User selected Products
     * and Apply the Offer conditions to give the Discount.
     */
    private function setDiscounts()
    {
        $this->discounts = $this->offers->map(function ($offer) {
            $item = $this->getItem(Product::findByName($offer->offer_on));

            if (!$item || $item->offer_applied_on >= $item->quantity) return;

            $item->offer_applied_on++;
            return (object)[
                'offer' => $offer,
                'amount' => $item->product->price * $offer->offer_percentage
            ];
        })->filter()->toArray();
    }

    /**
     * Get Total Discount Amount.
     *
     * @return mixed
     */
    public function totalDiscountAmount()
    {
        return $this->convertToUserCurrency(
            collect($this->discounts)->reduce(fn($current, $next) => $current + $next->amount, 0)
        );
    }

    /**
     * 1- Get The Appropriate Offers that matches the name of The selected Products.
     * 2- Set The Applied Offers with The following Criteria:
     *      a- Find the item with its Quantity that when I buy it i can get the Offer
     *      b- Check if the Remaining Item Quantity covers the Offer Quantity that I have to buy to get it
     *      c- Repeat the same Offer if Quantity I buy can cover the same Offer
     *
     * @return \Illuminate\Support\Collection
     */
    private function setAppliedOffers()
    {
        $offers = $this->findAppropriateOffers();
        $appliedOffers = collect();

        for ($i = 0; $i < $offers->count(); $i++) {
            $item = $this->getItem(Product::findByName($offers[$i]->when_you_buy));

            if ($offers[$i]->amount_to_buy > ($item->quantity - $item->used_in_offers)) {
                continue;
            }

            $item->used_in_offers += $offers[$i]->amount_to_buy;
            $appliedOffers[] = $offers[$i];
            $i--;
        }

        $this->offers = $appliedOffers;
    }

    /**
     * Find collection of Offers that matches User selected Products.
     *
     * @return \Illuminate\Support\Collection
     */
    private function findAppropriateOffers()
    {
        return Offer::findOffersOnProducts(
            $this->getItems()->map(fn($item) => $item->product->name)->toArray()
        );
    }
}
