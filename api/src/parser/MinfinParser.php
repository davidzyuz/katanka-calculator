<?php
namespace app\parser;

use Goutte\Client;

class MinfinParser extends Parser
{
    public $crawler;
    public $haystack;
    /* TODO: заделать возможность передачи кастомного индекса
      * для получения нужного параметра через $valueIndex
    */
    public $valueIndex;
    private $_needle;

    /**
     * @param $needle
     */
    public function setNeedle($needle)
    {
        $this->_needle = $needle;
    }

    public function getNeedle()
    {
        return $this->_needle;
    }

    /**
     * @return MinfinParser
     */
    public function fetchData()
    {
        $client = new Client();
        $this->crawler = $client->request('GET', self::MINFIN_URL);
        return $this;
    }

    /**
     * @return MinfinParser
     */
    public function fetchHaystack()
    {
        $this->haystack = $this->crawler
            ->filter("table.mb-table-currency tbody td[data-title=\"Доллар\"]")
            ->each(function ($node) {
                return $node->text();
            });

        return $this;
    }

    public function findNeedle()
    {
        isset($this->valueIndex)
            ? $needleStr = trim($this->haystack[$this->valueIndex])
            : $needleStr = trim(end($this->haystack));

        $this->setNeedle(substr($needleStr, 0, 7));
        return $this;
    }

    /**
     * Make Parser great again
     */
    public function makeIt()
    {
        return $this
            ->fetchData()
            ->fetchHaystack()
            ->findNeedle()
            ->getNeedle();
    }
}
