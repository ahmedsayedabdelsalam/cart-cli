<?php


namespace App\Cart\Models;


use Illuminate\Support\Str;

class Offer extends Model
{
    private static array $offers = [
        [
            'id' => 1,
            'name' => '10% off Shoes',
            'when_you_buy' => 'Shoes',
            'amount_to_buy' => 1,
            'offer_on' => 'Shoes',
            'offer_percentage' => 0.1
        ],
        [
            'id' => 2,
            'name' => '50% off Jacket',
            'when_you_buy' => 'T-shirt',
            'amount_to_buy' => 2,
            'offer_on' => 'Jacket',
            'offer_percentage' => 0.5
        ],
    ];

    public static function findOffersOnProducts(array $products)
    {
        $products = collect($products)->map(fn($product) => Str::lower($product))->toArray();

        return collect(self::$offers)
            ->filter(fn ($offer) => in_array(Str::lower($offer['when_you_buy']), $products))
            ->map(fn($offer) => new self($offer));
    }
}
