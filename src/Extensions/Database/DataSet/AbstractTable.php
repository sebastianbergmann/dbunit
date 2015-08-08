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
 * Provides a basic functionality for dbunit tables
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DataSet_AbstractTable implements PHPUnit_Extensions_Database_DataSet_ITable
{
    /**
     * @var PHPUnit_Extensions_Database_DataSet_ITableMetaData
     */
    protected $tableMetaData;

    /**
     * A 2-dimensional array containing the data for this table.
     *
     * @var array
     */
    protected $data;

    /**
     * @var PHPUnit_Extensions_Database_DataSet_ITable|null
     */
    private $other;

    /**
     * Sets the metadata for this table.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITableMetaData $tableMetaData
     * @deprecated
     */
    protected function setTableMetaData(PHPUnit_Extensions_Database_DataSet_ITableMetaData $tableMetaData)
    {
        $this->tableMetaData = $tableMetaData;
    }

    /**
     * Returns the table's meta data.
     *
     * @return PHPUnit_Extensions_Database_DataSet_ITableMetaData
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
        return count($this->data);
    }

    /**
     * Returns the value for the given column on the given row.
     *
     * @param int $row
     * @param int $column
     * @todo reorganize this function to throw the exception first.
     */
    public function getValue($row, $column)
    {
        if (isset($this->data[$row][$column])) {
            $value = $this->data[$row][$column];

            return ($value instanceof SimpleXMLElement) ? (string) $value : $value;
        } else {
            if (!in_array($column, $this->getTableMetaData()->getColumns()) || $this->getRowCount() <= $row) {
                throw new InvalidArgumentException("The given row ({$row}) and column ({$column}) do not exist in table {$this->getTableMetaData()->getTableName()}");
            } else {
                return NULL;
            }
        }
    }

    /**
     * Returns the an associative array keyed by columns for the given row.
     *
     * @param  int   $row
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
                return NULL;
            }
        }
    }

    /**
     * Asserts that the given table matches this table.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITable $other
     */
    public function matches(PHPUnit_Extensions_Database_DataSet_ITable $other)
    {
        $thisMetaData  = $this->getTableMetaData();
        $otherMetaData = $other->getTableMetaData();

        if (!$thisMetaData->matches($otherMetaData) ||
            $this->getRowCount() != $other->getRowCount()) {
            return FALSE;
        }

        $columns  = $thisMetaData->getColumns();
        $rowCount = $this->getRowCount();

        for ($i = 0; $i < $rowCount; $i++) {
            foreach ($columns as $columnName) {
                $thisValue  = $this->getValue($i, $columnName);
                $otherValue = $other->getValue($i, $columnName);
                if (is_numeric($thisValue) && is_numeric($otherValue)) {
                    if ($thisValue != $otherValue) {
                        $this->other = $other;

                        return FALSE;
                    }
                } elseif ($thisValue !== $otherValue) {
                    $this->other = $other;

                    return FALSE;
                }
            }
        }

        return TRUE;
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
        return in_array($row, $this->data);
    }

    public function __toString()
    {
        $columns       = $this->getTableMetaData()->getColumns();
        $lineSeperator = str_repeat('+----------------------', count($columns)) . "+\n";
        $lineLength    = strlen($lineSeperator) - 1;

        $tableString  = $lineSeperator;
        $tableString .= '| ' . str_pad($this->getTableMetaData()->getTableName(), $lineLength - 4, ' ', STR_PAD_RIGHT) . " |\n";
        $tableString .= $lineSeperator;
        $tableString .= $this->rowToString($columns);
        $tableString .= $lineSeperator;

        $rowCount = $this->getRowCount();

        for ($i = 0; $i < $rowCount; $i++) {
            $values = [];

            foreach ($columns as $columnName) {
                if ($this->other) {
                    try {
                        if ($this->getValue($i, $columnName) != $this->other->getValue($i, $columnName)) {
                            $values[] = sprintf(
                                '%s != actual %s',
                                var_export($this->getValue($i, $columnName), TRUE),
                                var_export($this->other->getValue($i, $columnName), TRUE)
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

            $tableString .= $this->rowToString($values) . $lineSeperator;
        }

        return ($this->other ? '(table diff enabled)' : '') . "\n" . $tableString . "\n";
    }

    protected function rowToString(Array $row)
    {
        $rowString = '';

        foreach ($row as $value) {
            if (is_null($value)) {
                $value = 'NULL';
            }

            $rowString .= '| ' . str_pad(substr($value, 0, 20), 20, ' ', STR_PAD_BOTH) . ' ';
        }

        return $rowString . "|\n";
    }
}
