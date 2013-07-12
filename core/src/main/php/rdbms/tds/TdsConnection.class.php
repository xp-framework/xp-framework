<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.DBConnection',
    'rdbms.tds.TdsResultSet',
    'rdbms.tds.TdsBufferedResultSet',
    'rdbms.tds.TdsV7Protocol',
    'rdbms.Transaction',
    'rdbms.StatementFormatter',
    'rdbms.mssql.MsSQLDialect'
  );

  /**
   * Connection to MSSQL Databases via TDS
   *
   * @test     xp://net.xp_framework.unittest.rdbms.TokenizerTest
   * @test     xp://net.xp_framework.unittest.rdbms.DBTest
   * @purpose  Database connection
   */
  abstract class TdsConnection extends DBConnection {
    protected $affected= -1;

    /**
     * Constructor
     *
     * @param   rdbms.DSN dsn
     */
    public function __construct($dsn) { 
      parent::__construct($dsn);
      $this->formatter= new StatementFormatter($this, $this->getDialect());
      $this->handle= $this->getProtocol(new Socket($this->dsn->getHost(), $this->dsn->getPort(1433)));
    }
    
    /**
     * Returns dialect
     *
     * @return  rdbms.SQLDialect
     */
    protected abstract function getDialect();
    
    /**
     * Returns protocol
     *
     * @param   peer.Socket sock
     * @return  rdbms.tds.TdsProtocol
     */
    protected abstract function getProtocol($sock);

    /**
     * Connect
     *
     * @param   bool reconnect default FALSE
     * @return  bool success
     * @throws  rdbms.SQLConnectException
     */
    public function connect($reconnect= FALSE) {
      if ($this->handle->connected) return TRUE;                    // Already connected
      if (!$reconnect && (NULL === $this->handle->connected)) return FALSE;   // Previously failed connecting

      $this->_obs && $this->notifyObservers(new DBEvent(DBEvent::CONNECT, $reconnect));
      try {
        $this->handle->connect($this->dsn->getUser(), $this->dsn->getPassword());
        $this->_obs && $this->notifyObservers(new DBEvent(DBEvent::CONNECTED, $reconnect));
      } catch (IOException $e) {
        $this->handle->connected= NULL;
        $this->_obs && $this->notifyObservers(new DBEvent(DBEvent::CONNECTED, $reconnect));
        throw new SQLConnectException($e->getMessage(), $this->dsn);
      }

      return parent::connect();
    }
    
    /**
     * Disconnect
     *
     * @return  bool success
     */
    public function close() {
      if (!$this->handle->connected) return FALSE;
      $this->handle->close();
      return TRUE;
    }
    
    /**
     * Select database
     *
     * @param   string db name of database to select
     * @return  bool success
     * @throws  rdbms.SQLStatementFailedException
     */
    public function selectdb($db) {
      try {
        $this->handle->exec('use '.$db);
        return TRUE;
      } catch (IOException $e) {
        throw new SQLStatementFailedException($e->getMessage());
      }
    }

    /**
     * Retrieve identity
     *
     * @return  var identity value
     */
    public function identity($field= NULL) {
      $i= $this->query('select @@identity as xp_id')->next('xp_id');
      $this->_obs && $this->notifyObservers(new DBEvent(DBEvent::IDENTITY, $i));
      return $i;
    }

    /**
     * Retrieve number of affected rows for last query
     *
     * @return  int
     */
    protected function affectedRows() {
      return $this->affected;
    }    
    
    /**
     * Execute any statement
     *
     * @param   string sql
     * @param   bool buffered default TRUE
     * @return  rdbms.ResultSet or TRUE if no resultset was created
     * @throws  rdbms.SQLException
     */
    protected function query0($sql, $buffered= TRUE) {
      if (!$this->handle->connected) {
        if (!($this->flags & DB_AUTOCONNECT)) throw new SQLStateException('Not connected');
        $c= $this->connect();
        
        // Check for subsequent connection errors
        if (FALSE === $c) throw new SQLStateException('Previously failed to connect.');
      }
      
      try {
        $this->handle->ready() || $this->handle->cancel();
        $result= $this->handle->query($sql);
      } catch (TdsProtocolException $e) {
        $message= $e->getMessage().' (number '.$e->number.')';
        switch ($e->number) {
          case 1205: // Deadlock
            throw new SQLDeadlockException($message, $sql, $e->number);
          
          default:   // Other error
            throw new SQLStatementFailedException($message, $sql, $e->number);
        }
      } catch (IOException $e) {
        throw new SQLStatementFailedException($e->getMessage());
      }
      
      if (!is_array($result)) {
        $this->affected= $result;
        return TRUE;
      }

      $this->affected= -1;
      if (!$buffered || $this->flags & DB_UNBUFFERED) {
        return new TdsResultSet($this->handle, $result, $this->tz);
      } else {
        return new TdsBufferedResultSet($this->handle, $result, $this->tz);
      }
    }

    /**
     * Begin a transaction
     *
     * @param   rdbms.Transaction transaction
     * @return  rdbms.Transaction
     */
    public function begin($transaction) {
      $this->query('begin transaction xp_%c', $transaction->name);
      $transaction->db= $this;
      return $transaction;
    }

    /**
     * Retrieve transaction state
     *
     * @param   string name
     * @return  var state
     */
    public function transtate($name) { 
      return $this->query('select @@transtate as transtate')->next('transtate');
    }
    
    /**
     * Rollback a transaction
     *
     * @param   string name
     * @return  bool success
     */
    public function rollback($name) { 
      return $this->query('rollback transaction xp_%c', $name);
    }
    
    /**
     * Commit a transaction
     *
     * @param   string name
     * @return  bool success
     */
    public function commit($name) { 
      return $this->query('commit transaction xp_%c', $name);
    }

    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'(->'.$this->dsn->toString().', '.$this->handle->toString().')';
    }
  }
?>
