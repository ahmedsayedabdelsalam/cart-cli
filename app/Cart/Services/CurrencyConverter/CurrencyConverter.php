<?php


namespace App\Cart\Services\CurrencyConverter;

abstract class CurrencyConverter
{
    protected string $to = 'USD';

    protected float $amount;
}
