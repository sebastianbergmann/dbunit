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
 * A class factory to easily return database operations.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_Operation_Factory
{
    /**
     * Returns a null database operation
     *
     * @return PHPUnit_Extensions_Database_Operation_IDatabaseOperation
     */
    public static function NONE()
    {
        return new PHPUnit_Extensions_Database_Operation_Null();
    }

    /**
     * Returns a clean insert database operation. It will remove all contents
     * from the table prior to re-inserting rows.
     *
     * @param  bool                                                     $cascadeTruncates Set to true to force truncates to cascade on databases that support this.
     * @return PHPUnit_Extensions_Database_Operation_IDatabaseOperation
     */
    public static function CLEAN_INSERT($cascadeTruncates = FALSE)
    {
        return new PHPUnit_Extensions_Database_Operation_Composite([
            self::TRUNCATE($cascadeTruncates),
            self::INSERT()
        ]);
    }

    /**
     * Returns an insert database operation.
     *
     * @return PHPUnit_Extensions_Database_Operation_IDatabaseOperation
     */
    public static function INSERT()
    {
        return new PHPUnit_Extensions_Database_Operation_Insert();
    }

    /**
     * Returns a truncate database operation.
     *
     * @param  bool                                                     $cascadeTruncates Set to true to force truncates to cascade on databases that support this.
     * @return PHPUnit_Extensions_Database_Operation_IDatabaseOperation
     */
    public static function TRUNCATE($cascadeTruncates = FALSE)
    {
        $truncate = new PHPUnit_Extensions_Database_Operation_Truncate();
        $truncate->setCascade($cascadeTruncates);

        return $truncate;
    }

    /**
     * Returns a delete database operation.
     *
     * @return PHPUnit_Extensions_Database_Operation_IDatabaseOperation
     */
    public static function DELETE()
    {
        return new PHPUnit_Extensions_Database_Operation_Delete();
    }

    /**
     * Returns a delete_all database operation.
     *
     * @return PHPUnit_Extensions_Database_Operation_IDatabaseOperation
     */
    public static function DELETE_ALL()
    {
        return new PHPUnit_Extensions_Database_Operation_DeleteAll();
    }

    /**
     * Returns an update database operation.
     *
     * @return PHPUnit_Extensions_Database_Operation_IDatabaseOperation
     */
    public static function UPDATE()
    {
        return new PHPUnit_Extensions_Database_Operation_Update();
    }

}
