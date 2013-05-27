<?php
/**
 * @author sebastian.detert <github@elygor.de>
 * @date 26.05.13
 * @time 22:37
 * @license property of Sebastian Detert
 */

namespace de\detert\sebastian\slimline\db;

class CreateFoo2 extends Migration_Statement
{
    public function up()
    {
        $sql = 'CREATE TABLE `foo` (`id` INT(20))';
        $this->db->query($sql);
    }
}