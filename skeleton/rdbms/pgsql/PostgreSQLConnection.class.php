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
     private
       $formatter= NULL;

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
     * Retrieve identity
     *
     * @return  mixed identity value
     */
    public function identity($field= NULL) {
      $q= $this->query('select currval(%s) as id', $field);
      $id= $q ? $q->next('id') : NULL;
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $id));
      return $id;
    }

    /**
     * Execute an insert statement
     *
     * @param   mixed* args
     * @return  int number of affected rows
     * @throws  rdbms.SQLStatementFailedException
     */
    public function insert() { 
      $args= func_get_args();
      $args[0]= 'insert '.$args[0];
      if (!($r= call_user_func_array(array($this, 'query'), $args))) {
        return FALSE;
      }

      return pg_affected_rows($this->lastresult);
    }
    
    /**
     * Execute an update statement
     *
     * @param   mixed* args
     * @return  int number of affected rows
     * @throws  rdbms.SQLStatementFailedException
     */
    public function update() {
      $args= func_get_args();
      $args[0]= 'update '.$args[0];
      if (!($r= call_user_func_array(array($this, 'query'), $args))) {
        return FALSE;
      }
      
      return pg_affected_rows($this->lastresult);
    }
    
    /**
     * Execute an update statement
     *
     * @param   mixed* args
     * @return  int number of affected rows
     * @throws  rdbms.SQLStatementFailedException
     */
    public function delete() { 
      $args= func_get_args();
      $args[0]= 'delete '.$args[0];
      if (!($r= call_user_func_array(array($this, 'query'), $args))) {
        return FALSE;
      }
      
      return pg_affected_rows($this->lastresult);
    }
    
    /**
     * Execute a select statement and return all rows as an array
     *
     * @param   mixed* args
     * @return  array rowsets
     * @throws  rdbms.SQLStatementFailedException
     */
    public function select() { 
      $args= func_get_args();
      $args[0]= 'select '.$args[0];
      if (!($r= call_user_func_array(array($this, 'query'), $args))) {
        return FALSE;
      }
      
      $rows= array();
      while ($row= $r->next()) $rows[]= $row;
      return $rows;
    }
    
    /**
     * Execute any statement
     *
     * @param   mixed* args
     * @return  rdbms.pgsql.PostgreSQLResultSet or FALSE to indicate failure
     * @throws  rdbms.SQLException
     */
    public function query() { 
      $args= func_get_args();
      $sql= call_user_func_array(array($this, 'prepare'), $args);

      if (!is_resource($this->handle)) {
        if (!($this->flags & DB_AUTOCONNECT)) throw new SQLStateException('Not connected');
        $c= $this->connect();
        
        // Check for subsequent connection errors
        if (FALSE === $c) throw new SQLStateException('Previously failed to connect.');
      }
      
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $sql));

      $success= pg_send_query($this->handle, $sql);
      if (!$success) {
        $message= 'Statement failed: '.rtrim(pg_last_error($this->handle)).' @ '.$this->dsn->getHost();
        if (PGSQL_CONNECTION_OK !== pg_connection_status($this->handle)) {
          throw new SQLConnectionClosedException($message, $sql);
        } else {
          throw new SQLStatementFailedException($message, $sql);
        }
      }
      
      $this->lastresult= pg_get_result($this->handle);
      switch ($status= pg_result_status($this->lastresult, PGSQL_STATUS_LONG)) {
        case PGSQL_FATAL_ERROR: case PGSQL_BAD_RESPONSE: {
          $code= pg_result_error_field($this->lastresult, PGSQL_DIAG_SQLSTATE);
          $message= 'Statement failed: '.pg_result_error_field($this->lastresult, PGSQL_DIAG_MESSAGE_PRIMARY).' @ '.$this->dsn->getHost();
          if ('40P01' === $code) {
            throw new SQLDeadlockException($message, $sql, $code);
          } else {
            throw new SQLStatementFailedException($message, $sql, $code);
          }
        }
        
        case PGSQL_COMMAND_OK: {
          $this->_obs && $this->notifyObservers(new DBEvent('queryend', TRUE));
          return TRUE;
        }
        
        default: {
          $resultset= new PostgreSQLResultSet($this->lastresult, $this->tz);
          $this->_obs && $this->notifyObservers(new DBEvent('queryend', $resultset));
          return $resultset;
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
      if (FALSE === $this->query('begin transaction')) {
        return FALSE;
      }
      $transaction->db= $this;
      return $transaction;
    }
    
    /**
     * Retrieve transaction state
     *
     * @param   string name
     * @return  mixed state
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

    /**
     * get SQL formatter
     *
     * @return  rdbms.StatementFormatter
     */
    public function getFormatter() {
      if (NULL === $this->formatter) $this->formatter= new StatementFormatter($this, new PostgreSQLDialect());
      return $this->formatter;
    }
  }
?>
