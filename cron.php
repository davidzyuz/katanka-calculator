<?php

require_once __DIR__ . '/vendor/autoload.php';

use app\storeManager\StoreManager;

$store = new StoreManager();
$store->writeToMainStore();
