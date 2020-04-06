<?php

require_once __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

use app\calculator\Calculator;
use app\controller\Controller;
use app\datepicker\Datepicker;

$calculator = new Calculator();
$controller = new Controller();

$action = $_POST['action'] ?? 'init';
$data = [];

switch($action) {
    case 'update':
        $data['prize'] = $_POST['prize'];
        echo trim(json_encode($controller->update($_POST), 0, 512));
        break;

    case 'fetch_chart_data':
        echo trim(json_encode($controller->fetchChartData(), 0, 512));
        break;

    case 'datepicker':
        $datepicker = new Datepicker('29-01-2020');
        echo trim(json_encode($datepicker->specificLme, 0, 512));
        break;

    default:
        echo trim(json_encode($controller->init(), 0, 512));
        break;
}
