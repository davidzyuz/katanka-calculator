<?php

namespace app\controller;

use app\datepicker\Datepicker;
use app\storeManager\StoreManager;
use app\calculator\Calculator;

class Controller
{
    public function updateAction(array $data)
    {
        $store = new StoreManager();
        $calc = new Calculator();
        $store->updateFormulaValues($data, 'formula_values', 'csv');
        $responseData = $store->fetchFormulaValues();
        $responseData['bn'] = $calc->bnFormula($calc->averageLme, $calc->averageMinfin, $responseData['prize']);
        $responseData['cash'] = $calc->cashFormula($calc->averageLme, $calc->averageMinfin, $responseData['prize']);

        return $responseData;
    }

    public function indexAction()
    {
        $store = new StoreManager();
        $calc = new Calculator();
        $responseData = $store->fetchFormulaValues();
        $responseData['bn'] = $calc->bnFormula($calc->averageLme, $calc->averageMinfin, $responseData['prize']);
        $responseData['cash'] = $calc->cashFormula($calc->averageLme, $calc->averageMinfin, $responseData['prize']);
        $responseData['lmeAverage'] = $calc->averageLme;
        $responseData['minfinAverage'] = $calc->averageMinfin;
        $responseData['currentLme'] = $calc->currentLme;
        $responseData['currentMinfin'] = $calc->currentMinfin;
        $responseData['storedLmeDate'] = $calc->storedLmeDate;
        $responseData['storedMinfinDate'] = $calc->storedMinfinDate;

        return $responseData;
    }

    /**
     * Calculated cash and bn formula values
     * @return array;
     */
    public function fetchChartDataAction()
    {
        $store = new StoreManager();

        $lmeAverData = $store->fetchFromStore('lme_average', 20, 'csv');
        $minfAverData = $store->fetchFromStore('minfin_average', 20, 'csv');
        $formulaValues = $store->fetchFormulaValues();

        // Indexed array of average lme data
        $pureAverLme = array_map(function ($el) {
            return $el['value'];
        }, $lmeAverData);

        // Indexed array of average minfin data
        $pureAverMinfin = array_map(function ($el) {
            return $el['value'];
        }, $minfAverData);

        // Indexed array of stored time date
        $pureDate = array_map(function ($el) {
            return $el['stored_at'];
        }, $lmeAverData);

        $responseData = [];
        for ($i = 0, $len = count($lmeAverData); $i < $len; $i += 1) {
            $price = round(($pureAverLme[$i] + $formulaValues['prize']) * $pureAverMinfin[$i] * $formulaValues['second_var']);
            $cashless = round($price * $formulaValues['first_var']);

            $responseData[] = [
                'date' => $pureDate[$i],
                'price' => $price,
                'cashless' => $cashless
            ];
        }

        return $responseData;
    }

    /**
     * Pick a specific date from store
     * @return array
     */
    public function datepickerAction():array
    {
        $date = $_POST['date'];
        $datepicker = new Datepicker($date);
        return $datepicker->getAllSpecificValues();
    }
}
