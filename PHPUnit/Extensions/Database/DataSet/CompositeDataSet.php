<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2002-2014, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    DbUnit
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  2002-2014 Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 1.0.0
 */

/**
 * Creates Composite Datasets
 *
 * Allows for creating datasets from multiple sources (csv, query, xml, etc.)
 *
 * @package    DbUnit
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  2010-2014 Mike Lively <m@digitalsandwich.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
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
    public function __construct(Array $dataSets = array())
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
     * @param bool $reverse
     * @return PHPUnit_Extensions_Database_DataSet_ITableIterator
     */
    protected function createIterator($reverse = FALSE)
    {
        return $this->motherDataset->getIterator($reverse);
    }
}
