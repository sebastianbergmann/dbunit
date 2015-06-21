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
 * This class loads a table metadata object with database metadata.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DB_TableMetaData extends PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData
{
    public function __construct($tableName, PHPUnit_Extensions_Database_DB_IMetaData $databaseMetaData)
    {
        $this->tableName   = $tableName;
        $this->columns     = $databaseMetaData->getTableColumns($tableName);
        $this->primaryKeys = $databaseMetaData->getTablePrimaryKeys($tableName);
    }
}
