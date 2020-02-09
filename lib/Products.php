<?php

namespace PriceWatch;

class Products
{
    private $products;
    private $cyan = "\e[0;36m";
    private $reset_color = "\e[0m";

    /**
     * Get products
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
     */
    public function getProduct($i)
    {
        $products = $this->getProducts();
        return $products[$i-1];
    }

    /**
     * Add product
     */
    public function addProduct($url)
    {
        return file_put_contents('products.txt', $url."\n", FILE_APPEND);
    }

    /**
     * Remove product
     */
    public function removeProduct($i)
    {
        $products = $this->getProducts();
        unset($products[$i-1]);
        
        return file_put_contents('products.txt', implode("\n", $products)."\n");
    }

    /**
     * Show product
     */
    public function showProduct($i)
    {
        $product = $this->getProduct($i);
        
        echo $this->cyan.'URL: '.$this->reset_color.$product."\n";
    }
}
