<?php

namespace PriceWatch;

class Stores
{
    private $stores;
    private $longest_name_length = 0;

    /**
     * Get store ID
     *
     * @param  string $url Product URL
     * @return string
     */
    public function getStoreIdFromURL($url)
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
            $this->stores[$id]->priceRegExp = $store['priceRegExp'];
            $this->stores[$id]->name = $store['name'];
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
        if (!isset($this->stores[$id])) {
            return false;
        }

        return $this->stores[$id];
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
