<?php
namespace de\detert\sebastian\slimline;

use de\detert\sebastian\slimline\db\Handler;

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 26.05.13
 * @time 22:28
 * @license property of Sebastian Detert
 */
class Repository
{
    protected $db;

    public function __construct(Handler $db)
    {
        $this->db = $db;
    }
}