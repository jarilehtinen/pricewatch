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
);
