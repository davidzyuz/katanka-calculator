<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

use app\worker\Worker;
use app\storeManager\StoreManager;

/*
 * Fetch remote data, and save it into store. Then calculate data according to formula data saved in store. Works
 * scheduled by cron job*/

$worker = new Worker();

//$worker->fetchData();
//$worker->writeToTemp($worker->lmeData, 'lme_temp');//lme
//$worker->writeToTemp($worker->minfinData, 'minfin_temp');//minfin
//$isStoredLme = $worker->rebaseToMainStore('lme_temp', 'lme');
//$isStoredMinfin = $worker->rebaseToMainStore('minfin_temp', 'minfin');

// Если хотябы одно из значений изменилось - пересчитать if $result1 === true
if ($isStoredLme) {
    $worker->calculateAverage(Worker::LME_TYPE);
    $worker->writeAverage(Worker::LME_TYPE);
} else {
    // Перезапись средних значений.
    $storeManager = new StoreManager();
    $lastAverLme = $storeManager->fetchFromStore('lme_average', 1);
    print_r($lastAverLme);
}

if ($isStoredMinfin) {
    $worker->calculateAverage(Worker::MINFIN_TYPE);
    $worker->writeAverage(Worker::MINFIN_TYPE);
}
