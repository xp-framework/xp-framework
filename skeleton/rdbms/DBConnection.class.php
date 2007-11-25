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
  abstract class DBConnection extends Observable {
    public 
      $handle  = NULL,
      $dsn     = NULL,
      $timeout = 0,
      $flags   = 0;
    
    /**
     * Constructor
     *
     * @param   rdbms.DSN dsn
     */
    public function __construct($dsn) { 
      $this->dsn= $dsn;
      $this->flags= $dsn->getFlags();
      $this->setTimeout($dsn->getProperty('timeout', 0));   // 0 means no timeout
      
      // Keep this for BC reasons
      $obs= $dsn->getProperty('observer', array());
      if (NULL !== ($cat= $dsn->getProperty('log'))) { 
        $obs['util.log.LogObserver']= $cat; 
      }
      
      // Add observers
      foreach (array_keys($obs) as $observer) {
        $class= XPClass::forName($observer);
        $inst= call_user_func(array(xp::reflect($class->getName()), 'instanceFor'), $obs[$observer]);
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
     * @return  string
     */
    public function hashCode() {
      return get_resource_type($this->handle).' #'.(int)$this->handle;
    }

    /**
     * Set Timeout
     *
     * @param   int timeout
     */
    public function setTimeout($timeout) {
      $this->timeout= $timeout;
    }

    /**
     * Get Timeout
     *
     * @return  int
     */
    public function getTimeout() {
      return $this->timeout;
    }
    
    /**
     * Set a flag
     *
     * @param   int flag
     */
    public function setFlag($flag) { 
      $this->flags |= $flag;
    }
    
    /**
     * Connect
     *
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
     * @return  bool
     */
    public function hasChanged() {
      return TRUE;
    }

    /**
     * Disconnect
     *
     * @return  bool success
     */
    abstract public function close();
    
    /**
     * Select database
     *
     * @param   string db name of database to select
     * @return  bool success
     */
    abstract public function selectdb($db);

    /**
     * Prepare an SQL statement
     *
     * @param   mixed* args
     * @return  string
     */
    abstract public function prepare();
    
    /**
     * Execute an insert statement
     *
     * @param   mixed* args
     * @return  bool success
     */
    abstract public function insert();
    
    /**
     * Retrieve identity
     *
     * @return  mixed identity value
     */
    abstract public function identity($field= NULL);

    /**
     * Execute an update statement
     *
     * @param   mixed* args
     * @return  int number of affected rows
     */
    public function update() { }
    
    /**
     * Execute an update statement
     *
     * @param   mixed* args
     * @return  int number of affected rows
     */
    public function delete() { }
    
    /**
     * Execute a select statement
     *
     * @param   mixed* args
     * @return  array rowsets
     */
    public function select() { }
    
    /**
     * Execute any statement
     *
     * @param   mixed* args
     * @return  rdbms.ResultSet
     */
    public function query() { }
    
    /**
     * Begin a transaction
     *
     * @param   rdbms.DBTransaction transaction
     * @return  rdbms.DBTransaction
     */
    abstract public function begin($transaction);
    
    /**
     * Retrieve transaction state
     *
     * @param   string name
     * @return  mixed state
     */
    public function transtate($name) { }
    
    /**
     * Rollback a transaction
     *
     * @param   string name
     * @return  bool success
     */
    abstract public function rollback($name);
    
    /**
     * Commit a transaction
     *
     * @param   string name
     * @return  bool success
     */
    abstract public function commit($name);
    
    /**
     * get SQL formatter
     *
     * @return  rdbms.StatemantFormatter
     */
    abstract public function getFormatter();
  }
?>
