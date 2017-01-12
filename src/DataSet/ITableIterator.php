<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\DataSet;

use Iterator;
use PHPUnit_Extensions_Database_DataSet_ITableMetaData;

/**
 * Provides a basic interface for creating and reading data from data sets.
 */
interface ITableIterator extends Iterator
{
    /**
     * Returns the current table.
     *
     * @return ITable
     */
    public function getTable();

    /**
     * Returns the current table's meta data.
     *
     * @return PHPUnit_Extensions_Database_DataSet_ITableMetaData
     */
    public function getTableMetaData();
}
