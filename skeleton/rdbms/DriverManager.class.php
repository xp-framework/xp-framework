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
    var
      $drivers  = array();
      
    /**
     * Gets an instance
     *
     * @model   static
     * @access  public
     * @return  &rdbms.DriverManager
     */
    function &getInstance() {
      static $instance= NULL;
      
      if (!$instance) {
        $instance= new DriverManager();

        if (extension_loaded('sybase_ct')) {
          $instance->drivers['sybase']= &XPClass::forName('rdbms.sybase.SybaseConnection');
        }
        if (extension_loaded('mysql')) {
          $instance->drivers['mysql']= &XPClass::forName('rdbms.mysql.MySQLConnection');
        }
        
        // TBI: postgres, oracle, ...
      }
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
    function register($name, &$class) {
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
    function &getConnection($str) {
      $dsn= &new DSN($str);
      $id= $dsn->getDriver();
      $i= &DriverManager::getInstance();
      
      // Lookup driver by identifier
      if (!isset($i->drivers[$id])) {
        return throw(new DriverNotSupportedException('No driver registered for '.$id));
      }
      
      return $i->drivers[$id]->newInstance($dsn);
    }
    
  }
?>
