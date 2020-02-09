<?php

namespace PriceWatch;

class Tools
{
    /**
     * Price pad
     */
    public function pricePad($price)
    {
        $length = 8;
        $price = number_format($price, 2, ',', '');

        if (strlen($price) < $length) {
            return str_pad($price, $length, ' ', STR_PAD_LEFT);
        }

        return $price;
    }
}
