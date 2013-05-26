<?php
/**
 * @author sebastian.detert <github@elygor.de>
 * @date 26.05.13
 * @time 22:37
 * @license property of Sebastian Detert
 */

namespace de\detert\sebastian\slimline\db;

class ThrowException1 extends MigrationStatement
{
    public function up()
    {
        throw new \Exception('this class was not expected to be called');
    }
}