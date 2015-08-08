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
 * Creates the appropriate Persistor based on a given type and spec.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DataSet_Persistors_Factory
{
    /**
     * Returns the persistor.
     *
     * @param  string                                           $type
     * @param  string                                           $spec
     * @return PHPUnit_Extensions_Database_DataSet_IPersistable
     */
    public function getPersistorBySpec($type, $spec)
    {
        switch (strtolower($type)) {
            case 'xml':
                $xmlPersistor = new PHPUnit_Extensions_Database_DataSet_Persistors_Xml();
                $xmlPersistor->setFileName($spec);

                return $xmlPersistor;

            case 'flatxml':
                $flatXmlPersistor = new PHPUnit_Extensions_Database_DataSet_Persistors_FlatXml();
                $flatXmlPersistor->setFileName($spec);

                return $flatXmlPersistor;

            case 'yaml':
                $yamlPersistor = new PHPUnit_Extensions_Database_DataSet_Persistors_Yaml();
                $yamlPersistor->setFileName($spec);

                return $yamlPersistor;

            case 'mysqlxml':
                $mysqlXmlPersistor = new PHPUnit_Extensions_Database_DataSet_Persistors_MysqlXml();
                $mysqlXmlPersistor->setFileName($spec);

                return $mysqlXmlPersistor;

            default:
                throw new PHPUnit_Extensions_Database_Exception("I don't know what you want from me. PERSISTOR");
        }
    }
}
