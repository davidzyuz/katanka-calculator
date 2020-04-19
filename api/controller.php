<?php

require_once __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

use app\controller\Controller;

$controller = new Controller();

$action = $_POST['action'] ?? 'init';
$data = [];

switch($action) {
    case 'update':
        echo trim(json_encode($controller->updateAction($_POST), 0, 512));
        break;

    case 'fetch_chart_data':
        echo trim(json_encode($controller->fetchChartDataAction(), 0, 512));
        break;

    case 'datepicker':
        echo trim(json_encode($controller->datepickerAction(), 0, 512));
        break;

    case 'test':
        echo trim(json_encode('hello from test'));
        break;

    case 'init':
        echo trim(json_encode($controller->indexAction(), 0, 512));
        break;

    default:
        throw new Exception('Invalid action');
        break;
}
