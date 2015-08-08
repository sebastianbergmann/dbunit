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
 * An exception thrown when an invalid mode is requested from a mode factory.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_UI_InvalidModeException extends LogicException
{
    /**
     * @var string
     */
    protected $mode;

    /**
     * @var PHPUnit_Extensions_Database_UI_IModeFactory
     */
    protected $modeFactory;

    /**
     * @param string                                      $mode
     * @param string                                      $msg
     * @param PHPUnit_Extensions_Database_UI_IModeFactory $modeFactory
     */
    public function __construct($mode, $msg, PHPUnit_Extensions_Database_UI_IModeFactory $modeFactory)
    {
        $this->mode        = $mode;
        $this->modeFactory = $modeFactory;
        parent::__construct($msg);
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @return array
     */
    public function getValidModes()
    {
        return $this->modeFactory->getModeList();
    }
}

