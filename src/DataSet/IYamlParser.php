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

/**
 * An interface for parsing YAML files.
 */
interface IYamlParser
{
    /**
     * @param string $yamlFile
     *
     * @return array parsed YAML
     */
    public function parseYaml($yamlFile);
}
