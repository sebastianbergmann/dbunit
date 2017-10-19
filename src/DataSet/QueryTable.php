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

use PDO;
use PHPUnit\DbUnit\Database\Connection;

/**
 * Provides the functionality to represent a database table.
 */
class QueryTable extends AbstractTable
{
    /**
     * @var string
     */
    protected $query;

    /**
     * @var Connection
     */
    protected $databaseConnection;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * Creates a new database query table object.
     *
     * @param string     $table_name
     * @param string     $query
     * @param Connection $databaseConnection
     */
    public function __construct($tableName, $query, Connection $databaseConnection)
    {
        $this->query              = $query;
        $this->databaseConnection = $databaseConnection;
        $this->tableName          = $tableName;
    }

    /**
     * Returns the table's meta data.
     *
     * @return ITableMetadata
     */
    public function getTableMetaData()
    {
        $this->createTableMetaData();

        return parent::getTableMetaData();
    }

    /**
     * Checks if a given row is in the table
     *
     * @param array $row
     *
     * @return bool
     */
    public function assertContainsRow(array $row)
    {
        $this->loadData();

        return parent::assertContainsRow($row);
    }

    /**
     * Returns the number of rows in this table.
     *
     * @return int
     */
    public function getRowCount()
    {
        $this->loadData();

        return parent::getRowCount();
    }

    /**
     * Returns the value for the given column on the given row.
     *
     * @param int $row
     * @param int $column
     */
    public function getValue($row, $column)
    {
        $this->loadData();

        return parent::getValue($row, $column);
    }

    /**
     * Returns the an associative array keyed by columns for the given row.
     *
     * @param int $row
     *
     * @return array
     */
    public function getRow($row)
    {
        $this->loadData();

        return parent::getRow($row);
    }

    /**
     * Asserts that the given table matches this table.
     *
     * @param ITable $other
     */
    public function matches(ITable $other)
    {
        $this->loadData();

        return parent::matches($other);
    }

    protected function loadData()
    {
        if ($this->data === null) {
            $pdoStatement = $this->databaseConnection->getConnection()->query($this->query);
            $this->data   = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    protected function createTableMetaData()
    {
        if ($this->tableMetaData === null) {
            $this->loadData();

            // if some rows are in the table
            $columns = [];
            if (isset($this->data[0])) {
                // get column names from data
                $columns = \array_keys($this->data[0]);
            } else {
                // if no rows found, get column names from database
                $pdoStatement = $this->databaseConnection->getConnection()->prepare('SELECT column_name FROM information_schema.COLUMNS WHERE table_schema=:schema AND table_name=:table');
                $pdoStatement->execute([
                    'table'  => $this->tableName,
                    'schema' => $this->databaseConnection->getSchema()
                ]);

                $columns = $pdoStatement->fetchAll(PDO::FETCH_COLUMN, 0);
            }
            // create metadata
            $this->tableMetaData = new DefaultTableMetadata($this->tableName, $columns);
        }
    }
}
