<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.SQLException');
  
  define('DB_STORE_RESULT',     0x0001);
  define('DB_BUFFER_RESULTS',   0x0002);
  define('DB_AUTOCONNECT',      0x0004);
  
  /**
   * Base class for database connections
   *
   * @purpose  Provide an interface from which all other database connection
   *           classes extend
   */
  class DBConnection extends Object {
    var 
      $dsn    = NULL,
      $flags  = 0;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &rdbms.DSN dsn
     */
    function __construct(&$dsn) { 
      $this->dsn= &$dsn;
      parent::__construct();
    }
    
    /**
     * Set a flag
     *
     * @access  public
     * @param   int flag
     */
    function setFlag($flag) { 
      $this->flags |= $flag;
    }
    
    /**
     * Connect
     *
     * @access  public  
     * @return  bool success
     */
    function connect() { }
    
    /**
     * Disconnect
     *
     * @access  public
     * @return  bool success
     */
    function close() { }
    
    /**
     * Select database
     *
     * @access  public
     * @param   string db name of database to select
     * @return  bool success
     */
    function selectdb($db) { }
    
    /**
     * Seek
     *
     * @access  public
     * @param   resource set
     * @param   int offset
     * @return  bool success
     */
    function seek($set, $offset) { }

    /**
     * Prepare an SQL statement
     *
     * @access  public
     * @param   mixed* args
     * @return  string
     */
    function prepare() { }
    
    /**
     * Execute an insert statement
     *
     * @access  public
     * @param   mixed *args
     * @return  bool success
     */
    function insert() { }
    
    /**
     * Retreive identity
     *
     * @access  public
     * @return  mixed identity value
     */
    function identity() { }
    
    /**
     * Retreive affected rows
     *
     * @access  public
     * @return  int
     */
    function affected() { }
    
    /**
     * Retreive number of rows returned from last query
     *
     * @access  public
     * @return  int
     */
    function numrows() { }
    
    /**
     * Execute an update statement
     *
     * @access  public
     * @param   mixed* args
     * @return  bool success
     */
    function update() { }
    
    /**
     * Execute an update statement
     *
     * @access  public
     * @param   mixed* args
     * @return  bool success
     */
    function delete() { }
    
    /**
     * Execute a select statement
     *
     * @access  public
     * @param   mixed* args
     * @return  array rowsets
     */
    function select() { }
    
    /**
     * Execute any statement
     *
     * @access  public
     * @param   mixed* args
     * @return  resource set
     */
    function query() { }
    
    /**
     * Fetch a row (iterator function)
     *
     * @access  public
     * @param   resource set
     * @return  array rowset
     */
    function row($set) { }
    
    /**
     * Begin a transaction
     *
     * @access  public
     * @param   &rdbms.DBTransaction transaction
     * @return  &rdbms.DBTransaction
     */
    function &begin(&$transaction) {
      $transaction->db= &$this;
      return $transaction;
    }
    
    /**
     * Retreive transaction state
     *
     * @access  public
     * @param   string name
     * @return  mixed state
     */
    function transtate($name) { }
    
    /**
     * Rollback a transaction
     *
     * @access  public
     * @param   string name
     * @return  bool success
     */
    function rollback($name) { }
    
    /**
     * Commit a transaction
     *
     * @access  public
     * @param   string name
     * @return  bool success
     */
    function commit($name) { }
  }
?>
