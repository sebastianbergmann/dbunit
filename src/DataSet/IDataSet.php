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

use IteratorAggregate;

/**
 * Provides a basic interface for creating and reading data from data sets.
 */
interface IDataSet extends IteratorAggregate
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
     * @param string $tableName
     *
     * @return ITableMetadata
     */
    public function getTableMetaData($tableName);

    /**
     * Returns a table object for the given table.
     *
     * @param string $tableName
     *
     * @return ITable
     */
    public function getTable($tableName);

    /**
     * Returns a reverse iterator for all table objects in the given dataset.
     *
     * @return ITableIterator
     */
    public function getReverseIterator();

    /**
     * Asserts that the given data set matches this data set.
     *
     * @param IDataSet $other
     */
    public function matches(IDataSet $other);
}
