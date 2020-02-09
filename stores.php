<?php

$data = array(
    array(
        'id' => 'www.verkkokauppa.com',
        'name' => 'Verkkokauppa.com',
        'priceRegExp' => '/<meta data-rh="true" property="product:price:amount" content="(.*?)"\/>/is'
    ),
    array(
        'id' => 'www.gigantti.fi',
        'name' => 'Gigantti',
        'priceRegExp' => '/<div class="product-price-container"><span>(.*?)<\/span><\/div>/is'
    ),
    array(
        'id' => 'www.power.fi',
        'name' => 'Power',
        'priceRegExp' => '/<meta property="product:price:amount" content="(.*?)">/is'
    ),
    array(
        'id' => 'www.jimms.fi',
        'name' => 'Jimm\'s',
        'priceRegExp' => '/<meta property="product:price:amount" content="(.*?)">/is'
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
);
