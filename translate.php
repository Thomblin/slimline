<?php
/**
 * @author sebastian.detert <github@elygor.de>
 * @date 01.10.13
 * @time 20:42
 * @license property of Sebastian Detert
 */

namespace de\detert\sebastian\slimline;


class Translate
{
    /**
     * @var string
     */
    private $docRoot;
    /**
     * @var string
     */
    private $language;
    /**
     * @var array
     */
    private $cache = array();

    public function __construct($docRoot, $language)
    {
        $this->docRoot  = $docRoot;
        $this->language = $language;
    }

    /**
     * @param string $category
     * @param string $text
     *
     * @return string
     */
    public function getTranslation($category, $text)
    {
        $this->loadCategory($category);

        return isset($this->cache[$category][$text])
            ? $this->cache[$category][$text]
            : $text;
    }

    private function loadCategory($category)
    {
        if ( isset($this->cache[$category]) ) {
            return;
        }

        $path = $this->docRoot . DIRECTORY_SEPARATOR . $this->language . DIRECTORY_SEPARATOR . $category . ".php";

        $this->cache[$category] = require $path;
    }
}