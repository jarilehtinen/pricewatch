<?php

namespace PriceWatch;

class Stores
{
    private $stores;
    private $longest_name_length = 0;
    private $cyan = "\e[0;36m";
    private $reset = "\e[0m";

    /**
     * Get store ID from URL
     *
     * @param  string $url Product URL
     * @return string
     */
    public function getStoreIdFromUrl($url)
    {
        $id = str_replace(array('https://', 'http://'), '', $url);
        $id = explode('/', $id);
        return $id[0];
    }

    /**
     * Read stores
     *
     * @return array
     */
    private function readStores()
    {
        if (!file_exists(PATH.'/stores.json')) {
            echo "No stores.json found. Did you run 'pricewatch build'?\n";
            exit;
        }

        $stores = file_get_contents(PATH.'/stores.json');
        $stores = json_decode($stores, true);
        return $stores;
    }

    /**
     * Get stores
     *
     * @return object
     */
    public function getStores()
    {
        if ($this->stores) {
            return $this->stores;
        }

        // Read stores
        $stores = $this->readStores();

        foreach ($stores as $store) {
            $id = $store['id'];

            // Gather data
            if (!is_array($store['priceRegExp'])) {
                $store['priceRegExp'] = array($store['priceRegExp']);
            }

            $this->stores[$id] = (object)[];
            $this->stores[$id]->id = $store['id'];
            $this->stores[$id]->name = $store['name'];
            $this->stores[$id]->priceRegExp = $store['priceRegExp'];
        }

        return $this->stores;
    }

    /**
     * Get store
     *
     * @param  string $store_id Store ID
     * @return object
     */
    public function getStore($id)
    {
        if (!$this->stores) {
            $this->getStores();
        }

        if (!isset($this->stores[$id])) {
            return false;
        }

        return $this->stores[$id];
    }

    /**
     * Get store by product URL
     *
     * @param  string $url Product URL
     * @return array
     */
    public function getStoreByProductUrl($url)
    {
        // Get store ID
        $store_id = $this->getStoreIdFromUrl($url);

        return $this->getStore($store_id);
    }

    /**
     * Build stores
     *
     * @return boolean
     */
    public function buildStores()
    {
        require_once(PATH.'/stores.php');
        return file_put_contents(PATH.'/stores.json', json_encode($data));
    }
}
