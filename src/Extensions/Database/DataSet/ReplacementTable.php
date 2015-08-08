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
 * Allows for replacing arbitrary strings in your data sets with other values.
 *
 * @since      Class available since Release 1.0.0
 * @todo When setTableMetaData() is taken out of the AbstractTable this class should extend AbstractTable.
 */
class PHPUnit_Extensions_Database_DataSet_ReplacementTable implements PHPUnit_Extensions_Database_DataSet_ITable
{
    /**
     * @var PHPUnit_Extensions_Database_DataSet_ITable
     */
    protected $table;

    /**
     * @var array
     */
    protected $fullReplacements;

    /**
     * @var array
     */
    protected $subStrReplacements;

    /**
     * Creates a new replacement table
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITable $table
     * @param array                                      $fullReplacements
     * @param array                                      $subStrReplacements
     */
    public function __construct(PHPUnit_Extensions_Database_DataSet_ITable $table, Array $fullReplacements = [], Array $subStrReplacements = [])
    {
        $this->table              = $table;
        $this->fullReplacements   = $fullReplacements;
        $this->subStrReplacements = $subStrReplacements;
    }

    /**
     * Adds a new full replacement
     *
     * Full replacements will only replace values if the FULL value is a match
     *
     * @param string $value
     * @param string $replacement
     */
    public function addFullReplacement($value, $replacement)
    {
        $this->fullReplacements[$value] = $replacement;
    }

    /**
     * Adds a new substr replacement
     *
     * Substr replacements will replace all occurances of the substr in every column
     *
     * @param string $value
     * @param string $replacement
     */
    public function addSubStrReplacement($value, $replacement)
    {
        $this->subStrReplacements[$value] = $replacement;
    }

    /**
     * Returns the table's meta data.
     *
     * @return PHPUnit_Extensions_Database_DataSet_ITableMetaData
     */
    public function getTableMetaData()
    {
        return $this->table->getTableMetaData();
    }

    /**
     * Returns the number of rows in this table.
     *
     * @return int
     */
    public function getRowCount()
    {
        return $this->table->getRowCount();
    }

    /**
     * Returns the value for the given column on the given row.
     *
     * @param int $row
     * @param int $column
     */
    public function getValue($row, $column)
    {
        return $this->getReplacedValue($this->table->getValue($row, $column));
    }

    /**
     * Returns the an associative array keyed by columns for the given row.
     *
     * @param  int   $row
     * @return array
     */
    public function getRow($row)
    {
        $row = $this->table->getRow($row);

        return array_map([$this, 'getReplacedValue'], $row);
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
                        return FALSE;
                    }
                } elseif ($thisValue !== $otherValue) {
                    return FALSE;
                }
            }
        }

        return TRUE;
    }

    public function __toString()
    {
        $columns = $this->getTableMetaData()->getColumns();

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
                $values[] = $this->getValue($i, $columnName);
            }

            $tableString .= $this->rowToString($values);
            $tableString .= $lineSeperator;
        }

        return "\n" . $tableString . "\n";
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

    protected function getReplacedValue($value)
    {
        if (is_scalar($value) && array_key_exists((string) $value, $this->fullReplacements)) {
            return $this->fullReplacements[$value];
        }

        else if (count($this->subStrReplacements) && isset($value)) {
            return str_replace(array_keys($this->subStrReplacements), array_values($this->subStrReplacements), $value);
        }

        else {
            return $value;
        }
    }
}
