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
use SimpleXMLElement;

/**
 * Provides a basic functionality for dbunit tables
 */
class AbstractTable implements ITable
{
    /**
     * @var ITableMetadata
     */
    protected $tableMetaData;

    /**
     * A 2-dimensional array containing the data for this table.
     *
     * @var array
     */
    protected $data;

    /**
     * @var ITable|null
     */
    private $other;

    /**
     * Sets the metadata for this table.
     *
     * @param ITableMetadata $tableMetaData
     *
     * @deprecated
     */
    protected function setTableMetaData(ITableMetadata $tableMetaData)
    {
        $this->tableMetaData = $tableMetaData;
    }

    /**
     * Returns the table's meta data.
     *
     * @return ITableMetadata
     */
    public function getTableMetaData()
    {
        return $this->tableMetaData;
    }

    /**
     * Returns the number of rows in this table.
     *
     * @return int
     */
    public function getRowCount()
    {
        return \count($this->data);
    }

    /**
     * Returns the value for the given column on the given row.
     *
     * @param int $row
     * @param int $column
     *
     * @todo reorganize this function to throw the exception first.
     */
    public function getValue($row, $column)
    {
        if (isset($this->data[$row][$column])) {
            $value = $this->data[$row][$column];

            return ($value instanceof SimpleXMLElement) ? (string) $value : $value;
        } else {
            if (!\in_array($column, $this->getTableMetaData()->getColumns()) || $this->getRowCount() <= $row) {
                throw new InvalidArgumentException("The given row ({$row}) and column ({$column}) do not exist in table {$this->getTableMetaData()->getTableName()}");
            } else {
                return;
            }
        }
    }

    /**
     * Returns the an associative array keyed by columns for the given row.
     *
     * @param int $row
     *
     * @return array
     */
    public function getRow($row)
    {
        if (isset($this->data[$row])) {
            return $this->data[$row];
        } else {
            if ($this->getRowCount() <= $row) {
                throw new InvalidArgumentException("The given row ({$row}) does not exist in table {$this->getTableMetaData()->getTableName()}");
            } else {
                return;
            }
        }
    }

    /**
     * Asserts that the given table matches this table.
     *
     * @param ITable $other
     */
    public function matches(ITable $other)
    {
        $thisMetaData  = $this->getTableMetaData();
        $otherMetaData = $other->getTableMetaData();

        if (!$thisMetaData->matches($otherMetaData) ||
            $this->getRowCount() != $other->getRowCount()
        ) {
            return false;
        }

        $columns  = $thisMetaData->getColumns();
        $rowCount = $this->getRowCount();

        for ($i = 0; $i < $rowCount; $i++) {
            foreach ($columns as $columnName) {
                $thisValue  = $this->getValue($i, $columnName);
                $otherValue = $other->getValue($i, $columnName);
                if (\is_numeric($thisValue) && \is_numeric($otherValue)) {
                    if ($thisValue != $otherValue) {
                        $this->other = $other;

                        return false;
                    }
                } elseif ($thisValue !== $otherValue) {
                    $this->other = $other;

                    return false;
                }
            }
        }

        return true;
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
        return \in_array($row, $this->data);
    }

    public function __toString()
    {
        $columns = $this->getTableMetaData()->getColumns();
        $count   = \count($columns);

        // if count less than 0 (when table is empty), then set count to 1
        $count         = $count >= 1 ? $count : 1;
        $lineSeparator = \str_repeat('+----------------------', $count) . "+\n";
        $lineLength    = \strlen($lineSeparator) - 1;

        $tableString = $lineSeparator;
        $tblName     = $this->getTableMetaData()->getTableName();
        $tableString .= '| ' . \str_pad($tblName, $lineLength - 4, ' ',
                STR_PAD_RIGHT) . " |\n";
        $tableString .= $lineSeparator;
        $rows = $this->rowToString($columns);
        $tableString .= !empty($rows) ? $rows . $lineSeparator : '';

        $rowCount = $this->getRowCount();

        for ($i = 0; $i < $rowCount; $i++) {
            $values = [];

            foreach ($columns as $columnName) {
                if ($this->other) {
                    try {
                        if ($this->getValue($i, $columnName) != $this->other->getValue($i, $columnName)) {
                            $values[] = \sprintf(
                                '%s != actual %s',
                                \var_export($this->getValue($i, $columnName), true),
                                \var_export($this->other->getValue($i, $columnName), true)
                            );
                        } else {
                            $values[] = $this->getValue($i, $columnName);
                        }
                    } catch (\InvalidArgumentException $ex) {
                        $values[] = $this->getValue($i, $columnName) . ': no row';
                    }
                } else {
                    $values[] = $this->getValue($i, $columnName);
                }
            }

            $tableString .= $this->rowToString($values) . $lineSeparator;
        }

        return ($this->other ? '(table diff enabled)' : '') . "\n" . $tableString . "\n";
    }

    protected function rowToString(array $row)
    {
        $rowString = '';

        foreach ($row as $value) {
            if (null === $value) {
                $value = 'NULL';
            }

            $value_str = \mb_substr($value, 0, 20);

            // make str_pad act in multibyte manner
            $correction = \strlen($value_str) - \mb_strlen($value_str);
            $rowString .= '| ' . \str_pad($value_str, 20 + $correction, ' ', STR_PAD_BOTH) . ' ';
        }

        /** @see https://github.com/sebastianbergmann/dbunit/issues/195 */
        $rowString = !empty($row) ? $rowString . "|\n" : '';

        return $rowString;
    }
}
