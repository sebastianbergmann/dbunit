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
 * Provides the functionality to represent a database result set as a DBUnit
 * table.
 *
 * @deprecated The PHPUnit_Extension_Database_DataSet_QueryTable should be used instead
 * @see        PHPUnit_Extension_Database_DataSet_QueryTable
 * @see        PHPUnit_Extension_Database_DataSet_QueryDataSet
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DB_ResultSetTable extends PHPUnit_Extensions_Database_DataSet_AbstractTable
{
    /**
     * Creates a new result set table.
     *
     * @param string       $tableName
     * @param PDOStatement $pdoStatement
     */
    public function __construct($tableName, PDOStatement $pdoStatement)
    {
        $this->data = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);

        if (count($this->data)) {
            $columns = array_keys($this->data[0]);
        } else {
            $columns = [];
        }

        $this->setTableMetaData(new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData($tableName, $columns));
    }
}
