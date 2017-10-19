<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\Database\Metadata;

use PHPUnit\DbUnit\DataSet\DefaultTableMetadata;

/**
 * This class loads a table metadata object with database metadata.
 */
class Table extends DefaultTableMetadata
{
    public function __construct($tableName, Metadata $databaseMetaData)
    {
        $this->tableName   = $tableName;
        $this->columns     = $databaseMetaData->getTableColumns($tableName);
        $this->primaryKeys = $databaseMetaData->getTablePrimaryKeys($tableName);
    }
}
