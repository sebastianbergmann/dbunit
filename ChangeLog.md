# Changes in DbUnit

All notable changes to DbUnit are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [3.0.3] - 2018-01-23

### Fixed

* Fixed [#191](https://github.com/sebastianbergmann/dbunit/pull/191): MySQL's `FOREIGN_KEY_CHECKS` setting gets lost
* Fixed [#192](https://github.com/sebastianbergmann/dbunit/pull/192): Error message for wrong fixture is not good enough
* Fixed [#201](https://github.com/sebastianbergmann/dbunit/pull/201): `TestCaseTrait::tearDown()` does not call parent's `tearDown()`
* Fixed [#204](https://github.com/sebastianbergmann/dbunit/pull/204): `DefaultConnection::close()` does not close database connection
* Fixed [#205](https://github.com/sebastianbergmann/dbunit/pull/205): Metadata for empty SQLite table is not handled correctly

## [3.0.2] - 2017-11-18

### Changed

* This component is now compatible with Symfony Console 4

## [3.0.1] - 2017-10-19

### Fixed

* Fixed [#193](https://github.com/sebastianbergmann/dbunit/pull/193): Multibyte values are not displayed correctly
* Fixed [#195](https://github.com/sebastianbergmann/dbunit/issues/195): Empty tables are not displayed correctly

## [3.0.0] - 2017-02-03

### Changed

* DbUnit's units of code are now namespaced
* DbUnit is now compatible with, and requires, PHPUnit 6.0

### Removed

* The `dbunit` CLI tool was removed

[3.0.3]: https://github.com/sebastianbergmann/dbunit/compare/3.0.2...3.0.3
[3.0.2]: https://github.com/sebastianbergmann/dbunit/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/sebastianbergmann/dbunit/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/sebastianbergmann/dbunit/compare/2.0...3.0.0
