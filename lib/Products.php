<?php

namespace PriceWatch;

class Products
{
    private $products;
    private $cyan = "\e[0;36m";
    private $reset_color = "\e[0m";

    /**
     * Get products
     *
     * @return array
     */
    public function getProducts()
    {
        if (!file_exists('products.txt')) {
            echo "No products found. Add product: pricewatch add <url>\n";
            exit;
        }

        $products = trim(file_get_contents('products.txt'));

        if (!$products) {
            echo "No products found. Add product: pricewatch add <url>\n";
            exit;
        }
        
        $products = explode("\n", trim($products));

        return $products;
    }

    /**
     * Get product
     *
     * @param  integer $i Product ID
     * @return string
     */
    public function getProduct($i)
    {
        $products = $this->getProducts();
        return $products[$i-1];
    }

    /**
     * Add product
     *
     * @param  string  $url URL
     * @return boolean
     */
    public function addProduct($url)
    {
        return file_put_contents('products.txt', $url."\n", FILE_APPEND);
    }

    /**
     * Remove product
     *
     * @param  integer $i Product ID
     * @return boolean
     */
    public function removeProduct($i)
    {
        $products = $this->getProducts();
        unset($products[$i-1]);
        
        return file_put_contents('products.txt', implode("\n", $products)."\n");
    }

    /**
     * Show product
     *
     * @param integer $i Product ID
     */
    public function showProduct($i)
    {
        $product = $this->getProduct($i);
        
        echo $this->cyan.'URL: '.$this->reset_color.$product."\n";
    }
}
