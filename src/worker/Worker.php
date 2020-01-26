<?php

namespace app\worker;

use app\storeManager\StoreManager;
use app\parser\LmeParser;
use app\parser\MinfinParser;
use app\renderer\Renderer;

class Worker
{
    const LME_TYPE = 0;
    const MINFIN_TYPE = 1;
    const COOL_DOWN = 86400;

    public $lmeData;
    public $minfinData;
    public $lmeAvarage;
    public $minfinAvarage;
    public $bnFormulaValue;
    public $scrapFormulaValue;
    public $cashFormulaValue;

    /**
     * Fetch data. General abstraction to fetching data.
     */
    public function fetchData()
    {
        $lmeParser = new LmeParser();
        $minfinParser = new MinfinParser();
        $this->lmeData = round((float)$lmeParser->makeIt(), 3);
        $this->minfinData = round((float)$minfinParser->makeIt(), 3);
    }

    /**
     * Write fetched data to main store. This is LME and
     * Minfin data.
     * @param $data
     * @param string $filename
     * @param int $numOfLines
     * @param string $extension
     * @return bool
     */
    public function writeToStore($data, string $filename, $numOfLines = null, string $extension = 'csv'): bool
    {
        $storeManager = new StoreManager();
        $storedData = $storeManager->fetchFromStore($filename, $numOfLines, $extension);

        foreach ($storedData as $value) {
            $currentDate = date('d:m:Y');
            $storedDate = date('d:m:Y', $value['timestamp']);
            $storedValue = (float)$value['value'];
            if ($currentDate === $storedDate || $data === $storedValue) {
                return false;
            }
        }
        $storeManager->writeToStore($data, $filename, $extension);
        return true;
    }

    /**
     * Rebase values from temp store to stable
     */
    public function rebaseToMainStore($from, $to)
    {
        $storeManager = new StoreManager();
        $dataFromTemp = $storeManager->fetchFromStore($from, 1, 'csv');
        $dataFromGeneral = $storeManager->fetchFromStore($to, 1, 'csv');
        $tempTimestamp = $tempValue = $tempStored_at = '';
        $result = false;

        foreach ($dataFromTemp as $value) {
            $tempTimestamp = $value['timestamp'] - self::COOL_DOWN;
            $tempValue = (float)$value['value'];
            $tempStored_at = $value['stored_at'];
        }

        foreach ($dataFromGeneral as $value) {
            if ((float)$value['value'] !== $tempValue && $tempTimestamp > $value['timestamp']) {
                $result = true;
            }
        }

        if ($result) {
            $storeManager->writeToStore($tempValue, $to, 'csv');
        }

        return $result;
    }

    /**
     * Calculate average LME and Minfing data. Set values by type
     */
    public function calculateAverage($type)
    {
        switch ($type) {
            case self::LME_TYPE:
                $filename = 'lme';
                $numOfLines = 10;
                $extension = 'csv';
                break;

            case self::MINFIN_TYPE:
                $filename = 'minfin';
                $numOfLines = 5;
                $extension = 'csv';
                break;

            default:
                return false;
        }

        $storeManager = new StoreManager();
        $data = $storeManager->fetchFromStore($filename, $numOfLines, $extension);
        $sum = array_reduce($data, function ($init, $current) {
            return $init + $current['value'];
        }, 0);

        $avarage = round($sum / $numOfLines, 3);

        switch ($type) {
            case self::LME_TYPE:
                $this->lmeAvarage = $avarage;
                break;

            case self::MINFIN_TYPE:
                $this->minfinAvarage = $avarage;
                break;

            default:
                return false;
        }

        return true;
    }

    /**
     * Write average LME and Minfin data
     */
    public function writeAverage($type)
    {
        switch ($type) {
            case self::LME_TYPE:
                $filename = 'lme_average';
                $numOfLines = 10;
                $this->writeToStore($this->lmeAvarage, $filename, $numOfLines);
                break;

            case self::MINFIN_TYPE:
                $filename = 'minfin_average';
                $numOfLines = 5;
                $this->writeToStore($this->minfinAvarage, $filename, $numOfLines);
                break;

            default:
                return false;
        }
    }

    public function writeToTemp(float $data, string $filename)
    {
        $storeManager = new StoreManager();
        $storeManager->writeToTempStore($data, $filename, 'csv');
    }

    /**
     * Write formula values
     */
    public function writeFormulaValues()
    {
        
    }

    public function writeTest()
    {
        $storeManager = new StoreManager();
        $storeManager->writeToTest();
    }
}