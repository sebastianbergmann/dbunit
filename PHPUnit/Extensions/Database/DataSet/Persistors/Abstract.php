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
 * An abstract implementation of a dataset persistor.
 *
 * @package    DbUnit
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  2010-2014 Mike Lively <m@digitalsandwich.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 1.0.0
 */
abstract class PHPUnit_Extensions_Database_DataSet_Persistors_Abstract implements PHPUnit_Extensions_Database_DataSet_IPersistable
{
    public function write(PHPUnit_Extensions_Database_DataSet_IDataSet $dataset)
    {
        $this->saveDataSet($dataset);
    }

    /**
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataset
     */
    protected function saveDataSet(PHPUnit_Extensions_Database_DataSet_IDataSet $dataset)
    {
        $this->startDataSet($dataset);

        foreach ($dataset as $table) {
            $this->saveTable($table);
        }

        $this->endDataSet($dataset);
    }

    /**
     * @param PHPUnit_Extensions_Database_DataSet_ITable $table
     */
    protected function saveTable(PHPUnit_Extensions_Database_DataSet_ITable $table)
    {
        $rowCount = $table->getRowCount();
        $this->startTable($table);

        for ($i = 0; $i < $rowCount; $i++) {
            $this->row($table->getRow($i), $table);
        }

        $this->endTable($table);
    }

    /**
     * Override to save the start of a dataset.
     *
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataset
     */
    abstract protected function startDataSet(PHPUnit_Extensions_Database_DataSet_IDataSet $dataset);

    /**
     * Override to save the end of a dataset.
     *
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataset
     */
    abstract protected function endDataSet(PHPUnit_Extensions_Database_DataSet_IDataSet $dataset);

    /**
     * Override to save the start of a table.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITable $table
     */
    abstract protected function startTable(PHPUnit_Extensions_Database_DataSet_ITable $table);

    /**
     * Override to save the end of a table.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITable $table
     */
    abstract protected function endTable(PHPUnit_Extensions_Database_DataSet_ITable $table);

    /**
     * Override to save a table row.
     *
     * @param array $row
     * @param PHPUnit_Extensions_Database_DataSet_ITable $table
     */
    abstract protected function row(Array $row, PHPUnit_Extensions_Database_DataSet_ITable $table);
}
