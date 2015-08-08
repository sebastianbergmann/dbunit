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
 * @since      File available since Release 1.0.0
 */
class Extensions_Database_DataSet_PersistorTest extends PHPUnit_Framework_TestCase
{
    public function testFlatXml()
    {
        $dataSetFile = dirname(__FILE__) . '/../_files/XmlDataSets/FlatXmlWriter.xml';
        $filename    = dirname(__FILE__) . '/' . uniqid() . '.xml';
        $dataSet     = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet($dataSetFile);

        PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet::write($dataSet, $filename);
        $this->assertXmlFileEqualsXmlFile($dataSetFile, $filename);
        unlink($filename);
    }

    public function testXml()
    {
        $dataSetFile = dirname(__FILE__) . '/../_files/XmlDataSets/XmlWriter.xml';
        $filename    = dirname(__FILE__) . '/' . uniqid() . '.xml';
        $dataSet     = new PHPUnit_Extensions_Database_DataSet_XmlDataSet($dataSetFile);

        PHPUnit_Extensions_Database_DataSet_XmlDataSet::write($dataSet, $filename);
        $this->assertXmlFileEqualsXmlFile($dataSetFile, $filename);
        unlink($filename);
    }

    public function testEntitiesFlatXml()
    {
        $metaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData('table1', ['col1', 'col2'], ['col1']);
        $table    = new PHPUnit_Extensions_Database_DataSet_DefaultTable($metaData);
        $table->addRow(['col1' => 1, 'col2' => '<?xml version="1.0"?><myxml>test</myxml>']);
        $dataSet  = new PHPUnit_Extensions_Database_DataSet_DefaultDataSet([$table]);

        $expectedFile = dirname(__FILE__) . '/../_files/XmlDataSets/FlatXmlWriterEntities.xml';
        $filename     = dirname(__FILE__) . '/' . uniqid() . '.xml';
        PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet::write($dataSet, $filename);
        $this->assertXmlFileEqualsXmlFile($expectedFile, $filename);
        unlink($filename);
    }

    public function testEntitiesXml()
    {
        $metaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData('table1', ['col1', 'col2'], ['col1']);
        $table    = new PHPUnit_Extensions_Database_DataSet_DefaultTable($metaData);
        $table->addRow(['col1' => 1, 'col2' => '<?xml version="1.0"?><myxml>test</myxml>']);
        $dataSet = new PHPUnit_Extensions_Database_DataSet_DefaultDataSet([$table]);

        $expectedFile = dirname(__FILE__) . '/../_files/XmlDataSets/XmlWriterEntities.xml';
        $filename     = dirname(__FILE__) . '/' . uniqid() . '.xml';
        PHPUnit_Extensions_Database_DataSet_XmlDataSet::write($dataSet, $filename);
        $this->assertXmlFileEqualsXmlFile($expectedFile, $filename);
        unlink($filename);
    }
}
