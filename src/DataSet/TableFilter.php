<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\DataSet;

use PHPUnit\DbUnit\InvalidArgumentException;

/**
 * A table decorator that allows filtering out table columns from results.
 */
class TableFilter extends AbstractTable
{
    /**
     * The table meta data being decorated.
     *
     * @var ITable
     */
    protected $originalTable;

    /**
     * Creates a new table filter using the original table
     *
     * @param $originalTable ITable
     * @param $excludeColumns array @deprecated, use the set* methods instead
     */
    public function __construct(ITable $originalTable, array $excludeColumns = [])
    {
        $this->originalTable = $originalTable;
        $this->setTableMetaData(new TableMetadataFilter($originalTable->getTableMetaData()));
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
        if (\in_array($column, $this->getTableMetaData()->getColumns())) {
            return $this->originalTable->getValue($row, $column);
        }

        throw new InvalidArgumentException("The given row ({$row}) and column ({$column}) do not exist in table {$this->getTableMetaData()->getTableName()}");
    }

    /**
     * Sets the columns to include in the table.
     *
     * @param array $includeColumns
     */
    public function addIncludeColumns(array $includeColumns): void
    {
        $this->tableMetaData->addIncludeColumns($includeColumns);
    }

    /**
     * Clears the included columns.
     */
    public function clearIncludeColumns(): void
    {
        $this->tableMetaData->clearIncludeColumns();
    }

    /**
     * Sets the columns to exclude from the table.
     *
     * @param array $excludeColumns
     */
    public function addExcludeColumns(array $excludeColumns): void
    {
        $this->tableMetaData->addExcludeColumns($excludeColumns);
    }

    /**
     * Clears the included columns.
     */
    public function clearExcludeColumns(): void
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
    public function assertContainsRow(array $row)
    {
        $this->loadData();

        return parent::assertContainsRow($row);
    }

    /**
     * Loads data into local data table if it's not already loaded
     */
    protected function loadData(): void
    {
        if ($this->data === null) {
            $data = [];
            for ($row = 0; $row < $this->originalTable->getRowCount(); $row++) {
                $tRow = [];
                foreach ($this->getTableMetaData()->getColumns() as $col) {
                    $tRow[$col] = $this->getValue($row, $col);
                }
                $data[$row] = $tRow;
            }
            $this->data = $data;
        }
    }
}
