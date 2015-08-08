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
 * A table decorator that allows filtering out table columns from results.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DataSet_TableFilter extends PHPUnit_Extensions_Database_DataSet_AbstractTable
{
    /**
     * The table meta data being decorated.
     * @var PHPUnit_Extensions_Database_DataSet_ITable
     */
    protected $originalTable;

    /**
     * Creates a new table filter using the original table
     *
     * @param $originalTable PHPUnit_Extensions_Database_DataSet_ITable
     * @param $excludeColumns Array @deprecated, use the set* methods instead.
     */
    public function __construct(PHPUnit_Extensions_Database_DataSet_ITable $originalTable, Array $excludeColumns = [])
    {
        $this->originalTable = $originalTable;
        $this->setTableMetaData(new PHPUnit_Extensions_Database_DataSet_TableMetaDataFilter($originalTable->getTableMetaData()));
        $this->addExcludeColumns($excludeColumns);
    }

    /**
     * Returns the number of rows in this table.
     *
     * @return int
     */
    public function getRowCount()
    {
        return $this->originalTable->getRowCount();
    }

    /**
     * Returns the value for the given column on the given row.
     *
     * @param int $row
     * @param int $column
     */
    public function getValue($row, $column)
    {
        if (in_array($column, $this->getTableMetaData()->getColumns())) {
            return $this->originalTable->getValue($row, $column);
        } else {
            throw new InvalidArgumentException("The given row ({$row}) and column ({$column}) do not exist in table {$this->getTableMetaData()->getTableName()}");
        }
    }

    /**
     * Sets the columns to include in the table.
     * @param Array $includeColumns
     */
    public function addIncludeColumns(Array $includeColumns)
    {
        $this->tableMetaData->addIncludeColumns($includeColumns);
    }

    /**
     * Clears the included columns.
     */
    public function clearIncludeColumns()
    {
        $this->tableMetaData->clearIncludeColumns();
    }

    /**
     * Sets the columns to exclude from the table.
     * @param Array $excludeColumns
     */
    public function addExcludeColumns(Array $excludeColumns)
    {
        $this->tableMetaData->addExcludeColumns($excludeColumns);
    }

    /**
     * Clears the included columns.
     */
    public function clearExcludeColumns()
    {
        $this->tableMetaData->clearExcludeColumns();
    }

    /**
     * Checks if a given row is in the table
     *
     * @param array $row
     *
     * @return bool
     */
    public function assertContainsRow(Array $row)
    {
        $this->loadData();

        return parent::assertContainsRow($row);
    }

    /**
     * Loads data into local data table if it's not already loaded
     */
    protected function loadData()
    {
        if ($this->data === NULL) {
            $data = [];
            for($row = 0;$row < $this->originalTable->getRowCount();$row++) {
                $tRow = [];
                foreach($this->getTableMetaData()->getColumns() as $col) {
                    $tRow[$col] = $this->getValue($row, $col);
                }
                $data[$row] = $tRow;
            }
            $this->data   = $data;
        }
    }
}
