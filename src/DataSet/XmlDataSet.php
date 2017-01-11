<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\DbUnit\RuntimeException;

/**
 * The default implementation of a data set.
 */
class PHPUnit_Extensions_Database_DataSet_XmlDataSet extends PHPUnit_Extensions_Database_DataSet_AbstractXmlDataSet
{
    protected function getTableInfo(Array &$tableColumns, Array &$tableValues)
    {
        if ($this->xmlFileContents->getName() != 'dataset') {
            throw new RuntimeException('The root element of an xml data set file must be called <dataset>');
        }

        foreach ($this->xmlFileContents->xpath('/dataset/table') as $tableElement) {
            if (empty($tableElement['name'])) {
                throw new RuntimeException('Table elements must include a name attribute specifying the table name.');
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
                    throw new RuntimeException("Missing <column> elements for table $tableName. Add one or more <column> elements to the <table> element.");
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
                        throw new RuntimeException("Row contains more values than the number of columns defined for table $tableName.");
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
                            throw new RuntimeException('Unknown element ' . $columnValue->getName() . ' in a row element.');
                    }
                }

                $tableValues[$tableName][] = $rowValues;
            }
        }
    }
}
