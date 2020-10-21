<?php


namespace App\Cart\Services\CurrencyConverter;


use App\Exceptions\InvalidCurrencyException;

class LocalCurrencyConverter extends CurrencyConverter implements Contracts\CurrencyConverter
{
    public function getConversions()
    {
        return [
            'USD' => ['ratio' => 1, 'symbol' => '$'],
            'EGP' => ['ratio' => 15.7, 'symbol' => '£']
        ];
    }

    public function to(string $currency)
    {
        if (empty($this->getConversions()[$currency]))
            throw new InvalidCurrencyException("Currency $currency Not Found.");

        $this->to = $currency;

        return $this;
    }

    public function amount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    public function selectedCurrency()
    {
        return (object) $this->getConversions()[$this->to];
    }

    public function convert()
    {
        return $this->selectedCurrency()->ratio * $this->amount;
    }
}
