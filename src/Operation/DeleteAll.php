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

/**
 * Deletes all rows from all tables in a dataset.
 */
class PHPUnit_Extensions_Database_Operation_DeleteAll implements PHPUnit_Extensions_Database_Operation_IDatabaseOperation
{
    public function execute(IConnection $connection, PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
    {
        foreach ($dataSet->getReverseIterator() as $table) {
            /* @var $table PHPUnit_Extensions_Database_DataSet_ITable */

            $query = "
                DELETE FROM {$connection->quoteSchemaObject($table->getTableMetaData()->getTableName())}
            ";

            try {
                $connection->getConnection()->query($query);
            } catch (PDOException $e) {
                throw new PHPUnit_Extensions_Database_Operation_Exception('DELETE_ALL', $query, [], $table, $e->getMessage());
            }
        }
    }
}
