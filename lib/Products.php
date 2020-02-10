<?php

namespace PriceWatch;

class Products
{
    private $products;
    private $cyan = "\e[0;36m";
    private $reset = "\e[0m";

    /**
     * Get products
     *
     * @return array
     */
    public function getProducts()
    {
        if (!file_exists(PATH.'/products.txt')) {
            echo "No products found. Add product: pricewatch add <url>\n";
            exit;
        }

        $products = trim(file_get_contents(PATH.'/products.txt'));

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
        return file_put_contents(PATH.'/products.txt', $url."\n", FILE_APPEND);
    }

    /**
     * Remove product
     *
     * @param  mixed   $id Product ID or URL
     * @return boolean
     */
    public function removeProduct($id)
    {
        if (is_numeric($id)) {
            return $this->removeProductById($id);
        }
      
        return $this->removeProductByUrl($id);
    }

    /**
     * Remove product by ID
     */
    public function removeProductById($id)
    {
        $products = $this->getProducts();

        if (!isset($products[$id-1])) {
            return false;
        }

        unset($products[$id-1]);
        
        return file_put_contents(PATH.'/products.txt', implode("\n", $products)."\n");
    }

    /**
     * Remove product by URL
     */
    public function removeProductByUrl($url)
    {
        $products = $this->getProducts();

        foreach ($products as $i => $product) {
            if ($product == trim($url)) {
                unset($products[$i]);
                break;
            }
        }

        return file_put_contents(PATH.'/products.txt', implode("\n", $products)."\n");
    }

    /**
     * Display product
     *
     * @param integer $i Product ID
     */
    public function displayProduct($i)
    {
        $product = $this->getProduct($i);
        
        echo $this->cyan.'URL: '.$this->reset.$product."\n";
    }
}
