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
 * A text medium for the database extension tool.
 *
 * This class builds the call context based on command line parameters and
 * prints output to stdout and stderr as appropriate.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_UI_Mediums_Text implements PHPUnit_Extensions_Database_UI_IMedium
{
    /**
     * @var array
     */
    protected $arguments;

    /**
     * @var string
     */
    protected $command;

    /**
     * @param array $arguments
     */
    public function __construct(Array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Builds the context for the application.
     *
     * @param PHPUnit_Extensions_Database_UI_Context $context
     */
    public function buildContext(PHPUnit_Extensions_Database_UI_Context $context)
    {
        $arguments     = $this->arguments;
        $this->command = array_shift($arguments);

        $context->setMode(array_shift($arguments));
        $context->setModeArguments($arguments);
    }

    /**
     * Handles the displaying of exceptions received from the application.
     *
     * @param Exception $e
     */
    public function handleException(Exception $e)
    {
        try {
            throw $e;
        } catch (PHPUnit_Extensions_Database_UI_InvalidModeException $invalidMode) {
            if ($invalidMode->getMode() == '') {
                $this->error('Please Specify a Command!' . PHP_EOL);
            } else {
                $this->error('Command Does Not Exist: ' . $invalidMode->getMode() . PHP_EOL);
            }
            $this->error('Valid Commands:' . PHP_EOL);

            foreach ($invalidMode->getValidModes() as $mode) {
                $this->error('    ' . $mode . PHP_EOL);
            }
        } catch (Exception $e) {
            $this->error('Unknown Error: ' . $e->getMessage() . PHP_EOL);
        }
    }

    /**
     * Prints the message to stdout.
     *
     * @param string $message
     */
    public function output($message)
    {
        echo $message;
    }

    /**
     * Prints the message to stderr
     *
     * @param string $message
     */
    public function error($message)
    {
        fputs(STDERR, $message);
    }
}

