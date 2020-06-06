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
    const COOL_DOWN = 86400;

    /**
     * Path to store.
     *
     * @var string
     */
    private $_storePath;

    /**
     * Last date for chart.
     * @var string
     */
    private $_criteriaDate;

    public function __construct($date = null)
    {
        $this->_storePath = dirname(__DIR__) . '/store/';

        $this->_criteriaDate = $date;
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
        $resource = fopen($generatedFilename, 'rt');
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

    public function updateFormulaValues(string $filename, string $extension)
    {
        $storedData = $this->fetchFormulaValues();
        $generatedFilename = $this->generateFilename($filename, $extension);
        $resource = fopen($generatedFilename, 'wt');
        $storedData['prize'] = $_POST['prize'];
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

        return true;
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
     * @throws \Exception
     */
    public function fetchFromStore(string $filename, $numOfLines = null, string $extension = 'csv'): array
    {
        $filename = $this->generateFilename($filename, $extension);
        $resource = fopen($filename, 'rt');
        if (!$resource) {
            throw new \Exception('Invalid filename: ' . $filename);
        }
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
        return $this->reformatData($data, $numOfLines);
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
     * Helper method for get unique values from multidimensional array.
     *
     * @param $array
     * @param $key
     * @return array
     */
    private function _uniqueMultidimArray($array, $key) {
        $tempArray = [];
        $keyArray = [];
        $repeatedLastKey = [];

        foreach($array as $val) {
            if (!in_array($val[$key], $keyArray)) {
                $keyArray[] = $val[$key];
                $tempArray[] = $val;

                // TODO: не записывает последнюю дату в массив. Работает криво
                if (!empty($repeatedLastKey) && !empty($tempArray)) {
                    $last = key(array_slice($tempArray, -1, 1, true));
                    $tempArray[$last] = $repeatedLastKey;
                    $repeatedLastKey = [];
                }
            } else {
                $repeatedLastKey = $val;
            }
        }
        return $tempArray;
    }

    /**
     * Format data accordingly provided attribute
     * @param array $data
     * @param int $offset
     * @return array
     */
    public function reformatData(array $data, int $offset): array
    {
        $dataLen = count($data);

        if ($dataLen <= 1) {
            return [];
        } elseif ($dataLen <= $offset) {
            $offset = $dataLen - 1;
        }

        $arrKeys = array_slice($data, 0, 1)[0];

        // отсекаем элемент массива с ключами
        $filteredData = array_slice($data, 1);

        // Если есть дата, то обрезаем массив до этой даты включительно.
        if (isset($this->_criteriaDate)) {
            foreach ($filteredData as $key => $value) {
                if (in_array($this->_criteriaDate, $value)) {
                    $ind = $key;
                } else {
                    continue;
                }
            }
        }

        if (isset ($ind)) {
            $filteredData = array_filter($filteredData, function($key) use ($ind) {
                return $key <= $ind;
            }, ARRAY_FILTER_USE_KEY);
        }
        // Конец логики выше. TODO: вынести в метод.

        // Фильтруем повторяющиеся средние значения. Массив вида [0 => [0 => timestamp, 1 => value, 2 => date]];
        $filteredData = $this->_uniqueMultidimArray($filteredData, 1);
        var_dump($filteredData);
        die();

        //отрицательное смещение для получения данных c конца
        $arrValues = array_slice($filteredData, -($offset));
        $newArr = [];

        foreach ($arrValues as $arrValue) {
            if (empty($arrValue)) continue;
            $newArr[] = array_combine($arrKeys, $arrValue);
        }

        return $newArr;
    }
}
