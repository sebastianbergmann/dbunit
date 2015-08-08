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
class PHPUnit_Extensions_Database_DataSet_XmlDataSet extends PHPUnit_Extensions_Database_DataSet_AbstractXmlDataSet
{
    protected function getTableInfo(Array &$tableColumns, Array &$tableValues)
    {
        if ($this->xmlFileContents->getName() != 'dataset') {
            throw new PHPUnit_Extensions_Database_Exception('The root element of an xml data set file must be called <dataset>');
        }

        foreach ($this->xmlFileContents->xpath('/dataset/table') as $tableElement) {
            if (empty($tableElement['name'])) {
                throw new PHPUnit_Extensions_Database_Exception('Table elements must include a name attribute specifying the table name.');
            }

            $tableName = (string) $tableElement['name'];

            if (!isset($tableColumns[$tableName])) {
                $tableColumns[$tableName] = [];
            }

            if (!isset($tableValues[$tableName])) {
                $tableValues[$tableName] = [];
            }

            $tableInstanceColumns = [];

            foreach ($tableElement->xpath('./column') as $columnElement) {
                $columnName = (string) $columnElement;
                if (empty($columnName)) {
                    throw new PHPUnit_Extensions_Database_Exception("Missing <column> elements for table $tableName. Add one or more <column> elements to the <table> element.");
                }

                if (!in_array($columnName, $tableColumns[$tableName])) {
                    $tableColumns[$tableName][] = $columnName;
                }

                $tableInstanceColumns[] = $columnName;
            }

            foreach ($tableElement->xpath('./row') as $rowElement) {
                $rowValues                 = [];
                $index                     = 0;
                $numOfTableInstanceColumns = count($tableInstanceColumns);

                foreach ($rowElement->children() as $columnValue) {

                    if ($index >= $numOfTableInstanceColumns) {
                        throw new PHPUnit_Extensions_Database_Exception("Row contains more values than the number of columns defined for table $tableName.");
                    }
                    switch ($columnValue->getName()) {
                        case 'value':
                            $rowValues[$tableInstanceColumns[$index]] = (string) $columnValue;
                            $index++;
                            break;
                        case 'null':
                            $rowValues[$tableInstanceColumns[$index]] = NULL;
                            $index++;
                            break;
                        default:
                            throw new PHPUnit_Extensions_Database_Exception('Unknown element ' . $columnValue->getName() . ' in a row element.');
                    }
                }

                $tableValues[$tableName][] = $rowValues;
            }
        }
    }

    public static function write(PHPUnit_Extensions_Database_DataSet_IDataSet $dataset, $filename)
    {
        $pers = new PHPUnit_Extensions_Database_DataSet_Persistors_Xml();
        $pers->setFileName($filename);

        try {
            $pers->write($dataset);
        }

        catch (RuntimeException $e) {
            throw new PHPUnit_Framework_Exception(__METHOD__ . ' called with an unwritable file.');
        }
    }
}
