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
 * The default factory for db extension modes.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_UI_ModeFactory implements PHPUnit_Extensions_Database_UI_IModeFactory
{
    /**
     * Generates a new mode based on a given name.
     *
     * @param  string                               $mode
     * @return PHPUnit_Extensions_Database_UI_IMode
     */
    public function getMode($mode)
    {
        if ($mode == '') {
            throw new PHPUnit_Extensions_Database_UI_InvalidModeException($mode, 'A mode was not provided.', $this);
        }

        $modeMap = $this->getModeMap();
        if (isset($modeMap[$mode])) {
            $modeClass = $this->getModeClass($mode, $modeMap[$mode]);

            return new $modeClass();
        } else {
            throw new PHPUnit_Extensions_Database_UI_InvalidModeException($mode, 'The mode does not exist. Attempting to load mode ' . $mode, $this);
        }
    }

    /**
     * Returns the names of valid modes this factory can create.
     *
     * @return array
     */
    public function getModeList()
    {
        return array_keys($this->getModeMap());
    }

    /**
     * Returns a map of modes to class name parts
     *
     * @return array
     */
    protected function getModeMap()
    {
        return ['export-dataset' => 'ExportDataSet'];
    }

    /**
     * Given a $mode label and a $mode_name class part attempts to return the
     * class name necessary to instantiate the mode.
     *
     * @param  string $mode
     * @param  string $mode_name
     * @return string
     */
    protected function getModeClass($mode, $mode_name)
    {
        $modeClass = 'PHPUnit_Extensions_Database_UI_Modes_' . $mode_name;
        $modeFile  = dirname(__FILE__) . '/Modes/' . $mode_name . '.php';

        if (class_exists($modeClass)) {
            return $modeClass;
        }

        if (!is_readable($modeFile)) {
            throw new PHPUnit_Extensions_Database_UI_InvalidModeException($mode, 'The mode\'s file could not be loaded. Trying file ' . $modeFile, $this);
        }

        require_once ($modeFile);

        if (!class_exists($modeClass)) {
            throw new PHPUnit_Extensions_Database_UI_InvalidModeException($mode, 'The mode class was not found in the file. Expecting class name ' . $modeClass, $this);
        }

        return $modeClass;
    }
}

