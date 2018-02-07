<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\DataSet;

use PHPUnit\DbUnit\InvalidArgumentException;

/**
 * Implements the basic functionality of data sets using a PHP array.
 */
class ArrayDataSet extends AbstractDataSet
{
    /**
     * @var array
     */
    protected $tables = [];

    /**
     * Constructor to build a new ArrayDataSet with the given array.
     * The array parameter is an associative array of tables where the key is
     * the table name and the value an array of rows. Each row is an associative
     * array by itself with keys representing the field names and the values the
     * actual data.
     * For example:
     * array(
     *     "addressbook" => array(
     *         array("id" => 1, "name" => "...", "address" => "..."),
     *         array("id" => 2, "name" => "...", "address" => "...")
     *     )
     * )
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        foreach ($data as $tableName => $rows) {
            $columns = [];
            if (isset($rows[0])) {
                $columns = \array_keys($rows[0]);
            }

            $metaData = new DefaultTableMetadata($tableName, $columns);
            $table    = new DefaultTable($metaData);

            foreach ($rows as $row) {
                $table->addRow($row);
            }
            $this->tables[$tableName] = $table;
        }
    }

    public function getTable($tableName)
    {
        if (!isset($this->tables[$tableName])) {
            throw new InvalidArgumentException("$tableName is not a table in the current database.");
        }

        return $this->tables[$tableName];
    }

    protected function createIterator($reverse = false)
    {
        return new DefaultTableIterator($this->tables, $reverse);
    }
}
