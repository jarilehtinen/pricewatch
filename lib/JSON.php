<?php

namespace PriceWatch;

use PriceWatch\Stores;
use PriceWatch\Parser;
use PriceWatch\Products;
use PriceWatch\Log;

class JSON
{
    private $stores;
    private $products;
    private $parser;
    private $log;

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
        // Get stores
        $stores = $this->stores->getStores();

        // Display products
        $products = $this->products->getProducts();

        $result = [];

        foreach ($products as $id => $product) {
            // Get data
            $data = $this->parser->getData($id, $product->url);

            $result[] = [
                'store' => [
                    'name' => $data->store->name,
                    'id' => $data->store->id
                ],
                'product' => [
                    'id' => $data->product->id,
                    'title' => $data->product->title,
                    'url' => $data->product->url,
                    'price' => (double) number_format($data->product->price, 2, '.', ''),
                    'lastPrice' => $data->product->lastPrice
                        ? (double) number_format($data->product->lastPrice, 2, '.', '')
                        : false,
                    'priceIncreased' => $data->product->priceIncreased ? true : false,
                    'priceDecreased' => $data->product->priceDecreased ? true : false
                ]
            ];

            $this->log->logPrice($product->url, $data->product->title, $data->product->price);
        }

        // Send header
        header('Content-Type: application/json');

        if (count($result) > 0) {
            echo json_encode($result);
            return true;
        }

        echo json_encode(['noResults' => 1]);
        return true;
    }
}
