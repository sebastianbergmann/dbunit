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
 * Provides default table functionality.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DataSet_DefaultTable extends PHPUnit_Extensions_Database_DataSet_AbstractTable
{
    /**
     * Creates a new table object using the given $tableMetaData
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITableMetaData $tableMetaData
     */
    public function __construct(PHPUnit_Extensions_Database_DataSet_ITableMetaData $tableMetaData)
    {
        $this->setTableMetaData($tableMetaData);
        $this->data = [];
    }

    /**
     * Adds a row to the table with optional values.
     *
     * @param array $values
     */
    public function addRow($values = [])
    {
        $this->data[] = array_replace(
          array_fill_keys($this->getTableMetaData()->getColumns(), NULL),
          $values
        );
    }

    /**
     * Adds the rows in the passed table to the current table.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITable $table
     */
    public function addTableRows(PHPUnit_Extensions_Database_DataSet_ITable $table)
    {
        $tableColumns = $this->getTableMetaData()->getColumns();
        $rowCount     = $table->getRowCount();

        for ($i = 0; $i < $rowCount; $i++) {
            $newRow = [];
            foreach ($tableColumns as $columnName) {
                $newRow[$columnName] = $table->getValue($i, $columnName);
            }
            $this->addRow($newRow);
        }
    }

    /**
     * Sets the specified column of the specied row to the specified value.
     *
     * @param int    $row
     * @param string $column
     * @param mixed  $value
     */
    public function setValue($row, $column, $value)
    {
        if (isset($this->data[$row])) {
            $this->data[$row][$column] = $value;
        } else {
            throw new InvalidArgumentException('The row given does not exist.');
        }
    }
}
