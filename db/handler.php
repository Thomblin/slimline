<?php
namespace de\detert\sebastian\slimline\db;

use de\detert\sebastian\slimline\Exception\Pdo;
use de\detert\sebastian\slimline\Response_Debug_Sql;

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
     * @var Response_Debug_Sql
     */
    private $debug;

    const ROW_INSERTED = 1;
    const ROW_UPDATED  = 2;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->db = new \PDO($config->dsn, $config->user, $config->password);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->db->exec("SET NAMES UTF8");
    }

    /**
     * @param Response_Debug_Sql $debug
     */
    public function setDebugResponse(Response_Debug_Sql $debug)
    {
        $this->debug = $debug;
    }
    /**
     * @param string $sql
     * @param array  $params
     */
    private function startDebug($sql, array $params = array())
    {
       if ( !is_null($this->debug) ) {
           $this->debug->start($sql, $params);
       }
    }

    public function stopDebug()
    {
        if ( !is_null($this->debug) ) {
            $this->debug->stop();
        }
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
        try {
            $this->startDebug($sql, $params);
            $statement = $this->db->prepare($sql);
            $statement->execute($params);
            $this->stopDebug();
        } catch(\PDOException $e) {
            $this->stopDebug();
            throw new Pdo(
                $e->getMessage() . PHP_EOL .
                $sql . PHP_EOL .
                print_r($params, true) . PHP_EOL
            );
        }

        return $statement;
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return array
     */
    public function fetch($sql, array $params = array())
    {
        return $this->prepareAndExecute($sql, $params)->fetch(\PDO::FETCH_ASSOC);
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

    /**
     * @param string $class
     * @param array  $where
     */
    public function loadModel($class, array $where)
    {
        /** @var Model $model */
        $model = new $class();
        $table = $model->getTableName();

        $sql = "SELECT
                *
            FROM
                `$table`
            WHERE
                `" . implode('` = ? AND `', array_keys($where)) . "` = ?
            LIMIT 1";

        $result = $this->fetch($sql, array_values($where));

        if ( empty($result) ) {
            throw new Exception_Notfound("model $class not found with " . print_r($where, true));
        }

        $model->fromArray($result);

        return $model;
    }

    /**
     * @param Model $model
     */
    public function saveModel(Model $model)
    {
        $data  = $model->toArray();

        if ( empty($data) ) {
            return;
        }

        $table = $model->getTableName();

        $columns = "`" . implode('`,`', array_keys($data)) . "`";
        $values  = implode(', ', array_fill(0, count($data), '?'));

        $update = "`" . implode('` = ?, `', array_keys($data)) . "` = ?";

        $params = array_merge(array_values($data), array_values($data));

        $sql = "INSERT INTO `$table`
            ($columns) VALUES
            ($values)
            ON DUPLICATE KEY UPDATE $update";

        $this->query($sql, $params);
    }

    /**
     * @param string $class
     * @param array  $where
     */
    public function loadAllModels($class, array $where)
    {
        $models = array();

        /** @var Model $model */
        $model = new $class();
        $table = $model->getTableName();

        $sql = "SELECT
                *
            FROM
                `$table`
            " . (empty($where)
                ? ""
                : "WHERE `" . implode('` = ? AND `', array_keys($where)) . "` = ?");

        $result = $this->fetchAll($sql, array_values($where));

        foreach ( $result as $row ) {
            $model = new $class();
            $model->fromArray($row);
            $models[] = $model;
        }

        return $models;
    }

    public function getAffectedRows()
    {
        $result = $this->fetch("SELECT ROW_COUNT()");

        return current($result);
    }
}