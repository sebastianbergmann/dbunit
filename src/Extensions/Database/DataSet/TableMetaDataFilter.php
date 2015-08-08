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
 * A TableMetaData decorator that allows filtering columns from another
 * metaData object.
 *
 * The if a whitelist (include) filter is specified, then only those columns
 * will be included.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DataSet_TableMetaDataFilter extends PHPUnit_Extensions_Database_DataSet_AbstractTableMetaData
{
    /**
     * The table meta data being decorated.
     * @var PHPUnit_Extensions_Database_DataSet_ITableMetaData
     */
    protected $originalMetaData;

    /**
     * The columns to exclude from the meta data.
     * @var Array
     */
    protected $excludeColumns = [];

    /**
     * The columns to include from the meta data.
     * @var Array
     */
    protected $includeColumns = [];

    /**
     * Creates a new filtered table meta data object filtering out
     * $excludeColumns.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITableMetaData $originalMetaData
     * @param array                                              $excludeColumns   - Deprecated. Use the set* methods instead.
     */
    public function __construct(PHPUnit_Extensions_Database_DataSet_ITableMetaData $originalMetaData, Array $excludeColumns = [])
    {
        $this->originalMetaData = $originalMetaData;
        $this->addExcludeColumns($excludeColumns);
    }

    /**
     * Returns the names of the columns in the table.
     *
     * @return array
     */
    public function getColumns()
    {
        if (!empty($this->includeColumns)) {
            return array_values(array_intersect($this->originalMetaData->getColumns(), $this->includeColumns));
        }
        elseif (!empty($this->excludeColumns)) {
            return array_values(array_diff($this->originalMetaData->getColumns(), $this->excludeColumns));
        }
        else {
            return $this->originalMetaData->getColumns();
        }
    }

    /**
     * Returns the names of the primary key columns in the table.
     *
     * @return array
     */
    public function getPrimaryKeys()
    {
        return $this->originalMetaData->getPrimaryKeys();
    }

    /**
     * Returns the name of the table.
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->originalMetaData->getTableName();
    }

    /**
     * Sets the columns to include in the table.
     * @param Array $includeColumns
     */
    public function addIncludeColumns(Array $includeColumns)
    {
        $this->includeColumns = array_unique(array_merge($this->includeColumns, $includeColumns));
    }

    /**
     * Clears the included columns.
     */
    public function clearIncludeColumns()
    {
        $this->includeColumns = [];
    }

    /**
     * Sets the columns to exclude from the table.
     * @param Array $excludeColumns
     */
    public function addExcludeColumns(Array $excludeColumns)
    {
        $this->excludeColumns = array_unique(array_merge($this->excludeColumns, $excludeColumns));
    }

    /**
     * Clears the excluded columns.
     */
    public function clearExcludeColumns()
    {
        $this->excludeColumns = [];
    }
}
