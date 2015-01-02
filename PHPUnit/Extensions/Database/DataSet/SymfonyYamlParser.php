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
 *
 * @package    DbUnit
 * @author     Yash Parghi <yash@yashparghi.com>
 * @copyright  Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 1.3.1
 */
class PHPUnit_Extensions_Database_DataSet_SymfonyYamlParser implements PHPUnit_Extensions_Database_DataSet_IYamlParser {

    public function parseYaml($yamlFile) {
        return Symfony\Component\Yaml\Yaml::parse($yamlFile);
    }
}
