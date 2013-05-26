<?php
namespace de\detert\sebastian\slimline\Db;

/**
 * database handler for sql queries and more
 *
 * @author sebastian.detert <github@elygor.de>
 * @date 25.05.13
 * @time 09:58
 * @license property of Sebastian Detert
 */
class Handler
{
    /**
     * @var PDO
     */
    private $db;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->db = new \PDO($config->dsn, $config->user, $config->password);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return void
     */
    public function query($sql, array $params = array())
    {
        $this->prepareAndExecute($sql, $params);
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return PDOStatement
     */
    private function prepareAndExecute($sql, array $params = array())
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement;
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return array
     */
    public function fetchAll($sql, array $params = array())
    {
        return $this->prepareAndExecute($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql
     * @param string $indexColumn
     * @param array  $params
     *
     * @return array
     */
    public function fetchIndexedBy($sql, $indexColumn, array $params = array())
    {
        $results = array();
        foreach ( $this->fetchAll($sql, $params) as $row ) {
            $results[$row[$indexColumn]] = $row;
        }

        return $results;
    }
}