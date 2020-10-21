<?php


namespace App\Cart\Models;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Product
{
    private static array $products = [
        [
            'id' => 1,
            'name' => 'T-shirt',
            'price' => 10.99
        ],
        [
            'id' => 2,
            'name' => 'Pants',
            'price' => 14.99
        ],
        [
            'id' => 3,
            'name' => 'Jacket',
            'price' => 19.99
        ],
        [
            'id' => 4,
            'name' => 'Shoes',
            'price' => 24.99
        ],
    ];

    private array $attributes = [];

    public function __construct($attributes = null)
    {
        $this->attributes = $attributes ?? [];
    }

    public static function findByName(string $name)
    {
        $product = Arr::first(
            static::$products,
            fn ($product) => Str::lower( $product['name']) === Str::lower($name)
        );

        return $product ? new self($product) : null;
    }

    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }
}
