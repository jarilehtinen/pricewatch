<?php

namespace PriceWatch;

use PriceWatch\Stores;
use PriceWatch\Products;
use PriceWatch\Log;

class PriceWatch
{
    private $stores;
    private $products;
    private $parser;
    private $log;
    private $data;
    private $longest_store_name_length;
    private $max_title_length = 55;

    private $red = "\e[0;31m";
    private $green = "\e[0;32m";
    private $yellow = "\e[0;33m";
    private $cyan = "\e[0;36m";
    private $gray = "\e[0;37m";
    private $reset_color = "\e[0m";

    /**
     * Construct
     */
    public function __construct()
    {
        $this->products = new Products;
        $this->stores = new Stores;
        $this->parser = new Parser;
        $this->log = new Log;
    }

    /**
     * Output prices
     */
    public function outputPrices()
    {
        // Get products
        $this->longest_store_name_length = $this->parser->longestStoreNameLength();

        // Display products
        $products = $this->products->getProducts();

        foreach ($products as $i => $url) {
            // Get URL data
            $data = $this->parser->getData($url);

            if ($data) {
                // Product ID
                echo $this->displayProductId($i).' ';

                // Store name
                echo $this->displayStoreName($data->name).'  ';

                // Product
                echo $this->displayProductTitle($data->title).'  ';

                // Last price
                if ($data->price) {
                    echo $this->displayPrice($data->price, $data->lastPrice);
                }

                echo "\n";

                $this->log->logPrice($url, $data->title, $data->price);
            } else {
                echo $this->red.'Could not get data for '.$url.$this->reset_color."\n";
            }
        }
    }

    /**
     * Display product ID
     */
    private function displayProductId($i)
    {
        return $this->yellow.'['.($i+1).']'.$this->reset_color;
    }

    /**
     * Display store name
     */
    private function displayStoreName($name)
    {
        $output = $this->cyan;
        
        $output .= str_pad(
            $name,
            $this->longest_store_name_length,
            ' '
        );

        $output .= $this->reset_color;

        return $output;
    }

    /**
     * Display product title
     */
    private function displayProductTitle($title)
    {
        $orig_length = strlen($title);

        if ($orig_length >= $this->max_title_length) {
            return mb_substr($title, 0, $this->max_title_length - 3).'...';
        }
    
        return str_pad($title, $this->max_title_length, ' ');
    }

    /**
     * Price pad
     */
    private function pricePad($price)
    {
        $length = 8;
        $price = number_format($price, 2, ',', '');

        if (strlen($price) < $length) {
            return str_pad($price, $length, ' ', STR_PAD_LEFT);
        }

        return $price;
    }

    /**
     * Display price
     */
    private function displayPrice($price, $last_price = false)
    {
        // Is it on sale?
        $on_sale = $last_price && $price < $last_price ? true : false;

        if ($on_sale) {
            $output = $this->cyan;
            $output .= $this->pricePad($price).'€';
            $output .= $this->green;
            $output .= ' – On sale! Old price was '.number_format($last_price, 2, ',', '').'€';
            $output .= $this->reset_color;
            return $output;
        }

        // Price increased?
        $price_increased = $last_price && $price > $last_price ? true : false;

        if ($price_increased) {
            $output = $this->cyan;
            $output .= $this->pricePad($price).'€';
            $output .= $this->red;
            $output .= ' – Price increased! Old price was '.number_format($last_price, 2, ',', '').'€';
            $output .= $this->reset_color;
            return $output;
        }

        $output = $this->cyan;
        $output .= $this->pricePad($price).'€';
        $output .= $this->reset_color;

        return $output;
    }

    /**
     * Run command
     */
    public function runCommand($args)
    {
        $command = $args[1];

        // Add product
        if ($command == 'add') {
            if (!isset($args[2])) {
                echo "Usage: pricewatch add [url]\n";
                return false;
            }

            $this->products->addProduct($args[2]);
            return true;
        }

        // Remove product
        if ($command == 'remove') {
            if (!isset($args[2])) {
                echo "Usage: pricewatch remove [id]\n";
                return false;
            }

            $this->products->removeProduct($args[2]);
            return true;
        }

        // Build stores
        if ($command == 'build') {
            $this->stores->buildStores();
            return true;
        }
    }
}
