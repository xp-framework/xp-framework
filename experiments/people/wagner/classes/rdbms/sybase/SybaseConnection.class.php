<?php
/* This class is part of the XP framework
 *
 * $Id: SybaseConnection.class.php 9451 2007-02-12 17:23:29Z friebe $ 
 */

  uses(
    'rdbms.DBConnection',
    'rdbms.sybase.SybaseResultSet',
    'rdbms.Transaction',
    'rdbms.StatementFormatter',
    'rdbms.dialect.SybaseDialect'
  );

  /**
   * Connection to Sybase databases using client libraries
   *
   * @see      http://sybase.com/
   * @ext      sybase_ct
   * @test     xp://net.xp_framework.unittest.rdbms.TokenizerTest
   * @test     xp://net.xp_framework.unittest.rdbms.DBTest
   * @purpose  Database connection
   */
  class SybaseConnection extends DBConnection {

    /**
     * Set Timeout
     *
     * @param   int timeout
     */
    public function setTimeout($timeout) {
      ini_set('sybct.login_timeout', $timeout);
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
        $this->handle= sybase_pconnect(
          $this->dsn->getHost(), 
          $this->dsn->getUser(), 
          $this->dsn->getPassword()
        );
      } else {
        $this->handle= sybase_connect(
          $this->dsn->getHost(), 
          $this->dsn->getUser(), 
          $this->dsn->getPassword()
        );
      }

      if (!is_resource($this->handle)) {
        throw(new SQLConnectException(trim(sybase_get_last_message()), $this->dsn));
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
      if ($this->handle && $r= sybase_close($this->handle)) {
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
      if (!sybase_select_db($db, $this->handle)) {
        throw(new SQLStatementFailedException(
          'Cannot select database: '.trim(sybase_get_last_message()),
          'use '.$db,
          current(sybase_fetch_row(sybase_query('select @@error', $this->handle)))
        ));
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
      static $formatter= NULL;
      $args= func_get_args();
      
      if (NULL === $formatter) {
        $formatter= new StatementFormatter();
        $this->formatter->setDialect(new SybaseDialect());
      }
      
      return $formatter->format(array_shift($args), $args);
    }
    
    /**
     * Retrieve identity
     *
     * @return  mixed identity value
     */
    public function identity() { 
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
      
      return sybase_affected_rows($this->handle);
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
      
      return sybase_affected_rows($this->handle);
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
      
      return sybase_affected_rows($this->handle);
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
     * @return  &rdbms.sybase.SybaseResultSet or FALSE to indicate failure
     * @throws  rdbms.SQLException
     */
    public function query() { 
      $args= func_get_args();
      $sql= call_user_func_array(array($this, 'prepare'), $args);

      if (!is_resource($this->handle)) {
        if (!($this->flags & DB_AUTOCONNECT)) throw(new SQLStateException('Not connected'));
        try {
          $c= $this->connect();
        } catch (SQLException $e) {
          throw ($e);
        }
        
        // Check for subsequent connection errors
        if (FALSE === $c) throw(new SQLStateException('Previously failed to connect'));
      }
      
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $sql));
      if ($this->flags & DB_UNBUFFERED) {
        $result= sybase_unbuffered_query($sql, $this->handle, $this->flags & DB_STORE_RESULT);
      } else {
        $result= sybase_query($sql, $this->handle);
      }

      if (FALSE === $result) {
        if (!is_resource($error= sybase_query('select @@error', $this->handle))) {
        
          // The only case selecting @@error should fail is if we receive a
          // disconnect. We could also check on the warnings stack if we can
          // find the following:
          //
          // Sybase:  Client message:  Read from SQL server failed. (severity 78)
          //
          // but that seems a bit errorprone. 
          throw(new SQLConnectionClosedException(
            'Statement failed: '.trim(sybase_get_last_message()), 
            $sql
          ));
        }

        throw(new SQLStatementFailedException(
          'Statement failed: '.trim(sybase_get_last_message()), 
          $sql,
          current(sybase_fetch_row($error))
        ));
      }
      
      if (TRUE === $result) {
        $this->_obs && $this->notifyObservers(new DBEvent('queryend', TRUE));
        return $result;
      }
      
      $resultset= new SybaseResultSet($result);
      $this->_obs && $this->notifyObservers(new DBEvent('queryend', $resultset));

      return $resultset;
    }
    
    /**
     * Begin a transaction
     *
     * @param   &rdbms.Transaction transaction
     * @return  &rdbms.Transaction
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
  }
?>
