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
    public function fetchFormulaValues(string $name, string $extension = 'csv')
    {
        $filename = $filename = $this->generateFilename($name, $extension);
        $resource = fopen($filename, 'r+t');
        $keys = [];
        $values = [];
        for ($i = 0; $line = fgetcsv($resource, 1000, ','); $i += 1) {
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
    public function updateFormulaValues(string $name, array $newValues)
    {

    }

    public function updateMainStore(string $name, float $data, string $extension = 'csv')
    {
        $filename = $this->generateFilename($name, $extension);
        $resource = fopen($filename, 'rt');
        $data = [];
        for ($i = 0; $line = fgetcsv($resource, 1000, ','); $i += 1) {
            $data[] = $line;
        }
    }

    public function writeToMainStore()
    {
        if (date('D') === 'Sat' || date('D') === 'Sun') {
            return 0;
        }
        $parser = new LmeParser();
        $data = round((float)$parser->makeIt(), 2);
        $filename = $this->generateFilename('lme');
        $resource = fopen($filename, 'a+t');
        $timestamp = time();
        $date = date('d:m:Y', $timestamp);
        $string = "${timestamp},${data},{$date}\n";
        fwrite($resource, $string, strlen($string));
        fclose($resource);
    }

    public function fetchFromMainStore()
    {
        $filename = $this->generateFilename('lme');
        $resource = fopen($filename, 'rt');
        $data = [];
        while (!feof($resource)) {
            $data[] = fgetcsv($resource, 1000, ',');
        }
        fclose($resource);
        return $data;
    }

    /**
     * Generates a path to specific store
     * @param string $name
     * @param string $extension
     * @return string
     */
    public function generateFilename(string $name, string $extension = 'csv')
    {
        $path = $this->getStorePath();
        return $path . $name . '.' . $extension;
    }
}
