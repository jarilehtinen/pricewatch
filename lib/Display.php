<?php

namespace PriceWatch;

use PriceWatch\Stores;

class Display
{
    private $stores;
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
        $this->stores = new Stores;
    }

    /**
     * Display product ID
     *
     * @param integer $i              Product ID
     * @param integer $total_products Number of total products
     */
    public function displayProductId($id, $total_products)
    {
        $max_id_length = strlen($total_products) + 1;
        
        $id = '#'.($id);
        $id = str_pad($id, $max_id_length, ' ', STR_PAD_LEFT);

        return $this->yellow.$this->bold.$id.$this->reset;
    }

    /**
     * Display store name
     *
     * @param  object $data Product data
     * @return string
     */
    public function displayStoreName($store_name)
    {
        $output = $this->cyan;
        
        if (!$store_name) {
            $name = '(store not set)';
        }

        $output .= str_pad(
            $store_name,
            $this->longestStoreNameLength(),
            ' '
        );

        $output .= $this->reset;

        return $output;
    }

    /**
     * Display product title
     *
     * @param  string $data Product title
     * @return string
     */
    public function displayProductTitle($title)
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
     * @param  object $data Product data
     * @return string
     */
    public function displayPrice($data)
    {
        if (!$data->product->price) {
            return false;
        }

        // Price decreased
        if ($data->product->priceDecreased) {
            $output = $this->green_bold;
            $output .= $this->pricePad($data->product->price).'€';
            $output .= ' ▼ ';
            $output .= '('.number_format($data->product->lastPrice, 2, ',', '').'€)';
            $output .= $this->reset;
            return $output;
        }

        // Price increased
        if ($data->product->priceIncreased) {
            $output = $this->red_bold;
            $output .= $this->pricePad($data->product->price).'€';
            $output .= ' ▲ ';
            $output .= '('.number_format($data->product->lastPrice, 2, ',', '').'€)';
            $output .= $this->reset;
            return $output;
        }

        $output = $this->cyan;
        $output .= $this->pricePad($data->product->price).'€';
        $output .= $this->reset;

        return $output;
    }

    /**
     * Price pad
     *
     * @param  double $price
     * @return string
     */
    public function pricePad($price)
    {
        $length = 8;
        $price = number_format($price, 2, ',', '');

        if (strlen($price) < $length) {
            return str_pad($price, $length, ' ', STR_PAD_LEFT);
        }

        return $price;
    }

    /**
     * Longest store name length
     */
    private function longestStoreNameLength()
    {
        if ($this->longest_store_name_length) {
            return $this->longest_store_name_length;
        }

        $this->longest_store_name_length = $this->stores->getLongestStoreNameLength();

        return $this->longest_store_name_length;
    }
}
