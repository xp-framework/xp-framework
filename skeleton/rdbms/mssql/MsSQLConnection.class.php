<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.DBConnection',
    'rdbms.mssql.MsSQLResultSet',
    'rdbms.Transaction',
    'rdbms.StatementFormatter',
    'rdbms.mssql.MsSQLDialect'
  );

  /**
   * Connection to MsSQL databases using client libraries
   *
   * @see      http://mssql.com/
   * @ext      mssql
   * @test     xp://net.xp_framework.unittest.rdbms.TokenizerTest
   * @test     xp://net.xp_framework.unittest.rdbms.DBTest
   * @purpose  Database connection
   */
  class MsSQLConnection extends DBConnection {
    private
      $formatter= NULL;

    /**
     * Set Timeout
     *
     * @param   int timeout
     */
    public function setTimeout($timeout) {
      ini_set('mssql.connect_timeout', $timeout);
      parent::setTimeout($timeout);
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

      if ($this->flags & DB_PERSISTENT) {
        $this->handle= mssql_pconnect(
          $this->dsn->getHost(), 
          $this->dsn->getUser(), 
          $this->dsn->getPassword()
        );
      } else {
        $this->handle= mssql_connect(
          $this->dsn->getHost(), 
          $this->dsn->getUser(), 
          $this->dsn->getPassword()
        );
      }

      if (!is_resource($this->handle)) {
        throw new SQLConnectException(trim(mssql_get_last_message()), $this->dsn);
      }
      
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $reconnect));
      return parent::connect();
    }
    
    /**
     * Disconnect
     *
     * @return  bool success
     */
    public function close() { 
      if ($this->handle && $r= mssql_close($this->handle)) {
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
      if (!mssql_select_db($db, $this->handle)) {
        throw new SQLStatementFailedException(
          'Cannot select database: '.trim(mssql_get_last_message()),
          'use '.$db,
          current(mssql_fetch_row(mssql_query('select @@error', $this->handle)))
        );
      }
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
     * Retrieve identity
     *
     * @return  mixed identity value
     */
    public function identity($field= NULL) {
      $i= $this->query('select @@identity as i')->next('i');
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $i));
      return $i;
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
      
      return mssql_rows_affected($this->handle);
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
      
      return mssql_rows_affected($this->handle);
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
      
      return mssql_rows_affected($this->handle);
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
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, sizeof ($rows)));
      return $rows;
    }
    
    /**
     * Execute any statement
     *
     * @param   mixed* args
     * @return  rdbms.mssql.MsSQLResultSet or TRUE if no resultset was created
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
     * @param   mixed* args
     * @return  rdbms.mssql.MsSQLResultSet or TRUE if no resultset was created
     * @throws  rdbms.SQLException
     */
    protected function query0($sql) {
      if (!is_resource($this->handle)) {
        if (!($this->flags & DB_AUTOCONNECT)) throw new SQLStateException('Not connected');
        $c= $this->connect();
        
        // Check for subsequent connection errors
        if (FALSE === $c) throw new SQLStateException('Previously failed to connect');
      }
      
      if ($this->flags & DB_UNBUFFERED) {
        $result= mssql_unbuffered_query($sql, $this->handle, $this->flags & DB_STORE_RESULT);
      } else {
        $result= mssql_query($sql, $this->handle);
      }

      if (FALSE === $result) {
        $message= 'Statement failed: '.trim(mssql_get_last_message()).' @ '.$this->dsn->getHost();
        if (!is_resource($error= mssql_query('select @@error', $this->handle))) {
        
          // The only case selecting @@error should fail is if we receive a
          // disconnect. We could also check on the warnings stack if we can
          // find the following:
          //
          // MsSQL:  Client message:  Read from SQL server failed. (severity 78)
          //
          // but that seems a bit errorprone. 
          throw new SQLConnectionClosedException($message, $sql);
        }

        $code= current(mssql_fetch_row($error));
        switch ($code) {
          case 1205:    // Deadlock
            throw new SQLDeadlockException($message, $sql, $code);

          default:      // Other error
            throw new SQLStatementFailedException($message, $sql, $code);
        }
      }
      
      return (TRUE === $result
        ? $result
        : new MsSQLResultSet($result, $this->tz)
      );
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
     * @return  mixed state
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
     * get SQL formatter
     *
     * @return  rdbms.StatementFormatter
     */
    public function getFormatter() {
      if (NULL === $this->formatter) $this->formatter= new StatementFormatter($this, new MsSQLDialect());
      return $this->formatter;
    }
  }
?>
