<?php
namespace app\calculator;

use app\storeManager\StoreManager;

class Calculator
{
    public $currentLme;
    public $averageLme;
    public $currentMinfin;
    public $averageMinfin;
    //TODO: добавить сюда переменные формул

    public function __construct()
    {
        $fileNames = ['lme', 'lme_average', 'minfin', 'minfin_average'];
        $storeManager = new StoreManager();
        $values = [];

        foreach ($fileNames as $fileName) {
            $values[$fileName] = $storeManager->fetchFromStore($fileName, 1, 'csv')[0]['value'];
        }

        $this->currentLme = (float)$values['lme'];
        $this->averageLme = (float)$values['lme_average'];
        $this->currentMinfin = (float)$values['minfin'];
        $this->averageMinfin = (float)$values['minfin_average'];
    }

    /**
     * Формула "б/н"
     * @return int/float
     */
    public function bnFormula($averageLme, $averageMinfin, $prize)
    {
        return round(($averageLme + $prize) * $averageMinfin * 1.2, 3);
    }

    /**
     * Формула "cash"
     * @return int/float
     */
    public function cashFormula($averageLme, $averageMinfin, $prize)
    {
        return round(($averageLme + $prize) * $averageMinfin * 1.2 * 0.9, 3);
    }

    /**
     * Render an end data
     */
    public function render()
    {

    }
}
