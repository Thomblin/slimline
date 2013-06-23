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
     * @param string $dir
     */
    public function createModels($dir, $namespace = 'de\detert\sebastian\slimline\db\model')
    {
        $tables = $this->repository->getAllTables();

        $templateDir = __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;
        $methodTemplate    = file_get_contents($templateDir . 'methods');

        /** @var Model_Table $table */
        foreach ( $tables as $tableName => $table ) {
            $methods = array();
            $columns = array();

            /** @var Model_Column $column */
            foreach ( $table->columns as $name => $column ) {

                $ucColumn = ucfirst( preg_replace( '/_(.?)/e', "strtoupper('$1')", strtolower( $name ) ) );

                $method = $methodTemplate;
                $method = str_replace('%type%', $column->data_type, $method);
                $method = str_replace('%column%', $name, $method);
                $method = str_replace('%comment%', $column->getDescription(), $method);
                $method = str_replace('%uc_column%', $ucColumn, $method);

                $methods[] = $method;
                $columns[] = "'" . $name . "', // " . $column->getDescription();
            }

            $className = ucfirst( preg_replace( '/(_.?)/e', "strtoupper('$1')", strtolower( $tableName ) ) );

            $generatedTemplate = file_get_contents($templateDir . 'generated');
            $generatedTemplate = str_replace('%name%', $className, $generatedTemplate);
            $generatedTemplate = str_replace('%tableName%', $tableName, $generatedTemplate);
            $generatedTemplate = str_replace('%namespace%', $namespace, $generatedTemplate);
            $generatedTemplate = str_replace('%methods%', implode(PHP_EOL, $methods), $generatedTemplate);
            $generatedTemplate = str_replace('%columns%', implode(PHP_EOL."        ", $columns), $generatedTemplate);

            $filename = $dir . 'generated' . DS . str_replace('_', DS, $tableName) . '.php';
            $this->filePutContents($filename, $generatedTemplate);

            $filename = $dir . DS . str_replace('_', DS, $tableName) . '.php';
            if ( !file_exists($filename) ) {
                $extendedTemplate = file_get_contents($templateDir . 'extended');
                $extendedTemplate = str_replace('%name%', $className, $extendedTemplate);
                $extendedTemplate = str_replace('%namespace%', $namespace, $extendedTemplate);

                $this->filePutContents($filename, $extendedTemplate);
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