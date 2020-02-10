<?php

namespace PriceWatch;

use PriceWatch\Products;
use PriceWatch\Tools;

class Log
{
    private $products;
    private $tools;
    private $log;
    private $red = "\e[0;31m";
    private $green = "\e[0;32m";
    private $cyan = "\e[0;36m";
    private $reset_color = "\e[0m";

    /**
     * Read log
     *
     * @return array
     */
    public function readLog()
    {
        if ($this->log) {
            return $this->log;
        }

        $log = trim(file_get_contents(PATH.'/log.txt'));

        if (!$log) {
            return false;
        }

        $log = explode("\n", trim($log));
        
        foreach ($log as $entry) {
            $entry = explode('##', $entry);

            $date = $entry[0];
            $url = $entry[1];
            $price = $entry[2];

            $this->log[$url][] = array(
                'date' => $date,
                'price' => $price
            );
        }

        return $this->log;
    }

    /**
     * Log price
     *
     * @param  string  $url   Product URL
     * @param  string  $title Product title
     * @param  double  $price Product price
     * @return boolean
     */
    public function logPrice($url, $title, $price)
    {
        $data = date('Y-m-d H:i:s').'##'.$url.'##'.$price."\n";
        return file_put_contents(PATH.'/log.txt', $data, FILE_APPEND);
    }

    /**
     * Display price log
     *
     * @param  integer $product_id Product ID
     * @return null
     */
    public function dipslayPriceLog($product_id)
    {
        $this->products = new Products;
        $this->tools = new Tools;

        // Get products
        $products = $this->products->getProducts();

        if (!$products) {
            echo "No products found.\n";
            exit;
        }

        if (!isset($products[$product_id-1])) {
            echo "No product found with ID ".$product_id."\n";
            exit;
        }

        // Product URL
        $url = $products[$product_id-1];

        // Get log entries
        $log = $this->readLog();
        $last_price = false;

        foreach ($log[$url] as $entry) {
            if ($entry['price']) {
                $price_increased = $last_price && $entry['price'] > $last_price;
                $price_decreased = $last_price && $entry['price'] < $last_price;

                // Date
                echo date('d.m.Y H:i', strtotime($entry['date'])).' ';

                // Price change indicator colors
                if ($price_increased) {
                    echo $this->red;
                } elseif ($price_decreased) {
                    echo $this->green;
                } else {
                    echo $this->cyan;
                }

                // Price
                echo $this->tools->pricePad($entry['price']).'€';

                // Up/down arrows
                if ($price_increased) {
                    echo ' ▲';
                } elseif ($price_decreased) {
                    echo ' ▼';
                }

                echo $this->reset_color;
                echo "\n";

                $last_price = $entry['price'];
            }
        }
    }
}
