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
 * The default implementation of a data set.
 *
 * @package    DbUnit
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  2010-2014 Mike Lively <m@digitalsandwich.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DataSet_DefaultDataSet extends PHPUnit_Extensions_Database_DataSet_AbstractDataSet
{

    /**
     * An array of ITable objects.
     *
     * @var array
     */
    protected $tables;

    /**
     * Creates a new dataset using the given tables.
     *
     * @param array $tables
     */
    public function __construct(Array $tables = array())
    {
        $this->tables = $tables;
    }

    /**
     * Adds a table to the dataset.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITable $table
     */
    public function addTable(PHPUnit_Extensions_Database_DataSet_ITable $table)
    {
        $this->tables[] = $table;
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
        return new PHPUnit_Extensions_Database_DataSet_DefaultTableIterator($this->tables, $reverse);
    }
}
