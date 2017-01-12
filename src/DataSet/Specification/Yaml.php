<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\DataSet\Specification;

use PHPUnit\DbUnit\DataSet\YamlDataSet;

/**
 * Creates a YAML dataset based off of a spec string.
 *
 * The format of the spec string is as follows:
 *
 * <filename>
 *
 * The filename should be the location of a yaml file relative to the
 * current working directory.
 */
class Yaml implements Specification
{
    /**
     * Creates YAML Data Set from a data set spec.
     *
     * @param string $dataSetSpec
     *
     * @return YamlDataSet
     */
    public function getDataSet($dataSetSpec)
    {
        return new YamlDataSet($dataSetSpec);
    }
}
