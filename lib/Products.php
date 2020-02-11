<?php

namespace PriceWatch;

use PriceWatch\Stores;
use PriceWatch\Display;

class Products
{
    private $stores;
    private $display;
    private $products;
    private $cyan = "\e[0;36m";
    private $yellow = "\e[0;33m";
    private $reset = "\e[0m";

    /**
     * Construct
     */
    public function __construct()
    {
        $this->stores = new Stores;
        $this->display = new Display;
    }

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

        $product_data = trim(file_get_contents(PATH.'/products.txt'));

        if (!$product_data) {
            echo "No products found. Add product: pricewatch add <url>\n";
            exit;
        }
        
        $product_data = explode("\n", trim($product_data));

        $i = 1;
        foreach ($product_data as $product) {
            $products[$i] = (object)[];
            $products[$i]->id = $i;
            $products[$i]->url = $product;
            $i++;
        }

        return $products;
    }

    /**
     * Get product
     *
     * @param  integer $id Product ID
     * @return string
     */
    public function getProduct($id)
    {
        $products = $this->getProducts();

        if (!isset($products[$id])) {
            return false;
        }

        return $products[$id];
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

        if (!isset($products[$id])) {
            return false;
        }

        unset($products[$id]);

        return $this->saveProductConfig($products);
    }

    /**
     * Remove product by URL
     */
    public function removeProductByUrl($url)
    {
        $products = $this->getProducts();

        foreach ($products as $id => $product) {
            if ($product == trim($url)) {
                unset($products[$id]);
                break;
            }
        }

        return $this->saveProductConfig($products);
    }

    /**
     * Swap product place
     */
    public function swapProductPlace($id1, $id2)
    {
        $products = $this->getProducts();

        $temp = $products[$id1];
        $products[$id1] = $products[$id2];
        $products[$id2] = $temp;

        // Remove first item
        return $this->saveProductConfig($products);
    }

    /**
     * Save product config
     */
    private function saveProductConfig($products)
    {
        // Reset array keys
        $products = array_values($products);

        $data = [];

        foreach ($products as $product) {
            $data[] = $product->url;
        }

        return file_put_contents(PATH.'/products.txt', implode("\n", $data)."\n");
    }

    /**
     * Display products
     */
    public function displayProducts()
    {
        $products = $this->getProducts();
        
        if (!$products) {
            echo "No products found.\n";
        }

        $total_products = count($products);

        foreach ($products as $product) {
            $store = $this->stores->getStoreByProductUrl($product->url);

            echo $this->yellow;
            echo $this->display->displayProductId($product->id, $total_products)."  ";

            echo $this->cyan;
            echo $this->display->displayStoreName($store->name)."  ";
            echo $this->reset;
            echo $product->url;
            echo "\n";
        }

        return true;
    }

    /**
     * Display product
     *
     * @param integer $id Product ID
     */
    public function displayProduct($id)
    {
        $product = $this->getProduct($id);
        
        echo $this->cyan;
        echo 'URL: ';
        echo $this->reset;
        echo $product->url;
        echo "\n";
    }
}
