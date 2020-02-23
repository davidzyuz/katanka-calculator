<?php

namespace app\controller;

use app\storeManager\StoreManager;
use app\calculator\Calculator;

class Controller
{
    public function update(array $data)
    {
        $store = new StoreManager();
        $calc = new Calculator();
        $store->updateFormulaValues($data, 'formula_values', 'csv');
        $responseData = $store->fetchFormulaValues();
        $responseData['bn'] = $calc->bnFormula($calc->averageLme, $calc->averageMinfin, $responseData['prize']);
        $responseData['cash'] = $calc->cashFormula($calc->averageLme, $calc->averageMinfin, $responseData['prize']);

        return $responseData;
    }

    public function init()
    {
        $store = new StoreManager();
        $calc = new Calculator();
        $responseData = $store->fetchFormulaValues();
        $responseData['bn'] = $calc->bnFormula($calc->averageLme, $calc->averageMinfin, $responseData['prize']);
        $responseData['cash'] = $calc->cashFormula($calc->averageLme, $calc->averageMinfin, $responseData['prize']);

        return $responseData;
    }
}
