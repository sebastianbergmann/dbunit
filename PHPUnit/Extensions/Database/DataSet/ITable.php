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
 * Provides a basic interface for creating and reading data from data sets.
 *
 * @since      Class available since Release 1.0.0
 */
interface PHPUnit_Extensions_Database_DataSet_ITable
{
    /**
     * Returns the table's meta data.
     *
     * @return PHPUnit_Extensions_Database_DataSet_ITableMetaData
     */
    public function getTableMetaData();

    /**
     * Returns the number of rows in this table.
     *
     * @return int
     */
    public function getRowCount();

    /**
     * Returns the value for the given column on the given row.
     *
     * @param int $row
     * @param int $column
     */
    public function getValue($row, $column);

    /**
     * Returns the an associative array keyed by columns for the given row.
     *
     * @param  int   $row
     * @return array
     */
    public function getRow($row);

    /**
     * Asserts that the given table matches this table.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITable $other
     */
    public function matches(PHPUnit_Extensions_Database_DataSet_ITable $other);
}
