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
 * Creates the appropriate DataSet Spec based on a given type.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DataSet_Specs_Factory implements PHPUnit_Extensions_Database_DataSet_Specs_IFactory
{
    /**
     * Returns the data set
     *
     * @param  string                                    $type
     * @return PHPUnit_Extensions_Database_DataSet_ISpec
     */
    public function getDataSetSpecByType($type)
    {
        switch ($type) {
            case 'xml':
                return new PHPUnit_Extensions_Database_DataSet_Specs_Xml();

            case 'flatxml':
                return new PHPUnit_Extensions_Database_DataSet_Specs_FlatXml();

            case 'csv':
                return new PHPUnit_Extensions_Database_DataSet_Specs_Csv();

            case 'yaml':
                return new PHPUnit_Extensions_Database_DataSet_Specs_Yaml();

            case 'dbtable':
                return new PHPUnit_Extensions_Database_DataSet_Specs_DbTable();

            case 'dbquery':
                return new PHPUnit_Extensions_Database_DataSet_Specs_DbQuery();

            default:
                throw new PHPUnit_Extensions_Database_Exception("I don't know what you want from me.");
        }
    }
}
