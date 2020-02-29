<?php
namespace app\parser;

use Goutte\Client;

class LmeParser extends Parser
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
     * @return LmeParser
     */
    public function fetchData()
    {
        $client = new Client();
        $this->crawler = $client->request('GET', self::LME_COPPER_URL, [
            'Cache-Control' => 'no-cache'
        ]);
        return $this;
    }

    /**
     * @return LmeParser
     */
    public function fetchHaystack()
    {
        $this->haystack = $this->crawler->filter('div.table-wrapper:first-of-type table tbody tr:first-child')->each(function ($node) {
           return $node->text();
        });
        return $this;
    }

    public function findNeedle()
    {
        $arrValues = array_unique(explode(' ', $this->haystack[0]));

        isset($this->valueIndex)
            ? $this->setNeedle(trim($arrValues[$this->valueIndex]))
            : $this->setNeedle(trim(end($arrValues)));

        return $this;
    }

    /**
     * Make parser Great again
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
