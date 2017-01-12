<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\Operation;

use PHPUnit_Extensions_Database_Operation_Insert;
use PHPUnit_Extensions_Database_Operation_Null;
use PHPUnit_Extensions_Database_Operation_Update;

/**
 * A class factory to easily return database operations.
 */
class Factory
{
    /**
     * Returns a null database operation
     *
     * @return Operation
     */
    public static function NONE()
    {
        return new PHPUnit_Extensions_Database_Operation_Null();
    }

    /**
     * Returns a clean insert database operation. It will remove all contents
     * from the table prior to re-inserting rows.
     *
     * @param  bool $cascadeTruncates Set to true to force truncates to cascade on databases that support this.
     * @return Operation
     */
    public static function CLEAN_INSERT($cascadeTruncates = false)
    {
        return new Composite([
            self::TRUNCATE($cascadeTruncates),
            self::INSERT()
        ]);
    }

    /**
     * Returns an insert database operation.
     *
     * @return Operation
     */
    public static function INSERT()
    {
        return new PHPUnit_Extensions_Database_Operation_Insert();
    }

    /**
     * Returns a truncate database operation.
     *
     * @param  bool $cascadeTruncates Set to true to force truncates to cascade on databases that support this.
     * @return Operation
     */
    public static function TRUNCATE($cascadeTruncates = false)
    {
        $truncate = new Truncate();
        $truncate->setCascade($cascadeTruncates);

        return $truncate;
    }

    /**
     * Returns a delete database operation.
     *
     * @return Operation
     */
    public static function DELETE()
    {
        return new Delete();
    }

    /**
     * Returns a delete_all database operation.
     *
     * @return Operation
     */
    public static function DELETE_ALL()
    {
        return new DeleteAll();
    }

    /**
     * Returns an update database operation.
     *
     * @return Operation
     */
    public static function UPDATE()
    {
        return new PHPUnit_Extensions_Database_Operation_Update();
    }

}
