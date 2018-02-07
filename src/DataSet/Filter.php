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

/**
 * A dataset decorator that allows filtering out tables and table columns from
 * results.
 */
class Filter extends AbstractDataSet
{
    /**
     * The dataset being decorated.
     *
     * @var IDataSet
     */
    protected $originalDataSet;

    /**
     * The tables to exclude from the data set.
     *
     * @var array
     */
    protected $excludeTables = [];

    /**
     * The tables to exclude from the data set.
     *
     * @var array
     */
    protected $includeTables = [];

    /**
     * The columns to exclude from the data set.
     *
     * @var array
     */
    protected $excludeColumns = [];

    /**
     * The columns to exclude from the data set.
     *
     * @var array
     */
    protected $includeColumns = [];

    /**
     * Creates a new filtered data set.
     *
     * The $exclude tables should be an associative array using table names as
     * the key and an array of column names to exclude for the value. If you
     * would like to exclude a full table set the value of the table's entry
     * to the special string '*'.
     *
     * @param IDataSet $originalDataSet
     * @param array    $excludeTables   @deprecated use set* methods instead
     */
    public function __construct(IDataSet $originalDataSet, array $excludeTables = [])
    {
        $this->originalDataSet = $originalDataSet;

        $tables = [];
        foreach ($excludeTables as $tableName => $values) {
            if (\is_array($values)) {
                $this->setExcludeColumnsForTable($tableName, $values);
            } elseif ($values == '*') {
                $tables[] = $tableName;
            } else {
                $this->setExcludeColumnsForTable($tableName, (array) $values);
            }
        }

        $this->addExcludeTables($tables);
    }

    /**
     * Adds tables to be included in the data set.
     *
     * @param array $tables
     */
    public function addIncludeTables(array $tables): void
    {
        $this->includeTables = \array_unique(\array_merge($this->includeTables, $tables));
    }

    /**
     * Adds tables to be included in the data set.
     *
     * @param array $tables
     */
    public function addExcludeTables(array $tables): void
    {
        $this->excludeTables = \array_unique(\array_merge($this->excludeTables, $tables));
    }

    /**
     * Adds columns to include in the data set for the given table.
     *
     * @param string $table
     * @param array  $columns
     */
    public function setIncludeColumnsForTable($table, array $columns): void
    {
        $this->includeColumns[$table] = $columns;
    }

    /**
     * Adds columns to include in the data set for the given table.
     *
     * @param string $table
     * @param array  $columns
     */
    public function setExcludeColumnsForTable($table, array $columns): void
    {
        $this->excludeColumns[$table] = $columns;
    }

    /**
     * Creates an iterator over the tables in the data set. If $reverse is
     * true a reverse iterator will be returned.
     *
     * @param bool $reverse
     *
     * @return ITableIterator
     */
    protected function createIterator($reverse = false)
    {
        $original_tables = $this->originalDataSet->getIterator($reverse);
        $new_tables      = [];

        foreach ($original_tables as $table) {
            /* @var $table ITable */
            $tableName = $table->getTableMetaData()->getTableName();

            if ((!\in_array($tableName, $this->includeTables) && !empty($this->includeTables)) ||
                \in_array($tableName, $this->excludeTables)
            ) {
                continue;
            }
            if (!empty($this->excludeColumns[$tableName]) || !empty($this->includeColumns[$tableName])) {
                $new_table = new TableFilter($table);

                if (!empty($this->includeColumns[$tableName])) {
                    $new_table->addIncludeColumns($this->includeColumns[$tableName]);
                }

                if (!empty($this->excludeColumns[$tableName])) {
                    $new_table->addExcludeColumns($this->excludeColumns[$tableName]);
                }

                $new_tables[] = $new_table;
            } else {
                $new_tables[] = $table;
            }
        }

        return new DefaultTableIterator($new_tables);
    }
}
