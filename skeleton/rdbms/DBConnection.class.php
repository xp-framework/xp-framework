<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'rdbms.SQLException',
    'rdbms.SQLConnectException',
    'rdbms.SQLStateException',
    'rdbms.SQLStatementFailedException',
    'rdbms.DBEvent',
    'rdbms.DSN',
    'rdbms.ResultSet',
    'util.log.Logger',
    'util.Observable'
  );
  
  /**
   * Provide an interface from which all other database connection
   * classes extend.
   *
   * @purpose  Base class for database connections
   */
  class DBConnection extends Observable {
    var 
      $handle  = NULL,
      $dsn     = NULL,
      $timeout = 0,
      $flags   = 0;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &rdbms.DSN dsn
     */
    function __construct(&$dsn) { 
      $this->dsn= &$dsn;
      $this->flags= $dsn->getFlags();
      $this->setTimeout($dsn->getProperty('timeout', 0));   // 0 means no timeout
      
      // Keep this for BC reasons
      $obs= $this->dsn->getValue('observer', array());
      if (NULL !== ($cat= $this->dsn->getValue('log'))) { 
        $obs['util.log.LogObserver']= $cat; 
      }
      
      // Add observers
      foreach (array_keys($obs) as $observer) {
        try(); {
          $class= &XPClass::forName($observer);
          $inst= &call_user_func(array(xp::reflect($class->getName()), 'instanceFor'), $obs[$observer]);
        } if (catch('ClassNotFoundException', $e)) {
          return throw ($e);
        }

        $this->addObserver($inst);
      }
    }
    
    /**
     * Returns a hashcode for this connection
     *
     * Example:
     * <pre>
     *   sybase-ct link #50
     * </pre>
     *
     * @access  public
     * @return  string
     */
    function hashCode() {
      return get_resource_type($this->handle).' #'.(int)$this->handle;
    }

    /**
     * Set Timeout
     *
     * @access  public
     * @param   int timeout
     */
    function setTimeout($timeout) {
      $this->timeout= $timeout;
    }

    /**
     * Get Timeout
     *
     * @access  public
     * @return  int
     */
    function getTimeout() {
      return $this->timeout;
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
    function connect() { 
      if ($db= $this->dsn->getDatabase()) {
        return $this->selectdb($db);
      }
      
      return TRUE;
    }
    
    /**
     * Checks whether changed flag is set
     *
     * @access  public
     * @return  bool
     */
    function hasChanged() {
      return TRUE;
    }

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
     * @param   mixed* args
     * @return  bool success
     */
    function insert() { }
    
    /**
     * Retrieve identity
     *
     * @access  public
     * @return  mixed identity value
     */
    function identity() { }
    
    /**
     * Execute an update statement
     *
     * @access  public
     * @param   mixed* args
     * @return  int number of affected rows
     */
    function update() { }
    
    /**
     * Execute an update statement
     *
     * @access  public
     * @param   mixed* args
     * @return  int number of affected rows
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
     * @return  &rdbms.ResultSet
     */
    function &query() { }
    
    /**
     * Begin a transaction
     *
     * @access  public
     * @param   &rdbms.DBTransaction transaction
     * @return  &rdbms.DBTransaction
     */
    function &begin(&$transaction) { }
    
    /**
     * Retrieve transaction state
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
