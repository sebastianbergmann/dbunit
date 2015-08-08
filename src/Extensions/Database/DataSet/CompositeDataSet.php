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
 * Creates Composite Datasets
 *
 * Allows for creating datasets from multiple sources (csv, query, xml, etc.)
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DataSet_CompositeDataSet extends PHPUnit_Extensions_Database_DataSet_AbstractDataSet
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
    public function __construct(Array $dataSets = [])
    {
        $this->motherDataset = new PHPUnit_Extensions_Database_DataSet_DefaultDataSet();

        foreach ($dataSets as $dataSet)
        {
            $this->addDataSet($dataSet);
        }
    }

    /**
     * Adds a new data set to the composite.
     *
     * The dataset may not define tables that already exist in the composite.
     *
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet
     */
    public function addDataSet(PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
    {
        foreach ($dataSet->getTableNames() as $tableName)
        {
            if (!in_array($tableName, $this->getTableNames())) {
                $this->motherDataset->addTable($dataSet->getTable($tableName));
            } else {
                $other = $dataSet->getTable($tableName);
                $table = $this->getTable($tableName);

                if (!$table->getTableMetaData()->matches($other->getTableMetaData()))
                {
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
     * @param  bool                                               $reverse
     * @return PHPUnit_Extensions_Database_DataSet_ITableIterator
     */
    protected function createIterator($reverse = FALSE)
    {
        if ($reverse) {
            return $this->motherDataset->getReverseIterator();
        } else {
            return $this->motherDataset->getIterator();
        }
    }
}
