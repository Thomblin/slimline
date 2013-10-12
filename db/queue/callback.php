<?php
/**
 * @author sebastian.detert <github@elygor.de>
 * @date 08.10.13
 * @time 20:57
 * @license property of Sebastian Detert
 */

namespace de\detert\sebastian\slimline\db\queue;


interface Callback
{
    public function catchJobResult(Job $job, $result);
    public function catchException(\Exception $e);
    public function catchJobException(Job $job, \Exception $e);
}