<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.Transaction',
    'rdbms.DBConnection', 
    'rdbms.StatementFormatter',
    'net.xp_framework.unittest.rdbms.mock.MockResultSet',
    'net.xp_framework.unittest.rdbms.mock.MockDialect'
  );

  /**
   * Mock database connection.
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
     * Retrieve identity
     *
     * @return  mixed identity value
     */
    public function identity($field= NULL) {
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $this->identityValue));
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
     * @throws  rdbms.SQLException
     */
    protected function query0($sql) { 
      if (!$this->_connected) {
        if (!($this->flags & DB_AUTOCONNECT)) throw new SQLStateException('Not connected');
        $c= $this->connect();
        
        // Check for subsequent connection errors
        if (FALSE === $c) throw new SQLStateException('Previously failed to connect.');
      }

      switch (sizeof($this->queryError)) {
        case 0: {
          if ($this->currentResultSet >= sizeof($this->resultSets)) {
            return new MockResultSet();   // Empty
          }
          
          return $this->resultSets[$this->currentResultSet++];
        }

        case 1: {   // letServerDisconnect() sets this
          $this->queryError= array();
          $this->_connected= FALSE;
          throw new SQLConnectionClosedException(
            'Statement failed: Read from server failed',
            $sql
          );
        }
        
        case 2: {   // makeQueryFail() sets this
          $error= $this->queryError;
          $this->queryError= array();       // Reset so next query succeeds again
          throw new SQLStatementFailedException(
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
?>
