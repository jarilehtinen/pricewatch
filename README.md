# PriceWatch

Simple command-line tool to get prices for the products you want to buy. It keeps a log of the prices and tells you if a product is on sale.

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

Note: *id* is the list item number in ```pricewatch``` results

**List logged product prices**

```shell
pricewatch log [id]
```

Note: *id* is the list item number in ```pricewatch``` results

## Supported stores

- discshop.fi
- gigantti.fi
- jimms.fi
- multitronic.fi
- power.fi
- proshop.fi
- verkkokauppa.com
- vpd.fi

You can add more stores to ```stores.php``` and run:

```shell
pricewatch build
```

