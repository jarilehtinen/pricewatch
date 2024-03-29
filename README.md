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
pricewatch add <url>
```

**Get prices**

```powershell
pricewatch
```

**Get prices in JSON format**

```shell
pricewatch json
```

**Remove product**

```shell
pricewatch remove <id|url>
```

<id> is the list item number in ```pricewatch``` results

Note: when removing several products with ID, please note that each product's ID is updated on every product removal

**Swap product places in list**

```shell
pricewatch swap <id> <id>
```

<id> is the list item number in ```pricewatch``` results

**Display product info**

```shell
pricewatch info <id>
```

<id> is the list item number in ```pricewatch``` results

**List logged product prices**

```shell
pricewatch log <id>
```

<id> is the list item number in ```pricewatch``` results

**List added products**

```shell
pricewatch products
```

**List supported stores**

```shell
pricewatch stores
```

**Build store information**

```shell
pricewatch build
```

**Getting help in terminal**

```shell
pricewatch help
```

## Supported stores

- audiokauppa.fi
- discshop.fi
- gigantti.fi
- hifihuone.fi
- hifistudio.fi
- jimms.fi
- konsolinet.fi
- maxgaming.fi
- multitronic.fi
- pelaajashop.fi
- proshop.fi
- puolenkuunpelit.com
- verkkokauppa.com
- vpd.fi

You can add more stores to ```stores.php``` and run:

```shell
pricewatch build
```

