<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\DataSet\Specification;

use PHPUnit\DbUnit\DataSet\FlatXmlDataSet;
use PHPUnit_Extensions_Database_DataSet_ISpec;

/**
 * Creates a FlatXML dataset based off of a spec string.
 *
 * The format of the spec string is as follows:
 *
 * <filename>
 *
 * The filename should be the location of a flat xml file relative to the
 * current working directory.
 */
class FlatXml implements PHPUnit_Extensions_Database_DataSet_ISpec
{
    /**
     * Creates Flat XML Data Set from a data set spec.
     *
     * @param  string $dataSetSpec
     * @return FlatXmlDataSet
     */
    public function getDataSet($dataSetSpec)
    {
        return new FlatXmlDataSet($dataSetSpec);
    }
}
