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
 * Creates a YAML dataset based off of a spec string.
 *
 * The format of the spec string is as follows:
 *
 * <filename>
 *
 * The filename should be the location of a yaml file relative to the
 * current working directory.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DataSet_Specs_Yaml implements PHPUnit_Extensions_Database_DataSet_ISpec
{
    /**
     * Creates YAML Data Set from a data set spec.
     *
     * @param  string                                          $dataSetSpec
     * @return PHPUnit_Extensions_Database_DataSet_YamlDataSet
     */
    public function getDataSet($dataSetSpec)
    {
        return new PHPUnit_Extensions_Database_DataSet_YamlDataSet($dataSetSpec);
    }
}
