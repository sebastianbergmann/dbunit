<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use PHPUnit\DbUnit\Database\IConnection;
use PHPUnit\DbUnit\DataSet\IDataSet;

/**
 * Provides a basic interface and functionality for executing database
 * operations against a connection using a specific dataSet.
 */
interface PHPUnit_Extensions_Database_Operation_IDatabaseOperation
{
    /**
     * Executes the database operation against the given $connection for the
     * given $dataSet.
     *
     * @param  IConnection $connection
     * @param  IDataSet       $dataSet
     * @throws PHPUnit_Extensions_Database_Operation_Exception
     */
    public function execute(IConnection $connection, IDataSet $dataSet);
}
