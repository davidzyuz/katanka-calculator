<?php
namespace app\renderer;

use app\storeManager\StoreManager;
//TODO: должен использоваться только StoreManager.
//parser перенести в StoreManager
use app\parser\MinfinParser;
use app\parser\LmeParser;


class Renderer
{
    public $minfinData;
    public $lmeData;

    public function __construct()
    {
        $this->minfinData = round((float)(new MinfinParser())->makeIt(), 2);
        $this->lmeData = round((float)(new LmeParser())->makeIt(), 2);
    }

    /**
     * Формула "Лом"
     * @return int|float
     */
    public function wasteFormula()
    {
        return ($this->lmeData + 250) * $this->minfinData * 1.2 * 0.9 - 22000;
    }

    /**
     * Формула "б/н"
     * @return int/float
     */
    public function bnFormula()
    {
        return ($this->lmeData + 250) * $this->minfinData * 1.2;
    }

    /**
     * Формула "cash"
     * @return int/float
     */
    public function cashFormula()
    {
        $part = ($this->lmeData + 250) * $this->minfinData * 1.2;
        return $part - $part * 0.1;
    }

    /**
     * Render an end data
     */
    public function render()
    {
        return $this->wasteFormula();
    }
}
