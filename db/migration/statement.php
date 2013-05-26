<?php
/**
 * @author sebastian.detert <github@elygor.de>
 * @date 26.05.13
 * @time 22:28
 * @license property of Sebastian Detert
 */

namespace de\detert\sebastian\slimline\Db;

abstract class MigrationStatement
{
    protected $db;

    public function __construct(Handler $db)
    {
        $this->db = $db;
    }

    public abstract function up();
}