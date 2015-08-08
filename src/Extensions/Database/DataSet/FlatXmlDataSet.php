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
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet extends PHPUnit_Extensions_Database_DataSet_AbstractXmlDataSet
{
    protected function getTableInfo(Array &$tableColumns, Array &$tableValues)
    {
        if ($this->xmlFileContents->getName() != 'dataset') {
            throw new PHPUnit_Extensions_Database_Exception('The root element of a flat xml data set file must be called <dataset>');
        }

        foreach ($this->xmlFileContents->children() as $row) {
            $tableName = $row->getName();

            if (!isset($tableColumns[$tableName])) {
                $tableColumns[$tableName] = [];
                $tableValues[$tableName]  = [];
            }

            $values = [];
            foreach ($row->attributes() as $name => $value) {
                if (!in_array($name, $tableColumns[$tableName])) {
                    $tableColumns[$tableName][] = $name;
                }

                $values[$name] = $value;
            }

            if (count($values)) {
                $tableValues[$tableName][] = $values;
            }
        }
    }

    public static function write(PHPUnit_Extensions_Database_DataSet_IDataSet $dataset, $filename)
    {
        $pers = new PHPUnit_Extensions_Database_DataSet_Persistors_FlatXml();
        $pers->setFileName($filename);

        try {
            $pers->write($dataset);
        } catch (RuntimeException $e) {
            throw new PHPUnit_Framework_Exception(__METHOD__ . ' called with an unwritable file.');
        }
    }
}
