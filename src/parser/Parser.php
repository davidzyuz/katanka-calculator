<?php
namespace app\parser;

abstract class Parser
{
    // The London metal exchange copper url
    const LME_COPPER_URL = 'https://www.lme.com/en-GB/Metals/Non-ferrous/Copper#tabIndex=0';
    // The minfin currency url
    const MINFIN_URL = 'https://minfin.com.ua/currency/mb/';

    /**
     * @return mixed
     */
    protected abstract function fetchData();
}
