<?php

namespace PriceWatch;

class Products
{
    private $products;

    /**
     * Get products
     */
    public function getProducts()
    {
        if (!file_exists('products.txt')) {
            echo "No products found. Add product: pricewatch add [url]\n";
        }

        $products = file_get_contents('products.txt');
        $products = explode("\n", trim($products));
        return $products;
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
        file_put_contents('products.txt', implode("\n", $products)."\n");
    }
}
