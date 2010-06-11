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
    'rdbms.SQLDeadlockException',
    'rdbms.DBEvent',
    'rdbms.DSN',
    'rdbms.ResultSet',
    'util.log.Logger',
    'util.Observable',
    'util.TimeZone'
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
      $tz      = NULL,
      $timeout = 0,
      $flags   = 0;
    
    protected
      $formatter= NULL;

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
      $observers= $dsn->getProperty('observer', array());
      if (NULL !== ($cat= $dsn->getProperty('log'))) { 
        $observers['util.log.LogObserver']= $cat; 
      }
      
      // Add observers
      foreach ($observers as $observer => $param) {
        $this->addObserver(XPClass::forName($observer)->getMethod('instanceFor')->invoke(NULL, array($param)));
      }

      // Time zone handling
      if ($tz= $dsn->getProperty('timezone', FALSE)) {
        $this->tz= new TimeZone($tz);
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
     * Creates a string representation of this connection
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s(->%s://%s%s@%s%s%s%s)',
        $this->getClassName(),
        $this->dsn->getDriver(),
        $this->dsn->getUser(),
        $this->dsn->getPassword() ? ':********' : '',
        $this->dsn->getHost(),
        $this->dsn->getPort() ? ':'.$this->dsn->getPort() : '',
        $this->tz ? ', tz='.$this->tz->toString() : '',
        $this->handle ? ', conn='.get_resource_type($this->handle).' #'.(int)$this->handle : ''
      );
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
     * @param   string fmt
     * @param   var* args
     * @return  string
     */
    public function prepare() {
      $args= func_get_args();
      return $this->formatter->format(array_shift($args), $args);
    }
    
    /**
     * Retrieve number of affected rows
     *
     * @return  int
     */
    protected function affectedRows() {}
    
    /**
     * Execute an insert statement
     *
     * @param   var* args
     * @return  int number of affected rows
     * @throws  rdbms.SQLStatementFailedException
     */
    public function insert() {
      $args= func_get_args();
      $args[0]= 'insert '.$args[0];
      call_user_func_array(array($this, 'query'), $args);
      return $this->affectedRows();
    }
    
    /**
     * Retrieve identity
     *
     * @return  var identity value
     */
    abstract public function identity($field= NULL);

    /**
     * Execute an update statement
     *
     * @param   var* args
     * @return  int number of affected rows
     * @throws  rdbms.SQLStatementFailedException
     */
    public function update() {
      $args= func_get_args();
      $args[0]= 'update '.$args[0];
      call_user_func_array(array($this, 'query'), $args);
      return $this->affectedRows();
    }
    
    /**
     * Execute an update statement
     *
     * @param   var* args
     * @return  int number of affected rows
     * @throws  rdbms.SQLStatementFailedException
     */
    public function delete() {
      $args= func_get_args();
      $args[0]= 'delete '.$args[0];
      call_user_func_array(array($this, 'query'), $args);
      return $this->affectedRows();
    }
    
    /**
     * Execute a select statement and return all rows as an array
     *
     * @param   var* args
     * @return  array rowsets
     * @throws  rdbms.SQLStatementFailedException
     */
    public function select() {
      $args= func_get_args();
      $args[0]= 'select '.$args[0];
      $r= call_user_func_array(array($this, 'query'), $args);

      $rows= array();
      while ($row= $r->next()) $rows[]= $row;
      return $rows;
    }

    /**
     * Execute any statement
     *
     * @param   var* args
     * @return  rdbms.ResultSet or TRUE if no resultset was created
     * @throws  rdbms.SQLException
     */
    public function query() { 
      $args= func_get_args();
      $sql= call_user_func_array(array($this, 'prepare'), $args);

      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $sql));
      $result= $this->query0($sql);
      $this->_obs && $this->notifyObservers(new DBEvent('queryend', $result));
      return $result;
    }

    /**
     * Execute any statement
     *
     * @param   var* args
     * @return  rdbms.ResultSet or TRUE if no resultset was created
     * @throws  rdbms.SQLException
     */
    public function open() { 
      $args= func_get_args();
      $sql= call_user_func_array(array($this, 'prepare'), $args);

      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $sql));
      $result= $this->query0($sql, FALSE);
      $this->_obs && $this->notifyObservers(new DBEvent('queryend', $result));
      return $result;
    }
    
    /**
     * Execute any statement
     *
     * @param   string sql
     * @param   bool buffered default TRUE
     * @return  rdbms.ResultSet or TRUE if no resultset was created
     * @throws  rdbms.SQLException
     */
    protected function query0($sql, $buffered= TRUE) {}
    
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
     * @return  var state
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
     * Retrieve SQL formatter
     *
     * @return  rdbms.StatementFormatter
     */
    public function getFormatter() {
      return $this->formatter;
    }
  }
?>
