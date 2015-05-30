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
 * @package    DbUnit
 * @author     Matthew Turland <tobias382@gmail.com>
 * @copyright  2010-2014 Matthew Turland <tobias382@gmail.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DataSet_MysqlXmlDataSet extends PHPUnit_Extensions_Database_DataSet_AbstractXmlDataSet
{

    /**
     * Creates a new data set using the given tables.
     *
     * @param string $xmlFile
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function __construct($xmlFile)
    {
        if (!is_file($xmlFile)) {
            throw new InvalidArgumentException(
                "Could not find xml file: {$xmlFile}"
            );
        }

        $libxmlErrorReporting  = libxml_use_internal_errors(TRUE);
        try{
            $this->readDocument($xmlFile);
        } catch (Exception $e) {
            $this->unRegisterXMLSequenceStream();
        }

        $errors = libxml_get_errors();
        if (count($errors)) {
            $message = '';

            foreach ($errors as $error) {
                $message .= print_r($error, true);
            }

            throw new RuntimeException($message);
        }

        libxml_clear_errors();
        libxml_use_internal_errors($libxmlErrorReporting);

        $tableColumns = array();
        $tableValues  = array();

        $this->getTableInfo($tableColumns, $tableValues);
        $this->createTables($tableColumns, $tableValues);
    }


    protected function getTableInfo(array &$tableColumns, array &$tableValues)
    {
        foreach($this->xmlFileContents as $dump) {

            if ($dump->getName() != 'mysqldump') {
                throw new PHPUnit_Extensions_Database_Exception('The root element of a MySQL XML data set file must be called <mysqldump>');
            }

            $this->getTableData($dump, $tableColumns, $tableValues);

            $this->getTableStructure($dump, $tableColumns);
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


    /**
     * Loading file that could contain several xml documents concatenated
     *
     * @param string $xmlFile
     * @return SimpleXMLElement
     */
    protected function readDocument($xmlFile)
    {
        $this->registerXMLSequenceStream();
        $path = "xmlseq://" . $xmlFile;

        $this->xmlFileContents = array();
        while (XMLSequenceStream::notAtEndOfSequence($path)) {
            $reader = new XMLReader();
            $reader->open($path);
            /** @var XMLElementIterator|XMLReaderNode $elements */
            $elements = new XMLElementIterator($reader);
            $this->xmlFileContents[] = new SimpleXMLElement(
                $elements->current()->readOuterXml()
            );
        }

        XMLSequenceStream::clean();
        $this->unRegisterXMLSequenceStream();
    }

    /**
     * Register the wrapper for the stream
     */
    protected function registerXMLSequenceStream() {
        if(!in_array('xmlseq', stream_get_wrappers())) {
            stream_wrapper_register('xmlseq', 'XMLSequenceStream');
        }
    }

    /**
     * De-register the wrapper for the stream
     */
    protected function unRegisterXMLSequenceStream() {
        if(in_array('xmlseq', stream_get_wrappers())) {
            stream_wrapper_unregister('xmlseq');
        }
    }

    /**
     * Extract table_data elements from xml dump
     *
     * @param SimpleXmlElement $xmlDump
     * @param array $tableColumns
     * @param array $tableValues
     * @throws PHPUnit_Extensions_Database_Exception
     */
    protected function getTableData(SimpleXmlElement $xmlDump, array &$tableColumns, array &$tableValues) {

        foreach ($xmlDump->xpath('./database/table_data') as $tableElement) {
            if (empty($tableElement['name'])) {
                throw new PHPUnit_Extensions_Database_Exception('<table_data> elements must include a name attribute');
            }

            $tableName = (string)$tableElement['name'];

            if (!isset($tableColumns[$tableName])) {
                $tableColumns[$tableName] = array();
            }

            if (!isset($tableValues[$tableName])) {
                $tableValues[$tableName] = array();
            }

            foreach ($tableElement->xpath('./row') as $rowElement) {
                $rowValues = array();

                foreach ($rowElement->xpath('./field') as $columnElement) {
                    if (empty($columnElement['name'])) {
                        throw new PHPUnit_Extensions_Database_Exception('<field> element name attributes cannot be empty');
                    }

                    $columnName = (string)$columnElement['name'];

                    if (!in_array($columnName, $tableColumns[$tableName])) {
                        $tableColumns[$tableName][] = $columnName;
                    }
                }

                foreach ($tableColumns[$tableName] as $columnName) {
                    $fields          = $rowElement->xpath('./field[@name="' . $columnName . '"]');
                    $column          = $fields[0];
                    $attr            = $column->attributes('http://www.w3.org/2001/XMLSchema-instance');

                    if (isset($attr['type']) && (string) $attr['type'] === 'xs:hexBinary') {
                        $columnValue = pack('H*',(string)$column);
                    } else {
                        $null        = isset($column['nil']) || isset($attr[0]);
                        $columnValue = $null ? NULL : (string)$column;
                    }

                    $rowValues[$columnName] = $columnValue;

                }

                $tableValues[$tableName][] = $rowValues;
            }
        }
    }

    /**
     * Extract table_data elements from xml dump
     *
     * @param SimpleXmlElement $xmlDump
     * @param array $tableColumns
     * @throws PHPUnit_Extensions_Database_Exception
     */
    protected function getTableStructure(SimpleXmlElement $xmlDump, array &$tableColumns) {

        foreach ($xmlDump->xpath('./database/table_structure') as $tableElement) {
            if (empty($tableElement['name'])) {
                throw new PHPUnit_Extensions_Database_Exception('<table_structure> elements must include a name attribute');
            }

            $tableName = (string) $tableElement['name'];

            foreach ($tableElement->xpath('./field') as $fieldElement) {
                if (empty($fieldElement['Field'])) {
                    throw new PHPUnit_Extensions_Database_Exception('<field> elements must include a Field attribute');
                }

                $columnName = (string) $fieldElement['Field'];

                if (array_key_exists($tableName, $tableColumns) && !in_array($columnName, $tableColumns[$tableName])) {
                    $tableColumns[$tableName][] = $columnName;
                }
            }
        }
    }
}
