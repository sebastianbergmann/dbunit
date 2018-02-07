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
 * Provides default table functionality.
 */
class DefaultTable extends AbstractTable
{
    /**
     * Creates a new table object using the given $tableMetaData
     *
     * @param ITableMetadata $tableMetaData
     */
    public function __construct(ITableMetadata $tableMetaData)
    {
        $this->setTableMetaData($tableMetaData);
        $this->data = [];
    }

    /**
     * Adds a row to the table with optional values.
     *
     * @param array $values
     */
    public function addRow($values = []): void
    {
        $this->data[] = \array_replace(
            \array_fill_keys($this->getTableMetaData()->getColumns(), null),
            $values
        );
    }

    /**
     * Adds the rows in the passed table to the current table.
     *
     * @param ITable $table
     */
    public function addTableRows(ITable $table): void
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
    public function setValue($row, $column, $value): void
    {
        if (isset($this->data[$row])) {
            $this->data[$row][$column] = $value;
        } else {
            throw new InvalidArgumentException('The row given does not exist.');
        }
    }
}
