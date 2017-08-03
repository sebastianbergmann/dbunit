<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\DataSet;

use PHPUnit\DbUnit\RuntimeException;

/**
 * Data set implementation for the output of mysqldump --xml.
 */
class MysqlXmlDataSet extends AbstractXmlDataSet
{
    protected function getTableInfo(array &$tableColumns, array &$tableValues)
    {
        if ($this->xmlFileContents->getName() != 'mysqldump') {
            throw new RuntimeException('The root element of a MySQL XML data set file must be called <mysqldump>');
        }

        foreach ($this->xmlFileContents->xpath('./database/table_data') as $tableElement) {
            if (empty($tableElement['name'])) {
                throw new RuntimeException('<table_data> elements must include a name attribute');
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
                        throw new RuntimeException('<field> element name attributes cannot be empty');
                    }

                    $columnName = (string) $columnElement['name'];

                    if (!\in_array($columnName, $tableColumns[$tableName])) {
                        $tableColumns[$tableName][] = $columnName;
                    }
                }

                foreach ($tableColumns[$tableName] as $columnName) {
                    $fields = $rowElement->xpath('./field[@name="' . $columnName . '"]');
                    if (!isset($fields[0])) {
                        throw new RuntimeException(
                            sprintf(
                                '%s column doesn\'t exist in current row for table %s',
                                $columnName,
                                $tableName
                            )
                        );
                    }

                    $column = $fields[0];
                    $attr   = $column->attributes('http://www.w3.org/2001/XMLSchema-instance');

                    if (isset($attr['type']) && (string) $attr['type'] === 'xs:hexBinary') {
                        $columnValue = \pack('H*', (string) $column);
                    } else {
                        $null        = isset($column['nil']) || isset($attr[0]);
                        $columnValue = $null ? null : (string) $column;
                    }

                    $rowValues[$columnName] = $columnValue;
                }

                $tableValues[$tableName][] = $rowValues;
            }
        }

        foreach ($this->xmlFileContents->xpath('./database/table_structure') as $tableElement) {
            if (empty($tableElement['name'])) {
                throw new RuntimeException('<table_structure> elements must include a name attribute');
            }

            $tableName = (string) $tableElement['name'];

            foreach ($tableElement->xpath('./field') as $fieldElement) {
                if (empty($fieldElement['Field']) && empty($fieldElement['field'])) {
                    throw new RuntimeException('<field> elements must include a Field attribute');
                }

                $columnName = (string) (empty($fieldElement['Field']) ? $fieldElement['field'] : $fieldElement['Field']);

                if (!\in_array($columnName, $tableColumns[$tableName])) {
                    $tableColumns[$tableName][] = $columnName;
                }
            }
        }
    }
}
