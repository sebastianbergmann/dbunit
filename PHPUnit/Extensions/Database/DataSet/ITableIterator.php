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
interface PHPUnit_Extensions_Database_DataSet_ITableIterator extends Iterator
{
    /**
     * Returns the current table.
     *
     * @return PHPUnit_Extensions_Database_DataSet_ITable
     */
    public function getTable();

    /**
     * Returns the current table's meta data.
     *
     * @return PHPUnit_Extensions_Database_DataSet_ITableMetaData
     */
    public function getTableMetaData();
}
