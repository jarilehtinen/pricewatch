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
            // Get store
            $store_id = $this->stores->getStoreIdFromURL($url);
            $store = $this->stores->getStore($store_id);

            if ($store) {
                // Get URL data
                $data = $this->parser->getData($url);

                // Product ID
                echo $this->displayProductId($i, $total_products).'  ';

                if ($data) {
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
                    // Could not get data for URL
                    echo $this->red.'Could not get data for '.$url.$this->reset."\n";
                }
            } else {
                // Store not set
                echo $this->displayProductId($i, $total_products).'  ';
                echo $this->red.'Warning: store '.$store_id.' not set in stores.json '.$this->reset."\n";
            }
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
     * @param  string $name Store name
     * @return string
     */
    private function displayStoreName($name)
    {
        $output = $this->cyan;
        
        if (!$name) {
            $name = '(store not set)';
        }

        $output .= str_pad(
            $name,
            $this->longest_store_name_length,
            ' '
        );

        $output .= $this->reset;

        return $output;
    }

    /**
     * Display product title
     *
     * @param  string $title Product title
     * @return string
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
     * Display price
     *
     * @param  double $price      Price
     * @param  mixed  $last_price Last price
     * @return string
     */
    private function displayPrice($price, $last_price = false)
    {
        // Is it on sale?
        $on_sale = $last_price && $price < $last_price ? true : false;

        if ($on_sale) {
            $output = $this->green_bold;
            $output .= $this->tools->pricePad($price).'€';
            $output .= ' ▼ ';
            $output .= '('.number_format($last_price, 2, ',', '').'€)';
            $output .= $this->reset;
            return $output;
        }

        // Price increased?
        $price_increased = $last_price && $price > $last_price ? true : false;

        if ($price_increased) {
            $output = $this->red_bold;
            $output .= $this->tools->pricePad($price).'€';
            $output .= ' ▲ ';
            $output .= '('.number_format($last_price, 2, ',', '').'€)';
            $output .= $this->reset;
            return $output;
        }

        $output = $this->cyan;
        $output .= $this->tools->pricePad($price).'€';
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
                echo "Usage: pricewatch remove <id>\n";
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

            $this->products->showProduct($args[2]);
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

        // Help
        if ($command == 'help' || $command == '--help' || $command == '-h') {
            $help = file_get_contents(PATH.'/usage.txt');
            echo $help."\n";
            return true;
        }
    }
}
