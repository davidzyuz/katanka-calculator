<?php

namespace app\storeManager;

use app\parser\LmeParser;
use app\parser\MinfinParser;

/**
 * Manage a csv files under store directory
 * Class StoreManager
 * @package app\storeManager
 */
class StoreManager
{
    /**
     * Path to store
     * @var string
     */
    private $_storePath;
    const COOL_DOWN = 86400;

    public function __construct()
    {
        $this->_storePath = dirname(__DIR__) . '/store/';
    }

    /**
     * Set path to store
     * @param $storePath
     */
    public function setStorePath($storePath)
    {
        $this->_storePath = $storePath;
    }

    /**
     * Get path to store
     * @return string
     */
    public function getStorePath(): string
    {
        return $this->_storePath;
    }

    /**
     * Fetch values from formulas csv
     * @param string $name
     * @param string $extension
     * @return array|false
     */
    public function fetchFormulaValues()
    {
        $generatedFilename = $this->generateFilename('formula_values', 'csv');
        $resource = fopen($generatedFilename, 'r+t');
        $values = [];
        for ($i = 0; $i <= 1; $i += 1) {
            $line = fgetcsv($resource, 1000, ',');
            foreach ($line as $item) {
                $i === 0
                    ? $keys[] = $item
                    : $values[] = $item;
            }
        }
        fclose($resource);
        return array_combine($keys, $values);
    }

    //TODO заделать возможность апдейтить данные формул
    public function updateFormulaValues(array $data, string $filename, string $extension)
    {
        $storedData = $this->fetchFormulaValues();
        $generatedFilename = $this->generateFilename($filename, $extension);
        $resource = fopen($generatedFilename, 'wt');
        //TODO: сделать код ниже универсальным
        $storedData['prize'] = $data['prize'];
        $keys = array_keys($storedData);
        $values = array_values($storedData);
        $content = array_reduce($keys, function ($acc, $elem) {
            return $acc .= ($elem . ',');
        }, '');
        $content = rtrim($content, ',');
        $content .= "\n";
        $content .= array_reduce($values, function ($acc, $elem) {
            return $acc .= ($elem . ',');
        }, '');
        $content = rtrim($content, ',');
        $content .= "\n";
        fwrite($resource, $content, strlen($content));
        fclose($resource);
    }

    /**
     * Write data to store such as minfin.csv, lme.csv
     * @param float $data
     * @param string $filename
     * @param string $extension
     */
    public function writeToStore(float $data, string $filename, string $extension)
    {
        $generateFilename = $this->generateFilename($filename, $extension);
        $resource = fopen($generateFilename, 'at');
        $scheduledAt = '07:59:59';
        $date = date('d-m-Y', time() - self::COOL_DOWN);
        $scheduledFullString = $date . ' ' . $scheduledAt;
        $timestamp = strtotime($scheduledFullString);
        $fields = [$timestamp, $data, $date];
        fputcsv($resource, $fields);
        fclose($resource);
    }

    /**
     * Writes fetched data to temporary store
     * @param float $data
     * @param string $filename
     * @param string $extension
     */
    public function writeToTempStore(float $data, string $filename, string $extension)
    {
        $generateFilename = $this->generateFilename($filename, $extension);
        $resource = fopen($generateFilename, 'at');
        $timestamp = time() - self::COOL_DOWN;
        $date = date('d-m-Y', $timestamp);
        $fields = [$timestamp, $data, $date];
        fputcsv($resource, $fields);
        fclose($resource);
    }

    /**
     * Return an needed lines of data.
     * @param string $filename
     * @param int | null $numOfLines
     * @param string $extension
     * @return array
     */
    public function fetchFromStore(string $filename, $numOfLines = null, string $extension = 'csv'): array
    {
        $filename = $this->generateFilename($filename, $extension);
        $resource = fopen($filename, 'rt');
        $data = [];

        while (!feof($resource)) {
            $current = fgetcsv($resource, 1000, ',');

            if (!empty($current)) {
                $data[] = $current;
            }
            continue;
        }
        fclose($resource);

        $numOfLines = $numOfLines ?? count($data);
        return self::reformatData($data, $numOfLines);
    }

    /**
     * Generates a path to specific store
     * @param string $name
     * @param string $extension
     * @return string
     */
    public function generateFilename(string $name, string $extension): string
    {
        $path = $this->getStorePath();
        return $path . $name . '.' . $extension;
    }

    public function writeToTest()
    {
        $filename = $this->generateFilename('test', 'csv');
        $content = file_get_contents($filename);
        $content .= "test \n";
        $resource = fopen($filename, 'w');
        fwrite($resource, $content);
        fclose($resource);
    }

    /**
     * Format data accordingly provided attribute
     * @param array $data
     * @param int $offset
     * @return array
     */
    static function reformatData(array $data, int $offset): array
    {
        $dataLen = count($data);

        if ($dataLen <= 1) {
            return [];
        } elseif ($dataLen <= $offset) {
            $offset = $dataLen - 1;
        }

        $arrKeys = array_slice($data, 0, 1)[0];
        $arrValues = array_slice($data, -($offset));//отрицательное смещение для получения данных c конца
        $newArr = [];

        foreach ($arrValues as $arrValue) {
            if (empty($arrValue)) continue;
            $newArr[] = array_combine($arrKeys, $arrValue);
        }

        return $newArr;
    }
}
