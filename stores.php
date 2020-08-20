<?php

$data = array(
    array(
        'id' => 'www.verkkokauppa.com',
        'name' => 'Verkkokauppa.com',
        'priceRegExp' => array(
            '/<meta data-rh="true" property="product:price:amount" content="([0-9.]+?)"\/>/is',
            '/<meta data-rh="true" content="([0-9.]+?)" property="product:price:amount"\/>/is'
        )
    ),
    array(
        'id' => 'www.gigantti.fi',
        'name' => 'Gigantti',
        'priceRegExp' => array(
            '/<div class="product-price-container"><span>(.*?)<\/span><\/div>/is',
            '/<meta itemprop="price" content="([0-9.,]+?)">/'
        )
    ),
    array(
        'id' => 'www.power.fi',
        'name' => 'Power',
        'priceRegExp' => array(
            '/<meta content="([0-9.,]+?)" property="product:price:amount" \/>/is',
            '/<meta property="product:price:amount" content="([0-9.,]+?)" \/>/is'
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
        'priceRegExp' => '/<span class="price">(.*?)<\/span>/is'
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
        )
    ),
    array(
        'id' => 'www.konsolinet.fi',
        'name' => 'Konsolinet',
        'priceRegExp' => '/<dd class="Price">(.*?)<\/dd>/is'
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
);
