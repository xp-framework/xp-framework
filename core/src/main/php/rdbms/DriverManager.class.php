<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.DBConnection', 'rdbms.DriverImplementationsProvider', 'rdbms.DriverNotSupportedException');

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
   * Driver preferences
   * ==================
   * Certain driver implementations may be preferred over others for performance
   * or conformity reasons - this is handled by the rdbms.DriverPreferences class 
   * for the framework's own implementations. This class is lazily loaded inside 
   * the <tt>getConnection()</tt> method and may thus be overridden by placing a 
   * userland class implementing rdbms.DriverImplementationsProvider upfront in
   * the classpath.
   *
   * @test     xp://net.xp_framework.unittest.rdbms.DriverManagerTest
   */
  class DriverManager extends Object {
    protected static $instance= NULL;
    public $drivers= array();

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
     * Remove a driver
     *
     * @param   string name
     */
    public static function remove($name) {
      unset(self::$instance->drivers[$name]);
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
      $driver= $dsn->getDriver();
      
      // Lookup driver by identifier, if no direct match is found, choose from 
      // the drivers with the same driver identifier. If no implementation can
      // be found that way, lazily load a class called rdbms.DriverPreferences,
      // verify it implements rdbms.DriverImplementationsProvider and then ask 
      // it for implementations once.
      if (!isset(self::$instance->drivers[$driver])) {
        $pref= NULL;
        do {
          $s= $driver.'+';
          $l= strlen($s);
          foreach (self::$instance->drivers as $name => $class) {
            if (0 !== strncmp($name, $s, $l)) continue;
            self::$instance->drivers[$driver]= $class;
            break 2;
          }

          if (NULL !== $pref) {
            $pref= NULL;
            throw new DriverNotSupportedException('No driver registered for '.$driver.' - is the library loaded?');
          }

          $pref= cast(XPClass::forName('rdbms.DriverPreferences')->newInstance(), 'rdbms.DriverImplementationsProvider');
          foreach ($pref->implementationsFor($driver) as $impl) {
            XPClass::forName($impl);
          }
        } while ($pref);
      }
      
      return self::$instance->drivers[$driver]->newInstance($dsn);
    }
  }
?>
