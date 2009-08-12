<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'rdbms.DBConnection',
    'rdbms.sqlsrv.SqlSrvResultSet',
    'rdbms.Transaction',
    'rdbms.StatementFormatter',
    'rdbms.sqlsrv.SqlSrvDialect'
  );

  /**
   * Connection to SqlSrv databases using client libraries
   *
   * @see      http://mssql.com/
   * @ext      sqlsrv
   * @test     xp://net.xp_framework.unittest.rdbms.TokenizerTest
   * @test     xp://net.xp_framework.unittest.rdbms.DBTest
   * @purpose  Database connection
   */
  class SqlSrvConnection extends DBConnection {
    private
      $formatter= NULL;

    /**
     * Returns all errors as a string
     *
     * @return  string
     */
    protected function errors() {
      $string= ''; 
      foreach (sqlsrv_errors() as $error) {
        $string.= '['.$error[0].':'.$error[1].']: '.$error[2].', ';
      }
      return substr($string, 0, -2);
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

      $spec= $this->dsn->getHost();
      if (-1 != ($port= $this->dsn->getPort(-1))) {
         $spec.= ', '.$port;
      }
      $this->handle= sqlsrv_connect($spec, $a= array(
        'Database'     => $this->dsn->getDatabase(),
        'LoginTimeout' => $this->timeout,
        'UID'          => $this->dsn->getUser(),
        'PWD'          => $this->dsn->getPassword(),
      ));

      if (!is_resource($this->handle)) {
        throw new SQLConnectException($this->errors(), $this->dsn);
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
      if ($this->handle && $r= sqlsrv_close($this->handle)) {
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
      if (!sqlsrv_select_db($db, $this->handle)) {
        throw new SQLStatementFailedException(
          'Cannot select database: '.$this->errors(),
          'use '.$db,
          current(sqlsrv_fetch_row(sqlsrv_query('select @@error', $this->handle)))
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
      if (!($r= $this->query('select @@identity as i'))) {
        return FALSE;
      }
      $i= $r->next('i');
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
      
      return sqlsrv_rows_affected($this->handle);
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
      
      return sqlsrv_rows_affected($this->handle);
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
      
      return sqlsrv_rows_affected($this->handle);
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
     * @return  rdbms.mssql.SqlSrvResultSet or FALSE to indicate failure
     * @throws  rdbms.SQLException
     */
    public function query() { 
      $args= func_get_args();
      $sql= call_user_func_array(array($this, 'prepare'), $args);

      if (!is_resource($this->handle)) {
        if (!($this->flags & DB_AUTOCONNECT)) throw new SQLStateException('Not connected');
        $c= $this->connect();
        
        // Check for subsequent connection errors
        if (FALSE === $c) throw new SQLStateException('Previously failed to connect');
      }
      
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $sql));
      $result= sqlsrv_query($this->handle, $sql);

      if (FALSE === $result) {
        $message= 'Statement failed: '.$this->errors().' @ '.$this->dsn->getHost();
        if (!is_resource($error= sqlsrv_query('select @@error', $this->handle))) {
        
          // The only case selecting @@error should fail is if we receive a
          // disconnect. We could also check on the warnings stack if we can
          // find the following:
          //
          // SqlSrv:  Client message:  Read from SQL server failed. (severity 78)
          //
          // but that seems a bit errorprone. 
          throw new SQLConnectionClosedException($message, $sql);
        }

        $code= current(sqlsrv_fetch_row($error));
        switch ($code) {
          case 1205:    // Deadlock
            throw new SQLDeadlockException($message, $sql, $code);

          default:      // Other error
            throw new SQLStatementFailedException($message, $sql, $code);
        }
      }
      
      if (TRUE === $result) {
        $this->_obs && $this->notifyObservers(new DBEvent('queryend', TRUE));
        return $result;
      }
      
      $resultset= new SqlSrvResultSet($result, $this->tz);
      $this->_obs && $this->notifyObservers(new DBEvent('queryend', $resultset));

      return $resultset;
    }
    
    /**
     * Begin a transaction
     *
     * @param   rdbms.Transaction transaction
     * @return  rdbms.Transaction
     */
    public function begin($transaction) {
      if (FALSE === $this->query('begin transaction xp_%c', $transaction->name)) {
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
      if (FALSE === ($r= $this->query('select @@transtate as transtate'))) {
        return FALSE;
      }
      return $r->next('transtate');
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
      if (NULL === $this->formatter) $this->formatter= new StatementFormatter($this, new SqlSrvDialect());
      return $this->formatter;
    }
  }
?>
