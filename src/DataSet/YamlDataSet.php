<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use PHPUnit\DbUnit\DataSet\AbstractDataSet;
use PHPUnit\DbUnit\DataSet\DefaultTable;
use PHPUnit\DbUnit\DataSet\DefaultTableIterator;

/**
 * Creates YamlDataSets.
 *
 * You can incrementally add YAML files as tables to your datasets
 */
class PHPUnit_Extensions_Database_DataSet_YamlDataSet extends AbstractDataSet
{
    /**
     * @var array
     */
    protected $tables = [];

    /**
     * @var PHPUnit_Extensions_Database_DataSet_IYamlParser
     */
    protected $parser;

    /**
     * Creates a new YAML dataset
     *
     * @param string                                          $yamlFile
     * @param PHPUnit_Extensions_Database_DataSet_IYamlParser $parser
     */
    public function __construct($yamlFile, $parser = null)
    {
        if ($parser == null) {
            $parser = new PHPUnit_Extensions_Database_DataSet_SymfonyYamlParser();
        }
        $this->parser = $parser;
        $this->addYamlFile($yamlFile);
    }

    /**
     * Adds a new yaml file to the dataset.
     * @param string $yamlFile
     */
    public function addYamlFile($yamlFile)
    {
        $data = $this->parser->parseYaml($yamlFile);

        foreach ($data as $tableName => $rows) {
            if (!isset($rows)) {
                $rows = [];
            }

            if (!is_array($rows)) {
                continue;
            }

            if (!array_key_exists($tableName, $this->tables)) {
                $columns = $this->getColumns($rows);

                $tableMetaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData(
                  $tableName, $columns
                );

                $this->tables[$tableName] = new DefaultTable(
                  $tableMetaData
                );
            }

            foreach ($rows as $row) {
                $this->tables[$tableName]->addRow($row);
            }
        }
    }

    /**
     * Creates a unique list of columns from all the rows in a table.
     * If the table is defined another time in the Yaml, and if the Yaml
     * parser could return the multiple occerrences, then this would be
     * insufficient unless we grouped all the occurences of the table
     * into onwe row set.  sfYaml, however, does not provide multiple tables
     * with the same name, it only supplies the last table.
     *
     * @params all the rows in a table.
     */
    private function getColumns($rows) {
        $columns = [];

        foreach ($rows as $row) {
            $columns = array_merge($columns, array_keys($row));
        }

        return array_values(array_unique($columns));
    }

    /**
     * Creates an iterator over the tables in the data set. If $reverse is
     * true a reverse iterator will be returned.
     *
     * @param  bool                                               $reverse
     * @return PHPUnit_Extensions_Database_DataSet_ITableIterator
     */
    protected function createIterator($reverse = false)
    {
        return new DefaultTableIterator(
          $this->tables, $reverse
        );
    }
}
