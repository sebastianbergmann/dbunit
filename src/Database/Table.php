<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use PHPUnit\DbUnit\Database\DataSet;
use PHPUnit\DbUnit\Database\IConnection;

/**
 * Provides the functionality to represent a database table.
 */
class PHPUnit_Extensions_Database_DB_Table extends PHPUnit_Extensions_Database_DataSet_AbstractTable
{
    /**
     * Creates a new database table object.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITableMetaData $tableMetaData
     * @param IConnection $databaseConnection
     */
    public function __construct(PHPUnit_Extensions_Database_DataSet_ITableMetaData $tableMetaData, IConnection $databaseConnection)
    {
        $this->setTableMetaData($tableMetaData);

        $pdoStatement = $databaseConnection->getConnection()->prepare(DataSet::buildTableSelect($tableMetaData, $databaseConnection));
        $pdoStatement->execute();
        $this->data = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
    }
}
