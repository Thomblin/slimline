<?php
namespace de\detert\sebastian\slimline;

/**
 * Render_Plain is used to create straight forward output of Response
 *
 * @author sebastian.detert <github@elygor.de>
 * @date 13.12.12
 * @time 23:30
 * @license property of Sebastian Detert
 */
class Render_Plain extends Render
{
    /**
     * @return string
     */
    protected function getTemplateFolder()
    {
        return 'plain';
    }
}
