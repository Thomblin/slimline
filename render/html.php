<?php
namespace de\detert\sebastian\slimline;

/**
 * Render_Html is used to create formatted html output
 *
 * @author sebastian.detert <github@elygor.de>
 * @date 13.12.12
 * @time 23:30
 * @license property of Sebastian Detert
 */
class Render_Html extends Render
{
    /**
     * @return string
     */
    protected function getTemplateFolder()
    {
        return 'html';
    }

    /**
     * @param Response_Form $response
     * @param string $name
     */
    public function getFormClass(Response_Form $response, $name)
    {
        if ( isset($response->errorFields[$name]) ) {
            return " has-error";
        }

        return "";
    }

    /**
     * @param array $a
     * @return string
     */
    public function printTable(array $a)
    {
        $table  = '<table>';
        $table .= '<tr><th>'.implode('</th><th>', array_keys($a[0])).'</th></tr>';

        foreach($a as $row){
            $table.= '<tr><td>'.implode('</td><td>', $row).'</td></tr>';
        }

        return $table . '</table>';
    }
}
