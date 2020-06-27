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
    const STATUS_STORED = 1;
    const STATUS_FAILED = 0;

    /**
     * Constants for inner updateFormulaValues checks.
     */
    const PRIZE = 1;
    const FIRST_VAR = 2;
    const SECOND_VAR = 3;

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
     * Updates formula_values.csv.
     *
     * @param string $filename
     * @param string $extension
     * @return bool
     */
    public function updateFormulaValues(string $filename, string $extension)
    {
        if (!isset($_POST['value_to_change'])) {
            throw new \Exception('The param "value_to_change" should be provided in POST body');
        }

        // Fetch formula values and update necessary field.
        $storedData = $this->fetchFormulaValues();

        switch ($_POST['value_to_change']) {
            case self::PRIZE:
                $storedData['prize'] = $_POST['value'];
                break;
            case self::FIRST_VAR:
                $storedData['first_var'] = $_POST['value'];
                break;
            case self::SECOND_VAR:
                $storedData['second_var'] = $_POST['value'];
                break;
            default:
                break;
        }

        // Prepare updated content to write. The idea is to use keys as column names
        // and values - as field values, accordingly to csv file format.
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

        $generatedFilename = $this->generateFilename($filename, $extension);
        $resource = fopen($generatedFilename, 'wt');
        fwrite($resource, $content, strlen($content));
        fclose($resource);

        return true;
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

    /**
     * Get path to store
     * @return string
     */
    public function getStorePath(): string
    {
        return $this->_storePath;
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
        return $this->reformatData($data, $numOfLines, $filename);
    }

    /**
     * Format data accordingly provided attribute
     * @param array $data
     * @param int $offset
     * @param string $filename
     * @return array
     */
    public function reformatData(array $data, int $offset, string $filename): array
    {
        $dataLen = count($data);

        if ($dataLen <= 1) {
            return [];
        } elseif ($dataLen <= $offset) {
            $offset = $dataLen - 1;
        }

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

        if (strripos($filename, 'lme_average') !== false || strripos($filename, 'minfin_average') !== false) {
            // Фильтруем повторяющиеся средние значения. Массив вида [0 => [0 => timestamp, 1 => value, 2 => date]];
            $filteredData = $this->_uniqueMultidimArray($filteredData, 1);
        }

        //отрицательное смещение для получения данных c конца
        $arrValues = array_slice($filteredData, -($offset));
        $newArr = [];

        $arrKeys = array_slice($data, 0, 1)[0];
        foreach ($arrValues as $arrValue) {
            if (empty($arrValue)) continue;
            $newArr[] = array_combine($arrKeys, $arrValue);
        }

        return $newArr;
    }

    /**
     * Helper method for get unique values from multidimensional array.
     *
     * @param $array
     * @param $key
     * @return array
     */
    private function _uniqueMultidimArray($array, $key) {
        $result = [];
        foreach ($array as $k => $set) {
            if (!isset($temp)) {
                $temp = $set;
                continue; //пропускаем первую итерацию, инициализируем $temp
            }

            if ($set[$key] !== $temp[$key]) {
                $result[] = $temp;
            }

            $temp = $set;
        }

        $last = count($result) - 1;

        if ($result[$last][$key] === $temp[$key]) {
            $result[$last] = $temp;
        } else {
            $result[] = $temp;
        }
        return $result;
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
}
