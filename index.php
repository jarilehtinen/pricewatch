<?php

define('PATH', __DIR__);

require_once('lib/PriceWatch.php');
require_once('lib/Stores.php');
require_once('lib/Products.php');
require_once('lib/Parser.php');
require_once('lib/Display.php');
require_once('lib/Log.php');
require_once('lib/JSON.php');

$priceWatch = new PriceWatch\PriceWatch;

// Run command
if (isset($argv[1])) {
    $priceWatch->runCommand($argv);
    exit;
}

// Output prices
$priceWatch->outputPrices();
