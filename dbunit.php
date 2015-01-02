#!/usr/bin/env php
<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (strpos('@php_bin@', '@php_bin') === 0) {
    set_include_path(dirname(__FILE__) . PATH_SEPARATOR . get_include_path());
}

require_once 'PHPUnit/Autoload.php';

$command = new PHPUnit_Extensions_Database_UI_Command(
  new PHPUnit_Extensions_Database_UI_ModeFactory()
);

$command->main(
  new PHPUnit_Extensions_Database_UI_Mediums_Text($_SERVER['argv']),
  new PHPUnit_Extensions_Database_UI_Context()
);
