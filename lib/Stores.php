<?php

namespace PriceWatch;

class Stores
{
    private $stores;
    private $longest_name_length = 0;

    /**
     * Get store ID
     */
    public function getStoreIdFromURL($url)
    {
        $id = str_replace('https://', '', $url);
        $id = explode('/', $id);
        $id = $id[0];
        return $id;
    }

    /**
     * Read stores
     */
    private function readStores()
    {
        if (!file_exists('stores.json')) {
            echo "No stores.json found. Did you run 'pricewatch build'?\n";
            exit;
        }

        $stores = file_get_contents('stores.json');
        $stores = json_decode($stores, true);
        return $stores;
    }

    /**
     * Get stores
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
            $this->stores[$id] = (object)[];
            $this->stores[$id]->priceRegExp = $store['priceRegExp'];
            $this->stores[$id]->name = $store['name'];
        }

        return $this->stores;
    }

    /**
     * Get store
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
     */
    public function buildStores()
    {
        require_once('stores.php');
        return file_put_contents('stores.json', json_encode($data));
    }
}
