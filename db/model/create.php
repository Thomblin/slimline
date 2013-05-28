<?php
namespace de\detert\sebastian\slimline\db;

/**
 * create simple models for each db table
 *
 * @author sebastian.detert <github@elygor.de>
 * @date 28.05.13
 * @time 19:15
 * @license property of Sebastian Detert
 */
class Model_Create
{
    /**
     * @var Model_Repository
     */
    private $repository;

    /**
     * @param Model_Repository $repository
     */
    public function __construct(Model_Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param callback $callback
     */
    public function setClassNameCallback($callback)
    {
        $this->classNameCallback = $callback;
    }

    /**
     * @param string $dir
     */
    public function createModels($dir, $namespace = 'de\detert\sebastian\slimline\db\model')
    {
        $tables = $this->repository->getAllTables();

        /** @var Model_Table $table */
        foreach ( $tables as $tableName => $table ) {
            $columns = '';

            /** @var Model_Column $column */
            foreach ( $table->columns as $name => $column ) {

                $columns .= "
    /**
     * {$column->getDescription()}
     *
     * @var {$column->data_type}
     */
    public \$$name;";

            }

            $className = ucfirst( preg_replace( '/(_.?)/e', "strtoupper('$1')", strtolower( $tableName ) ) );

            $template = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'template_generated');
            $template = str_replace('%name%', $className, $template);
            $template = str_replace('%namespace%', $namespace, $template);
            $template = str_replace('%columns%', $columns, $template);

            $filename = $dir . 'generated' . DS . str_replace('_', DS, $tableName) . '.php';
            $this->filePutContents($filename, $template);

            $filename = $dir . DS . str_replace('_', DS, $tableName) . '.php';
            if ( !file_exists($filename) ) {
                $template = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'template_extend');
                $template = str_replace('%name%', $className, $template);
                $template = str_replace('%namespace%', $namespace, $template);

                $this->filePutContents($filename, $template);
            }
        }
    }

    /**
     * @param string $filename
     * @param string $template
     */
    private function filePutContents($filename, $template)
    {
        if (!file_exists(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }

        file_put_contents($filename, $template);
    }
}