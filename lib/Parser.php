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
        // Get stores
        $this->stores->getStores();

        // Get store ID
        $store_id = $this->stores->getStoreIdFromURL($url);

        // Get HTML
        $html = $this->getHTML($url);

        if (!$html) {
            return false;
        }

        // Gather data
        $data = (object)[];
        $data->html = $html;
        $data->name = $this->stores->getStore($store_id)->name;
        $data->title = $this->getTitle($html);
        $data->price = $this->getPrice($store_id, $html);
        $data->lastPrice = $this->getLastPrice($url);

        $price_increased = $data->price && $data->lastPrice && $data->lastPrice > $data->price ? true : false;
        $data->priceIncreased = $price_increased;

        $price_decreased = $data->price && $data->lastPrice && $data->lastPrice < $data->price ? true : false;
        $data->priceDecreased = $price_decreased;

        return $data;
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

    /**
     * Longest store name length
     *
     * @return integer
     */
    public function longestStoreNameLength()
    {
        $longest_name_length = 0;

        // Get stores
        $stores = $this->stores->getStores();

        foreach ($stores as $store) {
            // Calculate store name length
            $name_length = strlen($store->name);

            if ($name_length > $longest_name_length) {
                $longest_name_length = $name_length;
            }
        }

        return $longest_name_length;
    }
}
