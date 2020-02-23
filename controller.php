<?php

require_once __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

use app\calculator\Calculator;
use app\controller\Controller;

$calculator = new Calculator();
$controller = new Controller();

$action = $_POST['action'] ?? 'init';
$data = [];

if ($action === 'update') {
    $data['prize'] = $_POST['prize'];
    echo trim(json_encode($controller->update($_POST), 0, 512));
} else {
    echo trim(json_encode($controller->init(), 0, 512));
}
