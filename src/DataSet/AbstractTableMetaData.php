<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Provides basic functionality for table meta data.
 *
 * @since      Class available since Release 1.0.0
 */
abstract class PHPUnit_Extensions_Database_DataSet_AbstractTableMetaData implements PHPUnit_Extensions_Database_DataSet_ITableMetaData
{
    /**
     * The names of all columns in the table.
     *
     * @var Array
     */
    protected $columns;

    /**
     * The names of all the primary keys in the table.
     *
     * @var Array
     */
    protected $primaryKeys;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * Returns the names of the columns in the table.
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Returns the names of the primary key columns in the table.
     *
     * @return array
     */
    public function getPrimaryKeys()
    {
        return $this->primaryKeys;
    }

    /**
     * Returns the name of the table.
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Asserts that the given tableMetaData matches this tableMetaData.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITableMetaData $other
     */
    public function matches(PHPUnit_Extensions_Database_DataSet_ITableMetaData $other)
    {
        if ($this->getTableName() != $other->getTableName() ||
            $this->getColumns() != $other->getColumns()) {
            return FALSE;
        }

        return TRUE;
    }
}
