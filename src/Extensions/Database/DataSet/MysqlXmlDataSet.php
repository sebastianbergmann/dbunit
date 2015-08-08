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
 * Data set implementation for the output of mysqldump --xml.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DataSet_MysqlXmlDataSet extends PHPUnit_Extensions_Database_DataSet_AbstractXmlDataSet
{
    protected function getTableInfo(array &$tableColumns, array &$tableValues)
    {
        if ($this->xmlFileContents->getName() != 'mysqldump') {
            throw new PHPUnit_Extensions_Database_Exception('The root element of a MySQL XML data set file must be called <mysqldump>');
        }

        foreach ($this->xmlFileContents->xpath('./database/table_data') as $tableElement) {
            if (empty($tableElement['name'])) {
                throw new PHPUnit_Extensions_Database_Exception('<table_data> elements must include a name attribute');
            }

            $tableName = (string) $tableElement['name'];

            if (!isset($tableColumns[$tableName])) {
                $tableColumns[$tableName] = [];
            }

            if (!isset($tableValues[$tableName])) {
                $tableValues[$tableName] = [];
            }

            foreach ($tableElement->xpath('./row') as $rowElement) {
                $rowValues = [];

                foreach ($rowElement->xpath('./field') as $columnElement) {
                    if (empty($columnElement['name'])) {
                        throw new PHPUnit_Extensions_Database_Exception('<field> element name attributes cannot be empty');
                    }

                    $columnName = (string) $columnElement['name'];

                    if (!in_array($columnName, $tableColumns[$tableName])) {
                        $tableColumns[$tableName][] = $columnName;
                    }
                }

                foreach ($tableColumns[$tableName] as $columnName) {
                    $fields          = $rowElement->xpath('./field[@name="' . $columnName . '"]');
                    $column          = $fields[0];
                    $attr            = $column->attributes('http://www.w3.org/2001/XMLSchema-instance');

                    if (isset($attr['type']) && (string) $attr['type'] === 'xs:hexBinary') {
                        $columnValue = pack('H*',(string) $column);
                    } else {
                        $null        = isset($column['nil']) || isset($attr[0]);
                        $columnValue = $null ? NULL : (string) $column;
                    }

                    $rowValues[$columnName] = $columnValue;

                }

                $tableValues[$tableName][] = $rowValues;
            }
        }

        foreach ($this->xmlFileContents->xpath('./database/table_structure') as $tableElement) {
            if (empty($tableElement['name'])) {
                throw new PHPUnit_Extensions_Database_Exception('<table_structure> elements must include a name attribute');
            }

            $tableName = (string) $tableElement['name'];

            foreach ($tableElement->xpath('./field') as $fieldElement) {
                if (empty($fieldElement['Field'])) {
                    throw new PHPUnit_Extensions_Database_Exception('<field> elements must include a Field attribute');
                }

                $columnName = (string) $fieldElement['Field'];

                if (!in_array($columnName, $tableColumns[$tableName])) {
                    $tableColumns[$tableName][] = $columnName;
                }
            }
        }
    }

    public static function write(PHPUnit_Extensions_Database_DataSet_IDataSet $dataset, $filename)
    {
        $pers = new PHPUnit_Extensions_Database_DataSet_Persistors_MysqlXml;
        $pers->setFileName($filename);

        try {
            $pers->write($dataset);
        }

        catch (RuntimeException $e) {
            throw new PHPUnit_Framework_Exception(__METHOD__ . ' called with an unwritable file.');
        }
    }
}
