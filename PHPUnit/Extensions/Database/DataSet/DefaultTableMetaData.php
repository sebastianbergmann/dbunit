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
 * The default implementation of table meta data
 *
 * @package    DbUnit
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  2010-2014 Mike Lively <m@digitalsandwich.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData extends PHPUnit_Extensions_Database_DataSet_AbstractTableMetaData
{

    /**
     * Creates a new default table meta data object.
     *
     * @param string $tableName
     * @param array $columns
     * @param array $primaryKeys
     */
    public function __construct($tableName, Array $columns, Array $primaryKeys = array())
    {
        $this->tableName   = $tableName;
        $this->columns     = $columns;
        $this->primaryKeys = array();

        foreach ($primaryKeys as $columnName) {
            if (!in_array($columnName, $this->columns)) {
                throw new InvalidArgumentException("Primary key column passed that is not in the column list.");
            } else {
                $this->primaryKeys[] = $columnName;
            }
        }
    }
}
