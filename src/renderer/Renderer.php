<?php
namespace app\renderer;

use app\storeManager\StoreManager;

class Renderer
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
     * Формула "Лом"
     * @return int|float
     */
    public function scrapFormula($averageLme, $averageMinfin)
    {
        return round(($averageLme + 250) * $averageMinfin * 1.2 * 0.9 - 22000, 3);
    }

    /**
     * Формула "б/н"
     * @return int/float
     */
    public function bnFormula($averageLme, $averageMinfin)
    {
        return round(($averageLme + 250) * $averageMinfin * 1.2, 3);
    }

    /**
     * Формула "cash"
     * @return int/float
     */
    public function cashFormula($averageLme, $averageMinfin)
    {
        return round(($averageLme + 250) * $averageMinfin * 1.2 * 0.9, 3);
    }

    /**
     * Render an end data
     */
    public function render()
    {

    }
}
