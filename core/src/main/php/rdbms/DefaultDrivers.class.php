<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.DriverImplementationsProvider');

  /**
   * Default driver preferences for driver manager
   *
   * @see   xp://rdbms.DriverImplementationsProvider
   */
  class DefaultDrivers extends DriverImplementationsProvider {
    protected static $impl= array();

    static function __static() {

      // MySQL support: Use mysql extension by default, mysqli otherwise. Never use mysqlnd!
      if (extension_loaded('mysqlnd')) {
        self::$impl['mysql']= array('rdbms.mysqlx.MySqlxConnection', 'rdbms.mysql.MySQLConnection', 'rdbms.mysqli.MySQLiConnection');
      } else {
        self::$impl['mysql']= array('rdbms.mysql.MySQLConnection', 'rdbms.mysqli.MySQLiConnection', 'rdbms.mysqlx.MySqlxConnection');
      }

      // Sybase support: Prefer sybase_ct over mssql
      if (extension_loaded('sybase_ct')) {
        self::$impl['sybase']= array('rdbms.sybase.SybaseConnection', 'rdbms.mssql.MsSQLConnection', 'rdbms.tds.SybasexConnection');
      } else {
        self::$impl['sybase']= array('rdbms.mssql.MsSQLConnection', 'rdbms.sybase.SybaseConnection', 'rdbms.tds.SybasexConnection');
      }

      // MSSQL support: Prefer SQLsrv from Microsoft over mssql 
      if (extension_loaded('sqlsrv')) {
        self::$impl['mssql']= array('rdbms.sqlsrv.SqlSrvConnection', 'rdbms.mssql.MsSQLConnection', 'rdbms.tds.MsSQLxConnection');
      } else {
        self::$impl['mssql']= array('rdbms.mssql.MsSQLConnection', 'rdbms.sqlsrv.SqlSrvConnection', 'rdbms.tds.MsSQLxConnection');
      }

      // PostgreSQL support
      self::$impl['pgsql']= array('rdbms.pgsql.PostgreSQLConnection');

      // SQLite support
      self::$impl['sqlite']= array('rdbms.sqlite3.SQLite3Connection', 'rdbms.sqlite.SQLiteConnection');

      // Interbase support
      self::$impl['ibase']= array('rdbms.ibase.InterBaseConnection');
    }

    /**
     * Returns an array of class names implementing a given driver
     *
     * @param   string driver
     * @return  string[] implementations
     */
    public function implementationsFor($driver) {
      return isset(self::$impl[$driver]) ? self::$impl[$driver] : parent::implementationsFor($driver);
    }
  }
?>
