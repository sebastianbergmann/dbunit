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

use PHPUnit\DbUnit\RuntimeException;

/**
 * Creates the appropriate DataSet Spec based on a given type.
 */
class Factory implements IFactory
{
    /**
     * Returns the data set
     *
     * @param string $type
     *
     * @return Specification
     */
    public function getDataSetSpecByType($type)
    {
        switch ($type) {
            case 'xml':
                return new Xml();

            case 'flatxml':
                return new FlatXml();

            case 'csv':
                return new Csv();

            case 'yaml':
                return new Yaml();

            case 'dbtable':
                return new Table();

            case 'dbquery':
                return new Query();

            default:
                throw new RuntimeException("I don't know what you want from me.");
        }
    }
}
