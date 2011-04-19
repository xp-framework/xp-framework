<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.DBConnection',
    'rdbms.Transaction',
    'rdbms.StatementFormatter',
    'rdbms.pgsql.PostgreSQLResultSet',
    'rdbms.pgsql.PostgreSQLDialect'
  );

  /**
   * Connection to PostgreSQL Databases
   *
   * @see      http://www.postgresql.org/
   * @see      http://www.freebsddiary.org/postgresql.php
   * @ext      pgsql
   * @purpose  Database connection
   */
  class PostgreSQLConnection extends DBConnection {
    protected
      $result     = NULL;

    /**
     * Constructor
     *
     * @param   rdbms.DSN dsn
     */
    public function __construct($dsn) {
      parent::__construct($dsn);
      $this->formatter= new StatementFormatter($this, new PostgreSQLDialect());
    }

    /**
     * Connect
     *
     * @param   bool reconnect default FALSE
     * @return  bool success
     * @throws  rdbms.SQLConnectException
     */
    public function connect($reconnect= FALSE) {
      if (is_resource($this->handle)) return TRUE;  // Already connected
      if (!$reconnect && (FALSE === $this->handle)) return FALSE;    // Previously failed connecting

      // Build connection string. In PostgreSQL, a dbname must _always_
      // be specified.
      $cs= 'dbname='.$this->dsn->getDatabase();
      if ($this->dsn->getHost()) $cs.= ' host='.$this->dsn->getHost();
      if ($this->dsn->getPort()) $cs.= ' port='.$this->dsn->getPort();
      if ($this->dsn->getUser()) $cs.= ' user='.$this->dsn->getUser();
      if ($this->dsn->getPassword()) $cs.= ' password='.$this->dsn->getPassword();

      if ($this->flags & DB_PERSISTENT) {
        $this->handle= pg_pconnect($cs);
      } else {
        $this->handle= pg_connect($cs);
      }

      if (!is_resource($this->handle)) {
        throw new SQLConnectException(rtrim(pg_last_error()), $this->dsn);
      }
      
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $reconnect));
      
      return TRUE;
    }
    
    /**
     * Disconnect
     *
     * @return  bool success
     */
    public function close() {
      if ($this->handle && $r= pg_close($this->handle)) {
        $this->handle= NULL;
        return $r;
      }
      return FALSE;
    }
    
    /**
     * Select database
     *
     * @param   string db name of database to select
     * @return  bool success
     * @throws  rdbms.SQLStatementFailedException
     */
    public function selectdb($db) {
      throw new SQLStatementFailedException(
        'Cannot select database, not implemented in PostgreSQL'
      );
    }

    /**
     * Retrieve identity
     *
     * @return  var identity value
     */
    public function identity($field= NULL) {
      $q= $this->query('select currval(%s) as id', $field);
      $id= $q ? $q->next('id') : NULL;
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $id));
      return $id;
    }

    /**
     * Retrieve number of affected rows for last query
     *
     * @return  int
     */
    protected function affectedRows() { 
      return pg_affected_rows($this->result);
    }
    
    /**
     * Execute any statement
     *
     * @param   string sql
     * @param   bool buffered default TRUE
     * @return  rdbms.pgsql.PostgreSQLResultSet or TRUE if no resultset was created
     * @throws  rdbms.SQLException
     */
    protected function query0($sql, $buffered= TRUE) {
      if (!is_resource($this->handle)) {
        if (!($this->flags & DB_AUTOCONNECT)) throw new SQLStateException('Not connected');
        $c= $this->connect();
        
        // Check for subsequent connection errors
        if (FALSE === $c) throw new SQLStateException('Previously failed to connect.');
      }

      $success= pg_send_query($this->handle, $sql);
      if (!$success) {
        $message= 'Statement failed: '.rtrim(pg_last_error($this->handle)).' @ '.$this->dsn->getHost();
        if (PGSQL_CONNECTION_OK !== pg_connection_status($this->handle)) {
          throw new SQLConnectionClosedException($message, $sql);
        } else {
          throw new SQLStatementFailedException($message, $sql);
        }
      }
      
      $this->result= pg_get_result($this->handle);
      switch ($status= pg_result_status($this->result, PGSQL_STATUS_LONG)) {
        case PGSQL_FATAL_ERROR:
        case PGSQL_BAD_RESPONSE: {
          $code= pg_result_error_field($this->result, PGSQL_DIAG_SQLSTATE);
          $message= 'Statement failed: '.pg_result_error_field($this->result, PGSQL_DIAG_MESSAGE_PRIMARY).' @ '.$this->dsn->getHost();
          if ('40P01' === $code) {
            throw new SQLDeadlockException($message, $sql, $code);
          } else {
            throw new SQLStatementFailedException($message, $sql, $code);
          }
        }
        
        case PGSQL_COMMAND_OK: {
          return TRUE;
        }
        
        default: {
          return new PostgreSQLResultSet($this->result, $this->tz);
        }
      }
    }
    
    /**
     * Begin a transaction
     *
     * @param   rdbms.Transaction transaction
     * @return  rdbms.Transaction
     */
    public function begin($transaction) {
      $this->query('begin transaction');
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
      return -1;
    }
    
    /**
     * Rollback a transaction
     *
     * @param   string name
     * @return  bool success
     */
    public function rollback($name) { 
      return $this->query('rollback transaction');
    }
    
    /**
     * Commit a transaction
     *
     * @param   string name
     * @return  bool success
     */
    public function commit($name) { 
      return $this->query('commit transaction');
    }
  }
?>
