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
 * Creates Composite Datasets
 *
 * Allows for creating datasets from multiple sources (csv, query, xml, etc.)
 */
class CompositeDataSet extends AbstractDataSet
{
    protected $motherDataSet;

    /**
     * Creates a new Composite dataset
     *
     * You can pass in any data set that implements PHPUnit_Extensions_Database_DataSet_IDataSet
     *
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     */
    public function __construct(array $dataSets = [])
    {
        $this->motherDataSet = new DefaultDataSet();

        foreach ($dataSets as $dataSet) {
            $this->addDataSet($dataSet);
        }
    }

    /**
     * Adds a new data set to the composite.
     *
     * The dataset may not define tables that already exist in the composite.
     *
     * @param IDataSet $dataSet
     */
    public function addDataSet(IDataSet $dataSet): void
    {
        foreach ($dataSet->getTableNames() as $tableName) {
            if (!\in_array($tableName, $this->getTableNames())) {
                $this->motherDataSet->addTable($dataSet->getTable($tableName));
            } else {
                $other = $dataSet->getTable($tableName);
                $table = $this->getTable($tableName);

                if (!$table->getTableMetaData()->matches($other->getTableMetaData())) {
                    throw new InvalidArgumentException("There is already a table named $tableName with different table definition");
                }

                $table->addTableRows($dataSet->getTable($tableName));
            }
        }
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
        if ($reverse) {
            return $this->motherDataSet->getReverseIterator();
        }

        return $this->motherDataSet->getIterator();
    }
}
