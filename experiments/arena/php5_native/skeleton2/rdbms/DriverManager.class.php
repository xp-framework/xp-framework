<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.DBConnection', 'rdbms.DriverNotSupportedException');

  /**
   * Manages database drivers
   *
   * Usage:
   * <code>
   *   uses('rdbms.DriverManager');
   *
   *   $conn= &DriverManager::getConnection('sybase://user:pass@server');
   *   try(); {
   *     $conn->connect();
   *     $r= &$conn->query('select @@version as version');
   *   } if (catch('SQLException', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *   var_dump($r->next('version'));
   * </code>
   *
   * @purpose  Manager
   */
  class DriverManager extends Object {
    public
      $drivers  = array();
      
    /**
     * Gets an instance
     *
     * @model   static
     * @access  public
     * @return  &rdbms.DriverManager
     */
    public static function &getInstance() {
      static $instance= NULL;
      
      if (!$instance) $instance= new DriverManager();
      return $instance;
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
     * @model   static
     * @access  public
     * @param   string name identifier
     * @param   &lang.XPClass class
     */
    public static function register($name, &$class) {
      $i= &DriverManager::getInstance();
      $i->drivers[$name]= &$class;
    }
    
    /**
     * Get a connection by a DSN string
     *
     * @model   static
     * @access  public
     * @param   string str
     * @return  &rdbms.DBConnection
     * @throws  rdbms.DriverNotSupportedException
     */
    public static function &getConnection($str) {
      static $builtin= array(
        'sybase'   => 'rdbms.sybase.SybaseConnection',
        'mysql'    => 'rdbms.mysql.MySQLConnection',
        'pgsql'    => 'rdbms.pgsql.PostgreSQLConnection',
        'sqlite'   => 'rdbms.sqlite.SQLiteConnection',
        // TBI: Oracle, ...
      );
      
      $dsn= &new DSN($str);
      $id= $dsn->getDriver();
      $i= &DriverManager::getInstance();
      
      // Lookup driver by identifier. If it's one
      if (!isset($i->drivers[$id])) {
        try {
          $i->drivers[$id]= &XPClass::forName($builtin[$id]);
        } catch (ClassNotFoundException $e) {
          throw(new DriverNotSupportedException(
            'No driver registered for '.$id.': '.$e->getMessage()
          ));
        }
      }
      
      return $i->drivers[$id]->newInstance($dsn);
    }
  }
?>
