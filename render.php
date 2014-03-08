<?php
namespace de\detert\sebastian\slimline;

use de\detert\sebastian\slimline\Exception\Error;

/**
 * class Render is used to load template files and pass values to them
 *
 * @author sebastian.detert <github@elygor.de>
 * @date 13.12.12
 * @time 23:30
 * @license property of Sebastian Detert
 */
abstract class Render
{
    /**
     * @var string
     */
    protected $root;
    /**
     * @var Translate
     */
    private $translate;

    /**
     * @param string $root directory which contains all templates
     */
    public function __construct($root)
    {
        $this->root = $root;
    }

    /**
     * @param string $template
     * @param mixed  $content  values to be passed to template
     */
    public function render($template, $content)
    {
        if ( is_object($content) ) {
            if ( isset($content->content) ) {
                throw new Error("\$content->content should not be used");
            }
            extract(get_object_vars($content));
        } elseif ( is_array($content) ) {
            if ( isset($content['content']) ) {
                throw new Error("\$content['content'] should not be used");
            }
            extract($content);
        }

        require $this->root . DS . $this->getTemplateFolder() . DS . $template;
    }

    /**
     * @return string
     */
    protected abstract function getTemplateFolder();

    public function setTranslation(Translate $translate)
    {
        $this->translate = $translate;
    }

    /**
     * @param string $category
     * @param string $text
     * @param array $params
     *
     * @return string
     */
    public function getTranslation($category, $text, array $params = array())
    {
        return isset($this->translate)
            ? $this->translate->getTranslation($category, $text, $params)
            : $text;
    }

    /**
     * @param array $a
     * @return string
     */
    public abstract function printTable(array $a);
}
