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
 * The default table iterator
 */
class DefaultTableIterator implements ITableIterator
{
    /**
     * An array of tables in the iterator.
     *
     * @var array
     */
    protected $tables;

    /**
     * If this property is true then the tables will be iterated in reverse
     * order.
     *
     * @var bool
     */
    protected $reverse;

    /**
     * Creates a new default table iterator object.
     *
     * @param array $tables
     * @param bool  $reverse
     */
    public function __construct(array $tables, $reverse = false)
    {
        $this->tables  = $tables;
        $this->reverse = $reverse;

        $this->rewind();
    }

    /**
     * Returns the current table.
     *
     * @return ITable
     */
    public function getTable()
    {
        return $this->current();
    }

    /**
     * Returns the current table's meta data.
     *
     * @return ITableMetadata
     */
    public function getTableMetaData()
    {
        return $this->current()->getTableMetaData();
    }

    /**
     * Returns the current table.
     *
     * @return ITable
     */
    public function current()
    {
        return \current($this->tables);
    }

    /**
     * Returns the name of the current table.
     *
     * @return string
     */
    public function key()
    {
        return $this->current()->getTableMetaData()->getTableName();
    }

    /**
     * advances to the next element.
     */
    public function next()
    {
        if ($this->reverse) {
            \prev($this->tables);
        } else {
            \next($this->tables);
        }
    }

    /**
     * Rewinds to the first element
     */
    public function rewind()
    {
        if ($this->reverse) {
            \end($this->tables);
        } else {
            \reset($this->tables);
        }
    }

    /**
     * Returns true if the current index is valid
     *
     * @return bool
     */
    public function valid()
    {
        return ($this->current() !== false);
    }
}
