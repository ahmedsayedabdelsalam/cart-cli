<?php

namespace App\Commands;

use App\Cart\Cart;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class CreateCart extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'cart:create
                             {products* : List of Products you want to add to the Cart}
                             {--c|bill-currency=USD : The currency you would like to have the Bill with (Ex: USD,EGP,..)}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create Cart of Products and get the Bill';

    /**
     * Execute the console command.
     *
     * @param Cart $cart
     * @return mixed
     * @throws \App\Exceptions\InvalidCurrencyException
     */
    public function handle(Cart $cart)
    {
        $cart->addItems($this->argument('products'));
        $cart->setCurrency($this->option('bill-currency'));
        $currencySymbol = $cart->getCurrencySymbol();
        $subtotal = $cart->getSubTotal();
        $taxes = $cart->getTaxes();
        $total = $cart->getTotal();

        $this->info("Subtotal: {$currencySymbol}{$subtotal}");
        $this->info("Taxes: {$currencySymbol}{$taxes}");
        if ($discounts = $cart->getDiscounts()) {
            $this->info('Discounts:');
            foreach ($discounts as $discount) {
                $this->info('       10% off shoes: -$2.499');
                $this->info('       50% off jacket: -$12.495');
            }
        }
        $this->info("Total: {$currencySymbol}{$total}");
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
