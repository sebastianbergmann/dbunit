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
 * The default YAML parser, using Symfony/Yaml.
 */
class PHPUnit_Extensions_Database_DataSet_SymfonyYamlParser implements PHPUnit_Extensions_Database_DataSet_IYamlParser {
    public function parseYaml($yamlFile) {
        return Symfony\Component\Yaml\Yaml::parse(file_get_contents($yamlFile));
    }
}
