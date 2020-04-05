<?php
namespace app\parser;

use Goutte\Client;

class LmeParser extends Parser
{
    public $crawler;
    public $haystack;
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
        $client->setHeader('Cache-Control', 'no-cache');
        $client->setHeader('Clear-Site-Data', '*');
        $client->setHeader('Cookie', 'foo');
        $this->crawler = $client->request('GET', self::LME_COPPER_URL);
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
