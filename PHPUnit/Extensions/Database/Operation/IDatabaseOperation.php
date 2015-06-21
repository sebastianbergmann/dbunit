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
 * Provides a basic interface and functionality for executing database
 * operations against a connection using a specific dataSet.
 *
 * @since      Class available since Release 1.0.0
 */
interface PHPUnit_Extensions_Database_Operation_IDatabaseOperation
{
    /**
     * Executes the database operation against the given $connection for the
     * given $dataSet.
     *
     * @param  PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection
     * @param  PHPUnit_Extensions_Database_DataSet_IDataSet       $dataSet
     * @throws PHPUnit_Extensions_Database_Operation_Exception
     */
    public function execute(PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet);
}
