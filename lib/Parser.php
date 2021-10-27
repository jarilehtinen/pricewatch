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
     * @param integer $id  Product ID
     * @param string  $url Product URL
     */
    public function getData($id, $url)
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

        if (!$data->store) {
            return false;
        }

        // Product
        if ($data->html) {
            $data->product = (object)[];
            $data->product->url = $url;
            $data->product->id = $id;
            $data->product->title = $this->getTitle($data->html);
            $data->product->price = $this->getPrice($data->store->id, $data->html);
            $data->product->lastPrice = $this->getLastPrice($url);
            $data->product->availability = $this->getAvailability($data->store->id, $data->html);

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

        if ($last_price < $price) {
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

        if ($last_price > $price) {
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
        // Get data from URL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);

        if (!$data) {
            return false;
        }

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
     * Get availability
     */
    private function getAvailability($store_id, $html)
    {
        // Get availability tag regular expression
        $regex = isset($this->stores->getStore($store_id)->availabilityRegExp)
            ? $this->stores->getStore($store_id)->availabilityRegExp
            : false;

        if (!$regex) {
            return false;
        }

        // Get availability match
        $availability = preg_match($regex[0], $html, $matches);

        if (isset($matches[1])) {
            $availability = $matches[1];
        } elseif (isset($matches[0])) {
            $availability = $matches[0];
        }

        // Specific values set for availability
        if (isset($regex[1])) {
            // Boolean value
            if (is_bool($regex[1])) {
                // First value set to true
                if ($regex[1]) {
                    return $availability ? 'yes' : 'no';
                }

                // First value set to false
                if (!$regex[1]) {
                    return $availability ? 'no' : 'yes';
                }

                return false;
            }

            // Specific string
            if (isset($regex[2])) {
                $available = $regex[1];
                $not_available = $regex[2];

                if ($availability == $available) {
                    return 'yes';
                }

                if ($availability == $not_available) {
                    return 'no';
                }
            }
        }

        if ($availability) {
            return 'yes';
        }

        return false;
    }
}
