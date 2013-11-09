<?php namespace net\xp_framework\unittest\rdbms\mock;

use rdbms\Transaction;
use rdbms\StatementFormatter;

/**
 * Mock database connection.
 *
 * @see      xp://rdbms.DBConnection
 * @purpose  Mock object
 */
class MockConnection extends \rdbms\DBConnection {
  public
    $affectedRows     = 1,
    $identityValue    = 1,
    $resultSets       = null,
    $queryError       = array(),
    $connectError     = null,
    $currentResultSet = 0,
    $sql              = null;

  public
    $_connected       = false;

  /**
   * Constructor
   *
   * @param   rdbms.DSN dsn
   */
  public function __construct($dsn) { 
    parent::__construct($dsn);
    $this->formatter= new StatementFormatter($this, new MockDialect());
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
   * Mock: Get last query
   *
   * @return  string
   */
   public function getStatement() {
     return $this->sql;
   }

  /**
   * Connect
   *
   * @param   bool reconnect default FALSE
   * @return  bool success
   * @throws  rdbms.SQLConnectException
   */
  public function connect($reconnect= false) {
    $this->sql= null;
  
    if ($this->_connected && !$reconnect) return true;
    
    $this->_obs && $this->notifyObservers(new \rdbms\DBEvent(\rdbms\DBEvent::CONNECT, $reconnect));
    if ($this->connectError) {
      $this->_connected= false;
      throw new \rdbms\SQLConnectException($this->connectError, $this->dsn);
    }

    $this->_connected= true;
    $this->_obs && $this->notifyObservers(new \rdbms\DBEvent(\rdbms\DBEvent::CONNECTED, $reconnect));
    return true;
  }

  /**
   * Disconnect
   *
   * @return  bool success
   */
  public function close() {
    $this->_connected= false;
    return true;
  }

  /**
   * Select database
   *
   * @param   string db name of database to select
   * @return  bool success
   */
  public function selectdb($db) { 
    return true;
  }


  /**
   * Retrieve identity
   *
   * @return  mixed identity value
   */
  public function identity($field= null) {
    $this->_obs && $this->notifyObservers(new \rdbms\DBEvent(\rdbms\DBEvent::IDENTITY, $this->identityValue));
    return $this->identityValue;
  }

  /**
   * Retrieve number of affected rows for last query
   *
   * @return  int
   */
  protected function affectedRows() {
    return $this->affectedRows;
  }    
  
  /**
   * Execute any statement
   *
   * @param   string sql
   * @return  rdbms.ResultSet
   * @return  rdbms.ResultSet or TRUE if no resultset was created
   * @throws  rdbms.SQLException
   */
  protected function query0($sql, $buffered= true) { 
    if (!$this->_connected) {
      if (!($this->flags & DB_AUTOCONNECT)) throw new \rdbms\SQLStateException('Not connected');
      $c= $this->connect();
      
      // Check for subsequent connection errors
      if (false === $c) throw new \rdbms\SQLStateException('Previously failed to connect.');
    }
    
    $this->sql= $sql;

    switch (sizeof($this->queryError)) {
      case 0: {
        if ($this->currentResultSet >= sizeof($this->resultSets)) {
          return new MockResultSet();   // Empty
        }
        
        return $this->resultSets[$this->currentResultSet++];
      }

      case 1: {   // letServerDisconnect() sets this
        $this->queryError= array();
        $this->_connected= false;
        throw new \rdbms\SQLConnectionClosedException(
          'Statement failed: Read from server failed',
          $sql
        );
      }
      
      case 2: {   // makeQueryFail() sets this
        $error= $this->queryError;
        $this->queryError= array();       // Reset so next query succeeds again
        throw new \rdbms\SQLStatementFailedException(
          'Statement failed: '.$error[1],
          $sql, 
          $error[0]
        );
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
}
