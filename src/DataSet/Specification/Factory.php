<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\DbUnit\DataSet\Specification\Csv;
use PHPUnit\DbUnit\DataSet\Specification\Table;
use PHPUnit\DbUnit\DataSet\Specification\Query;
use PHPUnit\DbUnit\RuntimeException;

/**
 * Creates the appropriate DataSet Spec based on a given type.
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
                return new Csv();

            case 'yaml':
                return new PHPUnit_Extensions_Database_DataSet_Specs_Yaml();

            case 'dbtable':
                return new Table();

            case 'dbquery':
                return new Query();

            default:
                throw new RuntimeException("I don't know what you want from me.");
        }
    }
}
