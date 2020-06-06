<?php

namespace app\worker;

use app\storeManager\StoreManager;
use app\parser\LmeParser;
use app\parser\MinfinParser;

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

    /** @var StoreManager */
    protected $_storeManager;

    /**
     * Fetch data. General abstraction to fetching data.
     */
    public function fetchData()
    {
        $lmeParser = new LmeParser();
        $minfinParser = new MinfinParser();
        $this->lmeData = round((float)$lmeParser->makeIt(), 3);
        $this->minfinData = round((float)$minfinParser->makeIt(), 3);
        $this->_storeManager = new StoreManager();
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
        $storedData = $this->_storeManager->fetchFromStore($filename, $numOfLines, $extension);

        foreach ($storedData as $value) {
            $currentDate = date('d:m:Y');
            $storedDate = date('d:m:Y', $value['timestamp']);
            $storedValue = (float)$value['value'];
            if ($currentDate === $storedDate || $data === $storedValue) {
                return false;
            }
        }
        $this->_storeManager->writeToStore($data, $filename, $extension);
        return true;
    }

    /**
     * Rebase values from temp store to stable
     */
    public function rebaseToMainStore($from, $to)
    {
        $dataFromTemp = $this->_storeManager->fetchFromStore($from, 1, 'csv');
        $dataFromGeneral = $this->_storeManager->fetchFromStore($to, 1, 'csv');
        $tempTimestamp = $tempValue = $tempStored_at = '';
        $result = false;

        foreach ($dataFromTemp as $currTemp) {
            $tempTimestamp = $currTemp['timestamp'] - self::COOL_DOWN;
            $tempValue = (float)$currTemp['value'];
        }

        foreach ($dataFromGeneral as $currGen) {
            if (
                ((float)$currGen['value'] !== $tempValue) &&
                ($tempTimestamp > $currGen['timestamp']) &&
                ((int)$tempValue !== 0)
            ) {
                $result = true;
            }
        }

        if ($result) {
            $this->_storeManager->writeToStore($tempValue, $to, 'csv');
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

        $data = $this->_storeManager->fetchFromStore($filename, $numOfLines, $extension);
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
        $this->_storeManager->writeToTempStore($data, $filename, 'csv');
    }

    /**
     * Write formula values
     */
    public function writeFormulaValues()
    {
        
    }

    /**
     * Rewrite average data
     *
     * @param $type
     */
    public function rewriteAverage($type)
    {
        switch ($type) {
            case self::LME_TYPE:
                $lastLine = $this->_storeManager->fetchFromStore('lme_average', 1);
        }
    }
}