<?php

namespace app\datepicker;

use app\calculator\Calculator;
use app\storeManager\StoreManager;

class Datepicker
{

    // TODO: фильтр повторяющихся значений для средних лме и валюты
    // TODO: брать близжайшее лме или валюту, если на указанную дату данных нет
    public $specificLme;
    public $specificLmeAverage;
    public $specificMinfin;
    public $specificMinfinAverage;

    private $_stores;
    private $_store;
    private $_lme;
    private $_lmeAverage;
    private $_minfin;
    private $_minfinAverage;
    private $_searchTarget;

    /**
     * Datepicker constructor.
     * @param string $searchTarget
     */
    public function __construct(string $searchTarget)
    {
        $this->_store = new StoreManager();
        $this->_stores = ['lme', 'lmeAverage', 'minfin', 'minfinAverage'];
        $this->_searchTarget = $searchTarget;
        $this->populateData();
        $this->populateSpecific();
    }

    /**
     * Available properties: store, lme, lmeAverage, minfin, minfinAverage
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        $privateProp = '_' . $property;
        if (property_exists($this, $property)) {
            return $this->$property;
        } elseif (property_exists($this, $privateProp)) {
            return $this->$privateProp;
        } else {
            return null;
        }
    }

    /**
     * Generate underscore case filename from camel case
     * @param string $storename
     * @return string
     */
    public function generateFilename(string $storename): string
    {
        if ($storename === strtolower($storename)) {
            return $storename;
        }

        $len = strlen($storename);
        $index = null;
        for ($i = 0; $i < $len; $i += 1) {
            if ($storename[$i] === ucfirst($storename[$i])) {
                $index = $i;
            }
        }

        $start = substr($storename, 0, $index);
        $end = strtolower(substr($storename, $index));
        return $start . '_' . $end;
    }

    /**
     * Set data to properties.
     */
    public function populateData()
    {
        array_map(function ($store) {
            $filename = $this->generateFilename($store);
            $this->$store = $this->_store->fetchFromStore($filename);
        }, $this->_stores);
    }

    /**
     * Finds specific record in a provided data.
     * @param array $data
     * @return array
     */
    private function findSpecific(array $data)
    {
        $date = new \DateTime($this->_searchTarget);
        $specific = [];
        $condition = true;

        while ($condition) {
            $format = $date->format('d-m-Y');
            $specific = array_filter($data, function ($el) use ($format) {
                return in_array($format, $el);
            });

            empty($specific) ? $date->modify('-1 day') : $condition = false;
        }

        return $specific;
    }

    /**
     * Generates a specific prop name.
     * @param string $storeName
     * @return string
     */
    private function generateSpecificPropName(string $storeName): string
    {
        return 'specific' . ucfirst($storeName);
    }

    /**
     * Populates specific properties, such as specificLme and other.
     */
    public function populateSpecific()
    {
        array_map(function ($store) {
            $specificStore = $this->generateSpecificPropName($store);
            $this->$specificStore = $this->findSpecific($this->$store);
        }, $this->_stores);
    }

    /**
     * Array of all requested concrete values
     * @return mixed
     */
    public function getAllSpecificValues()
    {
        $data =  array_reduce($this->_stores, function($acc, $curr) {
            $specificStore = $this->generateSpecificPropName($curr);
            foreach ($this->$specificStore as $batchData) {
                foreach ($batchData as $key => $value) {
                    $acc[$curr][$key] = $value;
                }
            }
            return $acc;
        }, []);

        $store = new StoreManager();
        $calc = new Calculator();
        $formulaValues = $store->fetchFormulaValues();
        $data['bnValue'] = $calc->bnFormula(
            $data['lmeAverage']['value'],
            $data['minfinAverage']['value'],
            $formulaValues['prize']
        );
        $data['cashValue'] = $calc->cashFormula(
            $data['lmeAverage']['value'],
            $data['minfinAverage']['value'],
            $formulaValues['prize']
        );

        return $data;
    }
}
