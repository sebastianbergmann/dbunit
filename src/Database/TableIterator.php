<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\Database;

use PHPUnit\DbUnit\DataSet\ITable;
use PHPUnit\DbUnit\DataSet\ITableIterator;
use PHPUnit\DbUnit\DataSet\ITableMetadata;

/**
 * Provides iterative access to tables from a database instance.
 */
class TableIterator implements ITableIterator
{
    /**
     * An array of tablenames.
     *
     * @var array
     */
    protected $tableNames;

    /**
     * If this property is true then the tables will be iterated in reverse
     * order.
     *
     * @var bool
     */
    protected $reverse;

    /**
     * The database dataset that this iterator iterates over.
     *
     * @var DataSet
     */
    protected $dataSet;

    public function __construct($tableNames, DataSet $dataSet, $reverse = false)
    {
        $this->tableNames = $tableNames;
        $this->dataSet    = $dataSet;
        $this->reverse    = $reverse;

        $this->rewind();
    }

    /**
     * Returns the current table.
     *
     * @return ITable
     */
    public function getTable()
    {
        return $this->current();
    }

    /**
     * Returns the current table's meta data.
     *
     * @return ITableMetadata
     */
    public function getTableMetaData()
    {
        return $this->current()->getTableMetaData();
    }

    /**
     * Returns the current table.
     *
     * @return ITable
     */
    public function current()
    {
        $tableName = \current($this->tableNames);

        return $this->dataSet->getTable($tableName);
    }

    /**
     * Returns the name of the current table.
     *
     * @return string
     */
    public function key()
    {
        return $this->current()->getTableMetaData()->getTableName();
    }

    /**
     * advances to the next element.
     */
    public function next()
    {
        if ($this->reverse) {
            \prev($this->tableNames);
        } else {
            \next($this->tableNames);
        }
    }

    /**
     * Rewinds to the first element
     */
    public function rewind()
    {
        if ($this->reverse) {
            \end($this->tableNames);
        } else {
            \reset($this->tableNames);
        }
    }

    /**
     * Returns true if the current index is valid
     *
     * @return bool
     */
    public function valid()
    {
        return (\current($this->tableNames) !== false);
    }
}
