<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.DBConnection', 'rdbms.DriverNotSupportedException');

  /**
   * Manages database drivers
   *
   * DSNs
   * ====
   * The DriverManager class expects a unified connection string (we call 
   * it DSN) specifying the following: 
   * <ul>
   *   <li>The driver (here: <tt>sybase</tt>). This corresponds either to a 
   *       built-in driver class or one you have previously registered to it.
   *   </li>
   *   <li>An optional username and password (here: <tt>user</tt> and 
   *       <tt>pass</tt>).
   *   </li>
   *   <li>The hostname of the rdbms (here: <tt>server</tt>).
   *       Hostname is not completely correct: in SQLite, for example, this 
   *       specifies the name of the data file; in Sybase, it corresponds to 
   *       an entry in the interfaces file.
   *   </li>
   *   <li>The database name (here: <tt>NICOTINE</tt>).
   *       May be ommitted - for instance, Sybase offers a per-user default 
   *       database setting which automatically selects the specified database 
   *       after log in.
   *   </li>
   *   <li>Optional parameters (here: none).</li>
   * </ul>
   * Parameters in DSN are used in a key-value syntax as known from HTTP 
   * urls, e.g. <tt>mysql://user:pass@server?autoconnect=1</tt>.
   *
   * These parameters are recognized: 
   * <ul>
   *   <li>*autoconnect=value* - A call to rdbms.DBConnection#connect may be 
   *       ommitted. Just go ahead and when calling the first method which 
   *       needs to connect (and log in), a connection will be established.
   *       Value is an integer of either 1 (on) or 0 (off). Default is 0 (off). 
   *   </li>
   *   <li>*persistent=value* - Uses persistent database connections. These 
   *       are explained in the PHP manual. Value is an integer of either 1 
   *       (on) or 0 (off). Default is 0 (off). 
   *   </li>
   *   <li>*timeout=value* - Sets a connection timeout. Value is an integer 
   *       specifying the number of seconds to wait before cancelling a connect/ 
   *       log on procedure. Default may vary between different RDBMS. 
   *   </li>
   *   <li>*observer[key]=value* - Adds observers to the connection. The key 
   *       corresponds to an observer class, the value to a string passed to 
   *       its constructor. 
   *   </li>
   * </ul>
   * Note: For convenience, <tt>log=category</tt> is supported as an alias 
   * for <tt>observer[util.log.LogObserver]=category</tt>.
   *
   * Usage
   * =====
   * <code>
   *   uses('rdbms.DriverManager');
   *   
   *   $conn= DriverManager::getConnection('sybase://user:pass@server');
   *   $conn->connect();
   *   
   *   Console::writeLine($conn->query('select @@version as version')->next('version'));
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.rdbms.DriverManagerTest
   * @purpose  Manager
   */
  class DriverManager extends Object {
    protected static 
      $instance     = NULL;

    public
      $drivers  = array();

    static function __static() {
      self::$instance= new self();

      // MySQL support: Use mysql extension by default, mysqli otherwise
      if (extension_loaded('mysql')) {
        self::$instance->drivers['mysql']= XPClass::forName('rdbms.mysql.MySQLConnection');
      } else if (extension_loaded('mysqli')) {
        self::$instance->drivers['mysql']= XPClass::forName('rdbms.mysqli.MySQLiConnection');
      }

      // PostgreSQL support
      if (extension_loaded('pgsql')) {
        self::$instance->drivers['pgsql']= XPClass::forName('rdbms.pgsql.PostgreSQLConnection');
      }
      
      // SQLite support
      if (extension_loaded('sqlite')) {
        self::$instance->drivers['sqlite']= XPClass::forName('rdbms.sqlite.SQLiteConnection');
      }
      
      // Sybase support: Prefer sybase_ct over mssql
      if (extension_loaded('sybase_ct')) {
        self::$instance->drivers['sybase']= XPClass::forName('rdbms.sybase.SybaseConnection');
      } else if (extension_loaded('mssql')) {
        self::$instance->drivers['sybase']= XPClass::forName('rdbms.mssql.MsSQLConnection');
      }
      
      // MSSQL support: Prefer SQLsrv from Microsoft over mssql 
      if (extension_loaded('sqlsrv')) {
        self::$instance->drivers['mssql']= XPClass::forName('rdbms.sqlsrv.SqlSrvConnection');
      } else if (extension_loaded('mssql')) {
        self::$instance->drivers['mssql']= XPClass::forName('rdbms.mssql.MsSQLConnection');
      }
      
      // Interbase support
      if (extension_loaded('interbase')) {
        self::$instance->drivers['ibase']= XPClass::forName('rdbms.ibase.InterBaseConnection');
      } 
    }
    
    /**
     * Constructor.
     *
     */
    protected function __construct() {
    }
      
    /**
     * Gets an instance
     *
     * @return  rdbms.DriverManager
     */
    public static function getInstance() {
      return self::$instance;
    }
  
    /**
     * Register a driver
     *
     * Usage:
     * <code>
     *   DriverManager::register('mydb', XPClass::forName('my.db.Connection'));
     *   // [...]
     *   $conn= DriverManager::getConnection('mydb://...');
     * </code>
     *
     * @param   string name identifier
     * @param   lang.XPClass<rdbms.DBConnection> class
     * @throws  lang.IllegalArgumentException in case an incorrect class is given
     */
    public static function register($name, XPClass $class) {
      if (!$class->isSubclassOf('rdbms.DBConnection')) {
        throw new IllegalArgumentException(sprintf(
          'Given argument must be lang.XPClass<rdbms.DBConnection>, %s given',
          $class->toString()
        ));
      }
      self::$instance->drivers[$name]= $class;
    }
    
    /**
     * Get a connection by a DSN string
     *
     * @param   string str
     * @return  rdbms.DBConnection
     * @throws  rdbms.DriverNotSupportedException
     */
    public static function getConnection($str) {
      $dsn= new DSN($str);
      $id= $dsn->getDriver();
      
      // Lookup driver by identifier.
      if (!isset(self::$instance->drivers[$id])) {
        throw new DriverNotSupportedException('No driver registered for '.$id.' - is the library loaded?');
      }
      
      return self::$instance->drivers[$id]->newInstance($dsn);
    }
  }
?>
