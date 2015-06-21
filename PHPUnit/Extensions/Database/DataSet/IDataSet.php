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
interface PHPUnit_Extensions_Database_DataSet_IDataSet extends IteratorAggregate
{
    /**
     * Returns an array of table names contained in the dataset.
     *
     * @return array
     */
    public function getTableNames();

    /**
     * Returns a table meta data object for the given table.
     *
     * @param  string                                             $tableName
     * @return PHPUnit_Extensions_Database_DataSet_ITableMetaData
     */
    public function getTableMetaData($tableName);

    /**
     * Returns a table object for the given table.
     *
     * @param  string                                     $tableName
     * @return PHPUnit_Extensions_Database_DataSet_ITable
     */
    public function getTable($tableName);

    /**
     * Returns a reverse iterator for all table objects in the given dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_ITableIterator
     */
    public function getReverseIterator();

    /**
     * Asserts that the given data set matches this data set.
     *
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $other
     */
    public function matches(PHPUnit_Extensions_Database_DataSet_IDataSet $other);
}
