<?php

$data = array(
    array(
        'id' => 'www.verkkokauppa.com',
        'name' => 'Verkkokauppa.com',
        'priceRegExp' => array(
            '/<meta data-rh="true" property="product:price:amount" content="([0-9.]+?)"\/>/is',
            '/<meta data-rh="true" content="([0-9.]+?)" property="product:price:amount"\/>/is'
        ),
        'availabilityRegExp' => array(
            '/<meta data-rh="true" content="([a-z ]+?)" property="product:availability"\/>/is',
            'in stock',
            'out of stock'
        )
    ),
    array(
        'id' => 'www.gigantti.fi',
        'name' => 'Gigantti',
        'priceRegExp' => array(
            '/<div class="product-price-container"><span>(.*?)<\/span><\/div>/is',
        ),
        'availabilityRegExp' => array(
            '/Tuote ei saatavilla/is',
            false,
            true
        )
    ),
    array(
        'id' => 'www.jimms.fi',
        'name' => 'Jimm\'s',
        'priceRegExp' => '/<meta property="product:price:amount" content="([0-9.,]+?)">/is'
    ),
    array(
        'id' => 'www.multitronic.fi',
        'name' => 'Multitronic',
        'priceRegExp' => '/<span id="vat" style="">(.*?)<\/span><\/span>/is'
    ),
    array(
        'id' => 'www.proshop.fi',
        'name' => 'Proshop',
        'priceRegExp' => '/<span class="site-currency-attention">(.*?)<\/span>/is'
    ),
    array(
        'id' => 'www.vpd.fi',
        'name' => 'VPD',
        'priceRegExp' => '/<span class="price">(.*?)<\/span>/is',
        'availabilityRegExp' => array(
            '/<span class="stock-status">(.*?) kpl<\/span>/is',
            true,
            false
        )
    ),
    array(
        'id' => 'www.discshop.fi',
        'name' => 'Discshop',
        'priceRegExp' => '/<span itemprop="price">(.*?)<\/span>/is'
    ),
    array(
        'id' => 'www.puolenkuunpelit.com',
        'name' => 'Puolenkuun Pelit',
        'priceRegExp' => array(
            '/<form name="cart_quantity".*?>.*?<b class="commonPriceSpecial"><s>.*?<\/s> &nbsp;&nbsp;(.*?)<\/b>/is',
            '/<form name="cart_quantity".*?>.*?<b class="commonPrice">(.*?)<\/b>/is'
        ),
        'availabilityRegExp' => array(
            '/Tuotepainos loppu/is',
            false,
            true
        )
    ),
    array(
        'id' => 'www.konsolinet.fi',
        'name' => 'Konsolinet',
        'priceRegExp' => '/<dd class="Price">(.*?)<\/dd>/is',
        'availabilityRegExp' => array(
            '/Loppunut/is',
            false,
            true
        )
    ),
    array(
        'id' => 'www.pelaajashop.fi',
        'name' => 'Pelaaja Shop',
        'priceRegExp' => '/<dd class="Price">(.*?)<\/dd>/is'
    ),
    array(
        'id' => 'www.maxgaming.fi',
        'name' => 'Max Gaming',
        'priceRegExp' => '/<div class="productInfo">.*?<div class="price">(.*?)<\/div>/is'
    ),
    array(
        'id' => 'www.hifistudio.fi',
        'name' => 'HifiStudio',
        'priceRegExp' => '/<meta property="product:price:amount" content="([0-9.,]+?)" \/>/is'
    ),
    array(
        'id' => 'www.audiokauppa.fi',
        'name' => 'Audiokauppa.fi',
        'priceRegExp' => '/<span itemprop=\'price\' content=\'.*\'>([0-9.,]+?)<\/span>/is'
    ),
    array(
        'id' => 'www.hifihuone.fi',
        'name' => 'Hifihuone',
        'priceRegExp' => '/<span class="woocommerce-Price-amount amount">([0-9.,]+?)<span/is'
    ),
    array(
        'id' => 'www.finnishdesignshop.fi',
        'name' => 'Finnish Design Shop',
        'priceRegExp' => '/<meta itemprop="price" content="([0-9.]+?)">/is'
    ),
);
