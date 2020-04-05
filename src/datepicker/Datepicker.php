<?php

namespace app\datepicker;

use app\storeManager\StoreManager;

class Datepicker
{
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
    public function findSpecific(array $data)
    {
        return array_filter($data, function ($el) {
            return in_array($this->_searchTarget, $el);
        });
    }

    public function populateSpecific()
    {
        array_map(function ($store) {
            $specificStore = 'specific' . ucfirst($store);
            $this->$specificStore = $this->findSpecific($this->$store);
        }, $this->_stores);
    }
}
