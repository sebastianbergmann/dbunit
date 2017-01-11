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
 * Creates CsvDataSets.
 *
 * You can incrementally add CSV files as tables to your datasets
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DataSet_CsvDataSet extends PHPUnit_Extensions_Database_DataSet_AbstractDataSet
{
    /**
     * @var array
     */
    protected $tables = [];

    /**
     * @var string
     */
    protected $delimiter = ',';

    /**
     * @var string
     */
    protected $enclosure = '"';

    /**
     * @var string
     */
    protected $escape = '"';

    /**
     * Creates a new CSV dataset
     *
     * You can pass in the parameters for how csv files will be read.
     *
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     */
    public function __construct($delimiter = ',', $enclosure = '"', $escape = '"')
    {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape    = $escape;
    }

    /**
     * Adds a table to the dataset
     *
     * The table will be given the passed name. $csvFile should be a path to
     * a valid csv file (based on the arguments passed to the constructor.)
     *
     * @param string $tableName
     * @param string $csvFile
     */
    public function addTable($tableName, $csvFile)
    {
        if (!is_file($csvFile)) {
            throw new InvalidArgumentException("Could not find csv file: {$csvFile}");
        }

        if (!is_readable($csvFile)) {
            throw new InvalidArgumentException("Could not read csv file: {$csvFile}");
        }

        $fh      = fopen($csvFile, 'r');
        $columns = $this->getCsvRow($fh);

        if ($columns === FALSE)
        {
            throw new InvalidArgumentException("Could not determine the headers from the given file {$csvFile}");
        }

        $metaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData($tableName, $columns);
        $table    = new PHPUnit_Extensions_Database_DataSet_DefaultTable($metaData);

        while (($row = $this->getCsvRow($fh)) !== FALSE)
        {
            $table->addRow(array_combine($columns, $row));
        }

        $this->tables[$tableName] = $table;
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
        return new PHPUnit_Extensions_Database_DataSet_DefaultTableIterator($this->tables, $reverse);
    }

    /**
     * Returns a row from the csv file in an indexed array.
     *
     * @param  resource $fh
     * @return array
     */
    protected function getCsvRow($fh)
    {
        if (version_compare(PHP_VERSION, '5.3.0', '>')) {
            return fgetcsv($fh, NULL, $this->delimiter, $this->enclosure, $this->escape);
        } else {
            return fgetcsv($fh, NULL, $this->delimiter, $this->enclosure);
        }
    }
}
