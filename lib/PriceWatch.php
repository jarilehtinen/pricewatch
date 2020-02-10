<?php

namespace PriceWatch;

use PriceWatch\Stores;
use PriceWatch\Products;
use PriceWatch\Log;
use PriceWatch\Tools;

class PriceWatch
{
    private $stores;
    private $products;
    private $parser;
    private $log;
    private $tools;
    private $data;
    private $longest_store_name_length;
    private $max_title_length = 55;

    private $red = "\e[0;31m";
    private $red_bold = "\e[1;31m";
    private $green = "\e[0;32m";
    private $green_bold = "\e[1;32m";
    private $yellow = "\e[0;33m";
    private $yellow_bold = "\e[1;33m";
    private $cyan = "\e[0;36m";
    private $cyan_bold = "\e[1;36m";
    private $reset = "\e[0m";

    /**
     * Construct
     */
    public function __construct()
    {
        $this->products = new Products;
        $this->stores = new Stores;
        $this->parser = new Parser;
        $this->log = new Log;
        $this->tools = new Tools;
    }

    /**
     * Output prices
     */
    public function outputPrices()
    {
        // Get products
        $this->longest_store_name_length = $this->parser->longestStoreNameLength();

        // Get stores
        $stores = $this->stores->getStores();

        // Display products
        $products = $this->products->getProducts();
        $total_products = count($products);

        foreach ($products as $i => $url) {
            // Get data
            $data = $this->parser->getData($url);

            // Store not configured
            if (!$data->store) {
                $store_id = $this->stores->getStoreIdFromUrl($url);
                echo $this->displayProductId($i, $total_products).'  ';
                echo $this->red.'Warning: store '.$store_id.' not set in stores.json '.$this->reset."\n";
                continue;
            }

            if (!$data->product) {
                // Could not get product data for URL
                echo $this->red.'Could not get product data for '.$url.$this->reset."\n";
                continue;
            }

            // Product ID
            echo $this->displayProductId($i, $total_products).'  ';

            // Store name
            echo $this->displayStoreName($data).'  ';

            // Product
            echo $this->displayProductTitle($data).'  ';

            // Last price
            echo $this->displayPrice($data);

            echo "\n";

            $this->log->logPrice($url, $data->product->title, $data->product->price);
        }
    }

    /**
     * Display product ID
     *
     * @param integer $i              Product ID
     * @param integer $total_products Number of total products
     */
    private function displayProductId($i, $total_products)
    {
        $max_i_length = strlen($total_products) + 1;
        
        $i = '#'.($i+1);
        $i = str_pad($i, $max_i_length, ' ', STR_PAD_LEFT);

        return $this->yellow.$this->bold.$i.$this->reset;
    }

    /**
     * Display store name
     *
     * @param  object $data Product data
     * @return string
     */
    private function displayStoreName($data)
    {
        $output = $this->cyan;
        
        if (!$data->store->name) {
            $name = '(store not set)';
        }

        $output .= str_pad(
            $data->store->name,
            $this->longest_store_name_length,
            ' '
        );

        $output .= $this->reset;

        return $output;
    }

    /**
     * Display product title
     *
     * @param  object $data Product data
     * @return string
     */
    private function displayProductTitle($data)
    {
        $orig_length = strlen($data->product->title);

        if ($orig_length >= $this->max_title_length) {
            return mb_substr($data->product->title, 0, $this->max_title_length - 3).'...';
        }
    
        return str_pad($data->product->title, $this->max_title_length, ' ');
    }

    /**
     * Display price
     *
     * @param  object $data Product data
     * @return string
     */
    private function displayPrice($data)
    {
        if (!$data->product->price) {
            return false;
        }

        // Price decreased
        if ($data->product->priceDecreased) {
            $output = $this->green_bold;
            $output .= $this->tools->pricePad($data->product->price).'€';
            $output .= ' ▼ ';
            $output .= '('.number_format($data->product->lastPrice, 2, ',', '').'€)';
            $output .= $this->reset;
            return $output;
        }

        // Price increased
        if ($data->product->priceIncreased) {
            $output = $this->red_bold;
            $output .= $this->tools->pricePad($data->product->price).'€';
            $output .= ' ▲ ';
            $output .= '('.number_format($data->product->lastPrice, 2, ',', '').'€)';
            $output .= $this->reset;
            return $output;
        }

        $output = $this->cyan;
        $output .= $this->tools->pricePad($data->product->price).'€';
        $output .= $this->reset;

        return $output;
    }

    /**
     * Run command
     *
     * @param  array   $args Arguments
     * @return boolean
     */
    public function runCommand($args)
    {
        $command = $args[1];

        // Add product
        if ($command == 'add') {
            if (!isset($args[2])) {
                echo "Usage: pricewatch add <url>\n";
                return false;
            }

            $this->products->addProduct($args[2]);
            return true;
        }

        // Remove product
        if ($command == 'remove') {
            if (!isset($args[2])) {
                echo "Usage: pricewatch remove <id|url>\n";
                return false;
            }

            $this->products->removeProduct($args[2]);
            return true;
        }

        // Product info
        if ($command == 'info') {
            if (!isset($args[2])) {
                echo "Usage: pricewatch info <id>\n";
                return false;
            }

            $this->products->displayProduct($args[2]);
            return true;
        }

        // Build stores
        if ($command == 'build') {
            $this->stores->buildStores();
            return true;
        }

        // Price log
        if ($command == 'log') {
            if (!isset($args[2])) {
                echo "Usage: pricewatch log <id>\n";
                return false;
            }

            $this->log->dipslayPriceLog($args[2]);
            return true;
        }

        // List stores
        if ($command == 'stores') {
            $this->stores->displayStores();
            return true;
        }

        // Help
        $help = file_get_contents(PATH.'/usage.txt');
        echo $help."\n";
        return true;
    }
}
