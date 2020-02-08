# PriceWatch

Simple command line tool to get prices for the products you want to buy. It logs the prices and tells you if it's on sale.

## Installation

Easiest way to use PriceWatch is to create an alias for ```index.php``` in your ```~/.bashrc``` or ```~/.zshrc``` like this:

```shell
alias pricewatch="php /path/to/your/pricewatch/index.php"
```

Then run:

```shell
pricewatch build
```

It creates the ```stores.json``` file containing all the store information.

## Usage

**Add product**

```shell
pricewatch add [url]
```

**Get prices**

```shell
pricewatch
```

**Remove product**

```shell
pricewatch remove [id]
```

*id* is the list item number when you run ```pricewatch```

## Supported stores

- verkkokauppa.com
- power.fi
- gigantti.fi

Although you can add more stores to ```stores.php``` and run:

```shell
pricewatch build
```

