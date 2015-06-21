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
 * An interface for parsing YAML files.
 *
 * @since      Class available since Release 1.3.1
 */
interface PHPUnit_Extensions_Database_DataSet_IYamlParser {
    /**
     * @param  string $yamlFile
     * @return array  parsed YAML
     */
    public function parseYaml($yamlFile);
}
