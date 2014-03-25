DbUnit 1.3
==========

This is the list of changes for the DbUnit 1.3 release series.

DbUnit 1.3.0
-------------

* [#82](https://github.com/sebastianbergmann/dbunit/issues/82) Composer now allows for dev packages as the master branch requires PHPUnit >= 3.8.0 ([whatthejeff](https://github.com/whatthejeff))
* [#66](https://github.com/sebastianbergmann/dbunit/pull/66) The dependency on the Symfony YAML library is now properly indicated in `package.xml` ([elazar](https://github.com/elazar))
* [#54](https://github.com/sebastianbergmann/dbunit/pull/54) `Operation_RowBased->execute()` now handles cases where `PDO::ATTR_EMULATE_PREPARES` is set to `false` ([dexen](https://github.com/dexen))
* [#28](https://github.com/sebastianbergmann/dbunit/issues/28) [#61](https://github.com/sebastianbergmann/dbunit/issues/61) `matches()` in `AbstractTable` and `ReplacementTable` now include type checking of column values
* [#88](https://github.com/sebastianbergmann/dbunit/pull/88) `getTable()` and `getTableMetaData()` in `DefaultTableIterator` now properly return values ([szicsu](https://github.com/szicsu))
* [#81](https://github.com/sebastianbergmann/dbunit/pull/81)`Operation_RowBased` now performs significantly better for truncate-only tables ([wakeless](https://github.com/wakeless))
* [#78](https://github.com/sebastianbergmann/dbunit/pull/78) `TestCase->assertTableRowCount()` now correctly calls `getConnection()` as an instance method rather than a static method ([josefzamrzla](https://github.com/josefzamrzla))
* [#69](https://github.com/sebastianbergmann/dbunit/pull/69) `QueryTable` now overrides `assertContainsRow()` to invoke `loadData()` before calling the parent implementation ([jeunito](https://github.com/jeunito))
* [#93](https://github.com/sebastianbergmann/dbunit/pull/93) [#95](https://github.com/sebastianbergmann/dbunit/pull/95) `AbstractTable` now supports outputting table diffs ([ptrofimov](https://github.com/ptrofimov))
* [#67](https://github.com/sebastianbergmann/dbunit/pull/67) [#117](https://github.com/sebastianbergmann/dbunit/pull/117) The Firebird database is now supported ([matheusd](https://github.com/matheusd))
* [#101](https://github.com/sebastianbergmann/dbunit/pull/101) PostgreSQL primary keys now load correctly ([danielek](https://github.com/danielek))
* [#103](https://github.com/sebastianbergmann/dbunit/pull/103) `Sqlite->getTableNames()` no longer emits a notice when called on an empty database ([neilime](https://github.com/neilime))
* [#104](https://github.com/sebastianbergmann/dbunit/pull/104) [#105](https://github.com/sebastianbergmann/dbunit/pull/105) [#106](https://github.com/sebastianbergmann/dbunit/pull/106) `YamlDataSet` can now support YAML parsers other than the one from Symfony via the `IYamlParser` interface ([yparghi](https://github.com/yparghi))
* [#100](https://github.com/sebastianbergmann/dbunit/pull/100) `dbunit.php` no longer references the deprecated singleton class `PHP_CodeCoverage_Filter` ([elazar](https://github.com/elazar))
