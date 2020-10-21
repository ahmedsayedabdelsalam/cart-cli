<?php


namespace App\Cart\Services\CurrencyConverter\Contracts;


interface CurrencyConverter
{
    public function getConversions();

    public function amount($amount);

    public function to(string $currency);

    public function selectedCurrency();

    public function convert();
}
