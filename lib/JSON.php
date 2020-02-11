<?php

namespace PriceWatch;

use PriceWatch\Stores;
use PriceWatch\Display;
use PriceWatch\Parser;
use PriceWatch\Products;
use PriceWatch\Log;

class JSON
{
    private $stores;
    private $products;
    private $parser;
    private $log;
    private $data;

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
    }

    /**
     * Output JSON
     */
    public function outputJSON()
    {
        // Send header
        header('Content-Type: application/json');

        // Get stores
        $stores = $this->stores->getStores();

        // Display products
        $products = $this->products->getProducts();
        $total_products = count($products);

        $result = [];

        foreach ($products as $id => $product) {
            // Get data
            $data = $this->parser->getData($id, $product->url);

            $result[] = array(
                'store' => $data->store->name,
                'storeId' => $data->store->id,
                'productId' => $data->product->id,
                'product' => $data->product->title,
                'url' => $data->product->url,
                'price' => (double) number_format($data->product->price, 2, '.', ''),
                'lastPrice' => $data->product->lastPrice
                    ? (double) number_format($data->product->lastPrice, 2, '.', '')
                    : false,
                'priceIncreased' => $data->product->priceIncreased ? 1 : 0,
                'priceDecreased' => $data->product->priceDecreased ? 1 : 0
            );

            $this->log->logPrice($product->url, $data->product->title, $data->product->price);
        }

        if (count($result) > 0) {
            echo json_encode($result);
            return true;
        }

        echo json_encode(['noResults' => 1]);
        return true;
    }
}
