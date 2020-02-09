<?php

namespace PriceWatch;

class Log
{
    private $log;

    /**
     * Read log
     */
    public function readLog()
    {
        if ($this->log) {
            return $this->log;
        }

        $log = trim(file_get_contents('log.txt'));

        if (!$log) {
            return false;
        }

        $log = explode("\n", trim($log));
        
        foreach ($log as $line) {
            $line = explode('##', $line);

            $date = $line[0];
            $url = $line[1];
            $price = $line[2];

            $this->log[$url][] = $price;
        }

        return $this->log;
    }

    /**
     * Log price
     */
    public function logPrice($url, $title, $price)
    {
        $data = date('Y-m-d H:i:s').'##'.$url.'##'.$price."\n";
        file_put_contents('log.txt', $data, FILE_APPEND);
    }

    /**
     * Display price log
     */
    public function dipslayPriceLog($product_id)
    {
        $this->products = new Products;
        $this->tools = new Tools;

        $products = $this->products->getProducts();

        if (!$products) {
            echo "No products found.\n";
            exit;
        }

        if (!isset($products[$product_id-1])) {
            echo "No product found with ID ".$product_id."\n";
            exit;
        }

        $url = $products[$product_id-1];

        $log = $this->readLog();
        $last_price = false;

        foreach ($log[$url] as $entry) {
            if ($entry['price']) {
                $price_increased = $last_price && $entry['price'] > $last_price;
                $price_decreased = $last_price && $entry['price'] < $last_price;

                echo date('d.m.Y H:i', strtotime($entry['date'])).' ';

                if ($price_increased) {
                    echo $this->red;
                } elseif ($price_decreased) {
                    echo $this->green;
                } else {
                    echo $this->cyan;
                }

                echo $this->tools->pricePad($entry['price']).'€';

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
