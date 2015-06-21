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
 * A XML dataset persistor.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DataSet_Persistors_Xml extends PHPUnit_Extensions_Database_DataSet_Persistors_Abstract
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var resource
     */
    protected $fh;

    /**
     * Sets the filename that this persistor will save to.
     *
     * @param string $filename
     */
    public function setFileName($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Override to save the start of a dataset.
     *
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataset
     */
    protected function startDataSet(PHPUnit_Extensions_Database_DataSet_IDataSet $dataset)
    {
        $this->fh = fopen($this->filename, 'w');

        if ($this->fh === FALSE) {
            throw new PHPUnit_Framework_Exception(
              "Could not open {$this->filename} for writing see " . __CLASS__ . '::setFileName()'
            );
        }

        fwrite($this->fh, "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n");
        fwrite($this->fh, "<dataset>\n");
    }

    /**
     * Override to save the end of a dataset.
     *
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataset
     */
    protected function endDataSet(PHPUnit_Extensions_Database_DataSet_IDataSet $dataset)
    {
        fwrite($this->fh, "</dataset>\n");
    }

    /**
     * Override to save the start of a table.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITable $table
     */
    protected function startTable(PHPUnit_Extensions_Database_DataSet_ITable $table)
    {
        fwrite($this->fh, "\t<table name=\"{$table->getTableMetaData()->getTableName()}\">\n");

        foreach ($table->getTableMetaData()->getColumns() as $columnName) {
            fwrite($this->fh, "\t\t<column>{$columnName}</column>\n");
        }
    }

    /**
     * Override to save the end of a table.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITable $table
     */
    protected function endTable(PHPUnit_Extensions_Database_DataSet_ITable $table)
    {
        fwrite($this->fh, "\t</table>\n");
    }

    /**
     * Override to save a table row.
     *
     * @param array                                      $row
     * @param PHPUnit_Extensions_Database_DataSet_ITable $table
     */
    protected function row(Array $row, PHPUnit_Extensions_Database_DataSet_ITable $table)
    {
        fwrite($this->fh, "\t\t<row>\n");

        foreach ($table->getTableMetaData()->getColumns() as $columnName) {
            if (isset($row[$columnName])) {
                fwrite($this->fh, "\t\t\t<value>" . htmlspecialchars($row[$columnName]) . "</value>\n");
            } else {
                fwrite($this->fh, "\t\t\t<null />\n");
            }
        }

        fwrite($this->fh, "\t\t</row>\n");
    }
}
