<?php


namespace App\Cart\Services\CurrencyConverter;


use App\Exceptions\InvalidCurrencyException;
use Illuminate\Support\Arr;

abstract class CurrencyConverter
{
    protected string $to = 'USD';

    protected float $amount;
}
