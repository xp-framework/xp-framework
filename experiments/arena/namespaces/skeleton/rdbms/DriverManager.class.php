<?php
/* This class is part of the XP framework
 *
 * $Id: DriverManager.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace rdbms;

  uses('rdbms.DBConnection', 'rdbms.DriverNotSupportedException');

  /**
   * Manages database drivers
   *
   * Usage:
   * <code>
   *   uses('rdbms.DriverManager');
   *
   *   $conn= DriverManager::getConnection('sybase://user:pass@server');
   *   try {
   *     $conn->connect();
   *     $r= $conn->query('select @@version as version');
   *   } catch (SQLException $e) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *   var_dump($r->next('version'));
   * </code>
   *
   * @purpose  Manager
   */
  class DriverManager extends lang::Object {
    protected static 
      $instance     = NULL;

    public
      $drivers  = array();

    static function __static() {
      self::$instance= new self();
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
     *   $conn= &DriverManager::getConnection('mydb://...');
     * </code>
     *
     * @param   string name identifier
     * @param   lang.XPClass class
     */
    public static function register($name, $class) {
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
      static $builtin= array(
        'sybase'   => 'rdbms.sybase.SybaseConnection',
        'mysql'    => 'rdbms.mysql.MySQLConnection',
        'pgsql'    => 'rdbms.pgsql.PostgreSQLConnection',
        'sqlite'   => 'rdbms.sqlite.SQLiteConnection',
        // TBI: Oracle, ...
      );
      
      $dsn= new DSN($str);
      $id= $dsn->getDriver();
      
      // Lookup driver by identifier.
      if (!isset(self::$instance->drivers[$id])) {
        try {
          self::$instance->drivers[$id]= lang::XPClass::forName($builtin[$id]);
        } catch (lang::ClassNotFoundException $e) {
          throw new DriverNotSupportedException(
            'No driver registered for '.$id.': '.$e->getMessage()
          );
        }
      }
      
      return self::$instance->drivers[$id]->newInstance($dsn);
    }
  }
?>
