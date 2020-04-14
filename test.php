<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

use app\datepicker\Datepicker;

$datepicker = new Datepicker('');
echo '<pre>';
print_r($datepicker->specificLme);
print_r($datepicker->specificLmeAverage);
print_r($datepicker->specificMinfin);
print_r($datepicker->specificMinfinAverage);
echo '</pre>';
