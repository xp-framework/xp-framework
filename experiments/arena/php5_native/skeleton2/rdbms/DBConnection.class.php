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
    'rdbms.SQLConnectionClosedException',
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
    public 
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
    public function __construct(&$dsn) { 
      $this->dsn= &$dsn;
      $this->flags= $dsn->getFlags();
      $this->setTimeout($dsn->getProperty('timeout', 0));   // 0 means no timeout
      
      // Keep this for BC reasons
      $obs= $dsn->getProperty('observer', array());
      if (NULL !== ($cat= $dsn->getProperty('log'))) { 
        $obs['util.log.LogObserver']= $cat; 
      }
      
      // Add observers
      foreach (array_keys($obs) as $observer) {
        try {
          $class= &XPClass::forName($observer);
          $inst= &call_user_func(array(xp::reflect($class->getName()), 'instanceFor'), $obs[$observer]);
        } catch (ClassNotFoundException $e) {
          throw ($e);
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
    public function hashCode() {
      return get_resource_type($this->handle).' #'.(int)$this->handle;
    }

    /**
     * Set Timeout
     *
     * @access  public
     * @param   int timeout
     */
    public function setTimeout($timeout) {
      $this->timeout= $timeout;
    }

    /**
     * Get Timeout
     *
     * @access  public
     * @return  int
     */
    public function getTimeout() {
      return $this->timeout;
    }
    
    /**
     * Set a flag
     *
     * @access  public
     * @param   int flag
     */
    public function setFlag($flag) { 
      $this->flags |= $flag;
    }
    
    /**
     * Connect
     *
     * @access  public
     * @return  bool success
     */
    public function connect() { 
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
    public function hasChanged() {
      return TRUE;
    }

    /**
     * Disconnect
     *
     * @access  public
     * @return  bool success
     */
    public function close() { }
    
    /**
     * Select database
     *
     * @access  public
     * @param   string db name of database to select
     * @return  bool success
     */
    public function selectdb($db) { }

    /**
     * Prepare an SQL statement
     *
     * @access  public
     * @param   mixed* args
     * @return  string
     */
    public function prepare() { }
    
    /**
     * Execute an insert statement
     *
     * @access  public
     * @param   mixed* args
     * @return  bool success
     */
    public function insert() { }
    
    /**
     * Retrieve identity
     *
     * @access  public
     * @return  mixed identity value
     */
    public function identity() { }
    
    /**
     * Execute an update statement
     *
     * @access  public
     * @param   mixed* args
     * @return  int number of affected rows
     */
    public function update() { }
    
    /**
     * Execute an update statement
     *
     * @access  public
     * @param   mixed* args
     * @return  int number of affected rows
     */
    public function delete() { }
    
    /**
     * Execute a select statement
     *
     * @access  public
     * @param   mixed* args
     * @return  array rowsets
     */
    public function select() { }
    
    /**
     * Execute any statement
     *
     * @access  public
     * @param   mixed* args
     * @return  &rdbms.ResultSet
     */
    public function &query() { }
    
    /**
     * Begin a transaction
     *
     * @access  public
     * @param   &rdbms.DBTransaction transaction
     * @return  &rdbms.DBTransaction
     */
    public function &begin(&$transaction) { }
    
    /**
     * Retrieve transaction state
     *
     * @access  public
     * @param   string name
     * @return  mixed state
     */
    public function transtate($name) { }
    
    /**
     * Rollback a transaction
     *
     * @access  public
     * @param   string name
     * @return  bool success
     */
    public function rollback($name) { }
    
    /**
     * Commit a transaction
     *
     * @access  public
     * @param   string name
     * @return  bool success
     */
    public function commit($name) { }
  }
?>
