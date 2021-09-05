<?php

require_once __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

use app\controller\Controller;

$controller = new Controller();

$action = $_POST['action'] ?? 'init';
$data = [];

echo match ($action) {
    'update' => trim(json_encode($controller->updateAction())),
    'chart_data' => trim(json_encode($controller->fetchChartDataAction())),
    'datepicker' => trim(json_encode($controller->datepickerAction())),
    'test' => trim(json_encode('hello from test')),
    'init' => trim(json_encode($controller->indexAction())),
    default => throw new Exception('Invalid action'),
};
