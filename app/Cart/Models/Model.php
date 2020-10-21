<?php


namespace App\Cart\Models;


class Model
{
    private array $attributes = [];

    public function __construct($attributes = null)
    {
        $this->attributes = $attributes ?? [];
    }

    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }
}
