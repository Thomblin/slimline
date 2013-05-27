<?php
namespace de\detert\sebastian\slimline;

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
                trigger_error("\$content->content should not be used", E_USER_WARNING);
            }
            extract(get_object_vars($content));
        } elseif ( is_array($content) ) {
            if ( isset($content->content) ) {
                trigger_error("\$content['content'] should not be used", E_USER_WARNING);
            }
            extract($content);
        }

        require $this->root . DS . $this->getTemplateFolder() . DS . $template;
    }

    /**
     * @return string
     */
    protected abstract function getTemplateFolder();
}
