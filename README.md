# Command Line Cart Bill.
Please organize, design, test, document, and deploy your code as if it were
going into production.

## Description

***accept multiple products, combine offers, and display a total detailed bill in different currencies (based on user selection).***

Available catalog products and their price in USD:

* T-shirt $10.99
* Pants $14.99
* Jacket $19.99
* Shoes $24.99

The program can handle some special offers, which affect the pricing.

Available offers:

* Shoes are on 10% off.
* Buy two t-shirts and get a jacket half its price.

The program accepts a list of products, outputs the detailed bill of the subtotal, tax, and discounts if applicable, bill can be displayed in various currencies.

*There is a 14% tax (before discounts) applied to all products.*

E.g.:

Adding the following products:

```
T-shirt
T-shirt
Shoes
Jacket
```

Outputs the following bill, the user selected the USD bill:

```
Subtotal: $66.96
Taxes: $9.37
Discounts:
	10% off shoes: -$2.499
	50% off jacket: -$9.995
Total: $63.8404
```

Another, e.g., If none of the offers are eligible, the user selected the EGP bill:

```
T-shirt
Pants
```

Outputs the following bill:

```
Subtotal: 409 e£
Taxes: 57 e£
Total: 467 e£
```
  
## Requirements
1. PHP >= 7.4
1. composer. 
  
## How to Run
1. clone the project.
1. cd to the directory. 
1. run `composer install`
1. run ` php cart-cli cart:create T-shirt T-shirt Shoes Jacket`
1. from more help about the command run ` php cart-cli cart:create -h`
  
## TODO
1. add live currency converter.
1. add data persistence layer for models like products, offers. 
1. add more tests

## Contribution
Don't hesitate to submit new tested features.
