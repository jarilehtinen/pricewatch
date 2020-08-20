<?php

namespace PriceWatch;

use PriceWatch\Stores;
use PriceWatch\Display;
use PriceWatch\Parser;
use PriceWatch\Products;
use PriceWatch\Log;
use PriceWatch\JSON;

class PriceWatch
{
    private $stores;
    private $products;
    private $parser;
    private $log;
    private $data;
    private $json;

    private $red = "\e[0;31m";
    private $reset = "\e[0m";

    /**
     * Construct
     */
    public function __construct()
    {
        $this->products = new Products;
        $this->stores = new Stores;
        $this->parser = new Parser;
        $this->display = new Display;
        $this->log = new Log;
        $this->json = new JSON;
    }

    /**
     * Output prices
     */
    public function outputPrices()
    {
        // Get stores
        $stores = $this->stores->getStores();

        // Display products
        $products = $this->products->getProducts();
        $total_products = count($products);

        foreach ($products as $id => $product) {
            // Get data
            $data = $this->parser->getData($id, $product->url);

            // Store not configured
            if (!$data->store) {
                $store_id = $this->stores->getStoreIdFromUrl($product->url);
                echo $this->display->displayProductId($i, $total_products).'  ';
                echo $this->red.'Warning: store '.$store_id.' not set in stores.json '.$this->reset."\n";
                continue;
            }

            if (!$data->product) {
                // Could not get product data for URL
                echo $this->red.'Could not get product data for '.$product->url.$this->reset."\n";
                continue;
            }

            // Product ID
            echo $this->display->displayProductId($id, $total_products).'  ';

            // Store name
            echo $this->display->displayStoreName($data->store->name).'  ';

            // Product
            echo $this->display->displayProductTitle($data->product->title).'  ';

            // Last price
            echo $this->display->displayPrice($data);

            echo "\n";

            $this->log->logPrice($product->url, $data->product->title, $data->product->price);
        }
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

        // JSON
        if ($command == 'json') {
            $this->json->outputJSON();
            return true;
        }

        // Add product
        if ($command == 'add') {
            if (!isset($args[2])) {
                echo "Usage: pricewatch add <url>\n";
                return false;
            }

            $this->products->addProduct($args[2]);

            // Display products after adding
            $this->products->displayProducts();

            return true;
        }

        // Remove product
        if ($command == 'remove') {
            if (!isset($args[2])) {
                echo "Usage: pricewatch remove <id|url>\n";
                return false;
            }

            $this->products->removeProduct($args[2]);

            // Display products after removing
            $this->products->displayProducts();

            return true;
        }

        // Swap products
        if ($command == 'swap') {
            if (!isset($args[2]) || !isset($args[3])) {
                echo "Usage: pricewatch swap <id> <id>\n";
                return false;
            }

            $this->products->swapProductPlace($args[2], $args[3]);

            // Display products after swapping
            $this->products->displayProducts();
            
            return true;
        }

        // Display products
        if ($command == 'products') {
            $this->products->displayProducts();
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
