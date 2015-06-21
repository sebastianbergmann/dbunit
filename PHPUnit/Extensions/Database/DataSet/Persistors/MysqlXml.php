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
 * A MySQL XML dataset persistor.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DataSet_Persistors_MysqlXml extends PHPUnit_Extensions_Database_DataSet_Persistors_Abstract
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $database;

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
     * Sets the name of the database.
     *
     * @param string $database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
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

        fwrite($this->fh, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        fwrite($this->fh, '<mysqldump xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . "\n");
        fwrite($this->fh, '<database name="' . $this->database . '">' . "\n");
    }

    /**
     * Override to save the end of a dataset.
     *
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataset
     */
    protected function endDataSet(PHPUnit_Extensions_Database_DataSet_IDataSet $dataset)
    {
        fwrite($this->fh, '</database>' . "\n");
        fwrite($this->fh, '</mysqldump>' . "\n");
    }

    /**
     * Override to save the start of a table.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITable $table
     */
    protected function startTable(PHPUnit_Extensions_Database_DataSet_ITable $table)
    {
        fwrite($this->fh, "\t" . '<table_data name="' . $table->getTableMetaData()->getTableName() . '">' . "\n");
    }

    /**
     * Override to save the end of a table.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITable $table
     */
    protected function endTable(PHPUnit_Extensions_Database_DataSet_ITable $table)
    {
        fwrite($this->fh, "\t" . '</table_data>' . "\n");
    }

    /**
     * Override to save a table row.
     *
     * @param array                                      $row
     * @param PHPUnit_Extensions_Database_DataSet_ITable $table
     */
    protected function row(Array $row, PHPUnit_Extensions_Database_DataSet_ITable $table)
    {
        fwrite($this->fh, "\t" . '<row>' . "\n");

        foreach ($table->getTableMetaData()->getColumns() as $columnName) {
            fwrite($this->fh, "\t\t" . '<field name="' . $columnName . '"');
            if (isset($row[$columnName])) {
                fwrite($this->fh, '>' . htmlspecialchars($row[$columnName]) . '</field>' . "\n");
            } else {
                fwrite($this->fh, ' xsi:nil="true" />' . "\n");
            }
        }

        fwrite($this->fh, "\t" . '</row>' . "\n");
    }
}
