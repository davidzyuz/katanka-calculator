<?php

/**
 * A such a rough script to date fast-forward.
 * Fast-forwards a date to + 1 day. Use it with
 * lme_average and minfin_average.
 */

require_once 'vendor/autoload.php';

use app\storeManager\StoreManager;

if (isset($argv[1])) {
    $filename = $argv[1];
} else {
    echo "Invalid argument specified \n";
    exit(1);
}

$storeManager = new StoreManager();

try {
    $averData = $storeManager->fetchFromStore($filename);
} catch (\Exception $err) {
    echo "{$err->getMessage()} \n";
    exit(1);
}

$firstDate = $averData[0]['stored_at'];
$dateTime = new DateTime($firstDate);

function addDates(array $inputArr, array &$acc, $curr, DateTime $dateObj)
{
    $dateObj->modify('+1 day');
    $filtered = array_filter($inputArr, function($el) use ($dateObj) {
        return in_array($dateObj->format('d-m-Y'), $el);
    });

    if (!empty($filtered)) {
        return;
    }

    $acc[] = [
        'timestamp' => $curr['timestamp'],
        'value' => $curr['value'],
        'stored_at' => $dateObj->format('d-m-Y')
    ];

    addDates($inputArr, $acc, $curr, $dateObj);
}

$fastForwardedAverDate = [];
foreach ($averData as $ind => $value) {
    $increasedTime = (new DateTime($value['stored_at']))->modify('+1 day');
    $increasedTimeString = $increasedTime->format('d-m-Y');
    $fastForwardedAverDate[] = [
        'timestamp' => $value['timestamp'],
        'value' => $value['value'],
        'stored_at' => $increasedTimeString
    ];
}

$fullFastForwarded = [];
foreach ($fastForwardedAverDate as $ind => $value) {
    $increasedTime = new DateTime($value['stored_at']);
    $increasedTimeString = $increasedTime->format('d-m-Y');
    $fullFastForwarded[] = [
        'timestamp' => $value['timestamp'],
        'value' => $value['value'],
        'stored_at' => $increasedTimeString
    ];

    $isLast = !array_key_exists($ind + 2, $averData);
    if (!$isLast) {
        addDates($fastForwardedAverDate, $fullFastForwarded, $value, $increasedTime);
    }
}

$resource = fopen('./src/store/' . $filename . '.csv', 'wt');
fputcsv($resource, ['timestamp', 'value', 'stored_at']);
array_map(function($el) use ($resource) {
    $arr = [$el['timestamp'], $el['value'], $el['stored_at']];
    fputcsv($resource, $arr);
}, $fullFastForwarded);
fclose($resource);
