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
 * A TableMetaData decorator that allows filtering columns from another
 * metaData object.
 *
 * The if a whitelist (include) filter is specified, then only those columns
 * will be included.
 */
class TableMetadataFilter extends AbstractTableMetadata
{
    /**
     * The table meta data being decorated.
     *
     * @var ITableMetadata
     */
    protected $originalMetaData;

    /**
     * The columns to exclude from the meta data.
     *
     * @var array
     */
    protected $excludeColumns = [];

    /**
     * The columns to include from the meta data.
     *
     * @var array
     */
    protected $includeColumns = [];

    /**
     * Creates a new filtered table meta data object filtering out
     * $excludeColumns.
     *
     * @param ITableMetadata $originalMetaData
     * @param array          $excludeColumns   - Deprecated. Use the set* methods instead.
     */
    public function __construct(ITableMetadata $originalMetaData, array $excludeColumns = [])
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
            return \array_values(\array_intersect($this->originalMetaData->getColumns(), $this->includeColumns));
        } elseif (!empty($this->excludeColumns)) {
            return \array_values(\array_diff($this->originalMetaData->getColumns(), $this->excludeColumns));
        } else {
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
     *
     * @param array $includeColumns
     */
    public function addIncludeColumns(array $includeColumns)
    {
        $this->includeColumns = \array_unique(\array_merge($this->includeColumns, $includeColumns));
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
     *
     * @param array $excludeColumns
     */
    public function addExcludeColumns(array $excludeColumns)
    {
        $this->excludeColumns = \array_unique(\array_merge($this->excludeColumns, $excludeColumns));
    }

    /**
     * Clears the excluded columns.
     */
    public function clearExcludeColumns()
    {
        $this->excludeColumns = [];
    }
}
