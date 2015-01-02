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
 * Deletes all rows from all tables in a dataset.
 *
 * @package    DbUnit
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  2010-2014 Mike Lively <m@digitalsandwich.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_Operation_DeleteAll implements PHPUnit_Extensions_Database_Operation_IDatabaseOperation
{

    public function execute(PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
    {
        foreach ($dataSet->getReverseIterator() as $table) {
            /* @var $table PHPUnit_Extensions_Database_DataSet_ITable */

            $query = "
                DELETE FROM {$connection->quoteSchemaObject($table->getTableMetaData()->getTableName())}
            ";

            try {
                $connection->getConnection()->query($query);
            } catch (PDOException $e) {
                throw new PHPUnit_Extensions_Database_Operation_Exception('DELETE_ALL', $query, array(), $table, $e->getMessage());
            }
        }
    }
}
