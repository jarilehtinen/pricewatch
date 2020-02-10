<?php

namespace PriceWatch;

use PriceWatch\Stores;
use PriceWatch\Log;

class Parser
{
    private $stores;
    private $log;
    private $data;
    private $red = "\e[0;31m";
    private $reset = "\e[0m";

    /**
     * Construct
     */
    public function __construct()
    {
        $this->stores = new Stores;
        $this->log = new Log;
    }

    /**
     * Get data
     *
     * @param string $url Product URL
     */
    public function getData($url)
    {
        // Get store
        $store = $this->stores->getStoreByProductUrl($url);

        // Gather data
        $data = (object)[];
        $data->url = $url;
        $data->html = $this->getHTML($url);

        // Store
        $data->store = (object)[];
        $data->store = $this->stores->getStoreByProductUrl($url);

        // Product
        if ($data->html) {
            $data->product = (object)[];
            $data->product->title = $this->getTitle($data->html);
            $data->product->price = $this->getPrice($data->store->id, $data->html);
            $data->product->lastPrice = $this->getLastPrice($url);

            $price_increased = $this->priceIncreased($data->product->price, $data->product->lastPrice) ? true : false;
            $data->product->priceIncreased = $price_increased;

            $price_decreased = $this->priceDecreased($data->product->price, $data->product->lastPrice) ? true : false;
            $data->product->priceDecreased = $price_decreased;
        }

        return $data;
    }

    /**
     * Price increased
     *
     * @param  double $price      Price
     * @param  double $last_price Last price
     * @return boolean
     */
    private function priceIncreased($price = false, $last_price = false)
    {
        if (!$price) {
            return false;
        }

        if (!$last_price) {
            return false;
        }

        if ($last_price > $price) {
            return true;
        }

        return false;
    }

    /**
     * Price decreased
     *
     * @param  double $price      Price
     * @param  double $last_price Last price
     * @return boolean
     */
    private function priceDecreased($price = false, $last_price = false)
    {
        if (!$price) {
            return false;
        }

        if (!$last_price) {
            return false;
        }

        if ($last_price < $price) {
            return true;
        }

        return false;
    }

    /**
     * Get HTML
     *
     * @param   string  $url  Product URL
     * @return  string
     */
    private function getHTML($url)
    {
        $data = file_get_contents($url);

        // Sanitize HTML for better regex compatibility
        $data = str_replace("\r", '', $data);
        $data = str_replace("\n", '', $data);
        $data = str_replace("\t", '', $data);
        $data = str_replace(' >', '>', $data);

        return $data;
    }

    /**
     * Get title
     *
     * @param string $html HTML
     * @
     */
    private function getTitle($html)
    {
        // Get title
        $title = preg_match('/<title[^>]*>(.*?)<\/title>/ism', $html, $matches) ? $matches[1] : false;

        if (!$title) {
            return false;
        }

        $title = html_entity_decode($title);

        // Remove useless stuff from title (hoping the product name comes first)
        $title = explode(' – ', $title);
        $title = $title[0];

        $title = explode(' - ', $title);
        $title = $title[0];

        $title = explode(' | ', $title);
        $title = $title[0];

        return trim(html_entity_decode($title));
    }

    /**
     * Clean price
     *
     * @param  string $price Price
     * @return double
     */
    private function cleanPrice($price)
    {
        $price = trim($price);
        $price = strip_tags($price);

        $price = preg_replace('/[^0-9,.]/', '', $price);

        $price = str_replace(',', '.', $price);
        $price = number_format($price, 2, '.', '');

        return $price;
    }

    /**
     * Get price
     *
     * @param  string $store_id Store ID
     * @param  string $html     HTML
     * @return mixed
     */
    private function getPrice($store_id, $html)
    {
        // Get price tag regular expression
        $regexp = $this->stores->getStore($store_id)->priceRegExp[0];

        if (!$regexp) {
            return false;
        }

        // Go through all store price regexes
        foreach ($this->stores->getStore($store_id)->priceRegExp as $regex) {
            $price = preg_match($regex, $html, $matches) ? $matches[1] : false;

            if ($price) {
                return $this->cleanPrice($price);
            }
        }

        return false;
    }

    /**
     * Get last price
     *
     * @param  string $url Product URL
     * @return double
     */
    private function getLastPrice($url)
    {
        $log = $this->log->readLog();

        if (!isset($log[$url])) {
            return false;
        }

        $last_log_entry = end($log[$url]);

        return $last_log_entry['price'];
    }
}
