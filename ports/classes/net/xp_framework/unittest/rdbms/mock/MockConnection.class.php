<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.Transaction',
    'rdbms.DBConnection', 
    'rdbms.StatementFormatter',
    'rdbms.sybase.SybaseDialect',
    'net.xp_framework.unittest.rdbms.mock.MockResultSet'
  );

  /**
   * Mock database connection. Uses the SYBASE dialect!
   *
   * @see      xp://rdbms.DBConnection
   * @purpose  Mock object
   */
  class MockConnection extends DBConnection {
    public
      $affectedRows     = 1,
      $identityValue    = 1,
      $resultSets       = NULL,
      $queryError       = array(),
      $connectError     = NULL,
      $currentResultSet = 0;

    public
      $_connected       = FALSE;

    private
      $formatter= NULL;

    /**
     * Constructor
     *
     * @param   rdbms.DSN dsn
     */
    public function __construct($dsn) { 
      parent::__construct($dsn);
      $this->clearResultSets();
    }

    /**
     * Mock: Set ResultSet as only result set
     *
     * @param   net.xp_framework.unittest.rdbms.mock.MockResultSet resultSet
     */
    public function setResultSet($resultSet) {
      $this->queryError= array();
      $this->resultSets= array($resultSet);
      $this->currentResultSet= 0;
    }

    /**
     * Mock: Clear ResultSets
     *
     * Example:
     * <code>
     *   $conn->clearResultSets()->addResultSet(...)->addResultSet(...);
     * </code>
     *
     * @return  net.xp_framework.unittest.rdbms.mock.MockConnection this
     */
    public function clearResultSets() {
      $this->queryError= array();
      $this->resultSets= array();
      $this->currentResultSet= 0;
      return $this;
    }

    /**
     * Mock: Add ResultSet
     *
     * @param   net.xp_framework.unittest.rdbms.mock.MockResultSet resultSet
     * @return  net.xp_framework.unittest.rdbms.mock.MockConnection this
     */
    public function addResultSet($resultSet) {
      $this->queryError= array();
      $this->resultSets[]= $resultSet;
      return $this;
    }

    /**
     * Mock: Get ResultSet
     *
     * @return  net.xp_framework.unittest.rdbms.mock.MockResultSet
     */
    public function getResultSets() {
      return $this->resultSets;
    }

    /**
     * Mock: Make next query fail
     *
     * @param   int errNo
     * @param   int errMsg
     */
    public function makeQueryFail($errNo, $errMsg) {
      $this->queryError= array($errNo, $errMsg);
    }

    /**
     * Mock: Let server disconnect. This will make query() thrown
     *
     */
    public function letServerDisconnect() {
      $this->queryError= array(2013);
    }

    /**
     * Mock: Make connect fail
     *
     * @param   int errMsg
     */
    public function makeConnectFail($errMsg) {
      $this->connectError= $errMsg;
    }

    /**
     * Mock: Set IdentityValue
     *
     * @param   mixed identityValue
     */
    public function setIdentityValue($identityValue) {
      $this->identityValue= $identityValue;
    }

    /**
     * Mock: Get IdentityValue
     *
     * @return  mixed
     */
    public function getIdentityValue() {
      return $this->identityValue;
    }

    /**
     * Mock: Set AffectedRows
     *
     * @param   int affectedRows
     */
    public function setAffectedRows($affectedRows) {
      $this->affectedRows= $affectedRows;
    }

    /**
     * Mock: Get AffectedRows
     *
     * @return  int
     */
    public function getAffectedRows() {
      return $this->affectedRows;
    }

    /**
     * Connect
     *
     * @param   bool reconnect default FALSE
     * @return  bool success
     * @throws  rdbms.SQLConnectException
     */
    public function connect($reconnect= FALSE) {
      if ($this->_connected && !$reconnect) return TRUE;
      
      if ($this->connectError) {
        $this->_connected= FALSE;
        throw new SQLConnectException($this->connectError, $this->dsn);
      }

      $this->_connected= TRUE;
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $reconnect));
      return TRUE;
    }

    /**
     * Disconnect
     *
     * @return  bool success
     */
    public function close() {
      $this->_connected= FALSE;
      return TRUE;
    }

    /**
     * Select database
     *
     * @param   string db name of database to select
     * @return  bool success
     */
    public function selectdb($db) { 
      return TRUE;
    }

    /**
     * Prepare an SQL statement
     *
     * @param   mixed* args
     * @return  string
     */
    public function prepare() { 
      $args= func_get_args();
      return $this->getFormatter()->format(array_shift($args), $args);
    }
    
    /**
     * Execute an insert statement
     *
     * @param   mixed* args
     * @return  bool success
     */
    public function insert() { 
      $args= func_get_args();
      $args[0]= 'insert '.$args[0];
      if (!($r= call_user_func_array(array($this, 'query'), $args))) {
        return FALSE;
      }
      
      return $this->affectedRows;
    }
    
    /**
     * Retrieve identity
     *
     * @return  mixed identity value
     */
    public function identity($field= NULL) {
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $this->identityValue));
      return $this->identityValue;
    }
    
    /**
     * Execute an update statement
     *
     * @param   mixed* args
     * @return  int number of affected rows
     */
    public function update() {
      $args= func_get_args();
      $args[0]= 'update '.$args[0];
      if (!($r= call_user_func_array(array($this, 'query'), $args))) {
        return FALSE;
      }
      
      return $this->affectedRows;
    }
    
    /**
     * Execute an update statement
     *
     * @param   mixed* args
     * @return  int number of affected rows
     */
    public function delete() {
      $args= func_get_args();
      $args[0]= 'delete '.$args[0];
      if (!($r= call_user_func_array(array($this, 'query'), $args))) {
        return FALSE;
      }
      
      return $this->affectedRows;
    }
    
    /**
     * Execute a select statement
     *
     * @param   mixed* args
     * @return  array rowsets
     */
    public function select() { 
      $args= func_get_args();
      $args[0]= 'select '.$args[0];
      if (!($r= call_user_func_array(array($this, 'query'), $args))) {
        return FALSE;
      }
      
      $rows= array();
      while ($row= $r->next()) $rows[]= $row;
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, sizeof ($rows)));
      return $rows;
    }
    
    /**
     * Execute any statement
     *
     * @param   mixed* args
     * @return  rdbms.ResultSet
     */
    public function query() { 
      $args= func_get_args();
      $sql= call_user_func_array(array($this, 'prepare'), $args);

      if (!$this->_connected) {
        if (!($this->flags & DB_AUTOCONNECT)) throw(new SQLStateException('Not connected'));
        try {
          $c= $this->connect();
        } catch (SQLException $e) {
          throw ($e);
        }
        
        // Check for subsequent connection errors
        if (FALSE === $c) throw(new SQLStateException('Previously failed to connect.'));
      }

      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $sql));

      switch (sizeof($this->queryError)) {
        case 0: {
          if ($this->currentResultSet >= sizeof($this->resultSets)) {
            return new MockResultSet();   // Empty
          }
          
          $resultset= $this->resultSets[$this->currentResultSet++];
          $this->_obs && $this->notifyObservers(new DBEvent('queryend', $resultset));
          return $resultset;
        }

        case 1: {   // letServerDisconnect() sets this
          $this->queryError= array();
          $this->_connected= FALSE;
          throw(new SQLConnectionClosedException(
            'Statement failed: Read from server failed',
            $sql
          ));
        }
        
        case 2: {   // makeQueryFail() sets this
          $error= $this->queryError;
          $this->queryError= array();       // Reset so next query succeeds again
          throw(new SQLStatementFailedException(
            'Statement failed: '.$error[1],
            $sql, 
            $error[0]
          ));
        }
      }
    }
    
    /**
     * Begin a transaction
     *
     * @param   rdbms.DBTransaction transaction
     * @return  rdbms.DBTransaction
     */
    public function begin($transaction) {
      $transaction->db= $this;
      return $transaction;
    }
    
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
    public function rollback($name) { }
    
    /**
     * Commit a transaction
     *
     * @param   string name
     * @return  bool success
     */
    public function commit($name) { }
  
    /**
     * get SQL formatter
     *
     * @return  rdbms.StatemantFormatter
     */
    public function getFormatter() {
      if (NULL === $this->formatter) $this->formatter= new StatementFormatter($this, new SybaseDialect());
      return $this->formatter;
    }
  }
?>
