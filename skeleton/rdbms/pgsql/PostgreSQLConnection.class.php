<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.DBConnection', 
    'rdbms.Transaction',
    'rdbms.pgsql.PostgreSQLResultSet'
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

    /**
     * Connect
     *
     * @access  public  
     * @param   bool reconnect default FALSE
     * @return  bool success
     * @throws  rdbms.SQLConnectException
     */
    function connect($reconnect= FALSE) {
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
        return throw(new SQLConnectException(rtrim(pg_last_error()), $this->dsn));
      }
      
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $reconnect));
      
      return TRUE;
    }
    
    /**
     * Disconnect
     *
     * @access  public
     * @return  bool success
     */
    function close() { 
      if ($this->handle && $r= pg_close($this->handle)) {
        $this->handle= NULL;
        return $r;
      }
      return FALSE;
    }
    
    /**
     * Select database
     *
     * @access  public
     * @param   string db name of database to select
     * @return  bool success
     * @throws  rdbms.SQLStatementFailedException
     */
    function selectdb($db) {
      return throw(new SQLStatementFailedException(
        'Cannot select database, not implemented in PostgreSQL'
      ));
    }

    /**
     * Prepare an SQL statement
     *
     * @access  public
     * @param   mixed* args
     * @return  string
     */
    function prepare() {
      static $formatter= NULL;
      $args= func_get_args();
      
      if (1 == sizeof($args)) return array_shift($args);
      
      if (NULL === $formatter) {
        $formatter= new StatementFormatter();
        $formatter->setEscapeRules(array(
          '\''  => '\'\''
        ));
        $formatter->setDateFormat('Y-m-d H:i:s');
      }
      return $formatter->format(array_shift($args), $args);
    }
    
    /**
     * Retrieve identity
     *
     * @access  public
     * @return  mixed identity value
     */
    function identity($field) {
      $q= &$this->query('select currval(%s) as id', $field);
      $id= $q ? $q->next('id') : NULL;
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $id));
      return $id;
    }

    /**
     * Execute an insert statement
     *
     * @access  public
     * @param   mixed* args
     * @return  int number of affected rows
     * @throws  rdbms.SQLStatementFailedException
     */
    function insert() { 
      $args= func_get_args();
      $args[0]= 'insert '.$args[0];
      if (!($r= &call_user_func_array(array(&$this, 'query'), $args))) {
        return FALSE;
      }

      return pg_affected_rows($r->handle);
    }
    
    /**
     * Execute an update statement
     *
     * @access  public
     * @param   mixed* args
     * @return  int number of affected rows
     * @throws  rdbms.SQLStatementFailedException
     */
    function update() {
      $args= func_get_args();
      $args[0]= 'update '.$args[0];
      if (!($r= &call_user_func_array(array(&$this, 'query'), $args))) {
        return FALSE;
      }
      
      return pg_affected_rows($r->handle);
    }
    
    /**
     * Execute an update statement
     *
     * @access  public
     * @param   mixed* args
     * @return  int number of affected rows
     * @throws  rdbms.SQLStatementFailedException
     */
    function delete() { 
      $args= func_get_args();
      $args[0]= 'delete '.$args[0];
      if (!($r= &call_user_func_array(array(&$this, 'query'), $args))) {
        return FALSE;
      }
      
      return pg_affected_rows($r->handle);
    }
    
    /**
     * Execute a select statement and return all rows as an array
     *
     * @access  public
     * @param   mixed* args
     * @return  array rowsets
     * @throws  rdbms.SQLStatementFailedException
     */
    function select() { 
      $args= func_get_args();
      $args[0]= 'select '.$args[0];
      if (!($r= &call_user_func_array(array(&$this, 'query'), $args))) {
        return FALSE;
      }
      
      $rows= array();
      while ($row= $r->next()) $rows[]= $row;
      return $rows;
    }
    
    /**
     * Execute any statement
     *
     * @access  public
     * @param   mixed* args
     * @return  &rdbms.pgsql.PostgreSQLResultSet or FALSE to indicate failure
     * @throws  rdbms.SQLException
     */
    function &query() { 
      $args= func_get_args();
      $sql= call_user_func_array(array(&$this, 'prepare'), $args);

      if (!is_resource($this->handle)) {
        if (!($this->flags & DB_AUTOCONNECT)) return throw(new SQLStateException('Not connected'));
        try(); {
          $c= $this->connect();
        }
        if (catch('SQLException', $e)) {
          return throw ($e);
        }
        
        // Check for subsequent connection errors
        if (FALSE === $c) return throw(new SQLStateException('Previously failed to connect.'));
      }
      
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $sql));

      $result= pg_query($this->handle, $sql);

      if (empty($result)) {
        return throw(new SQLStatementFailedException(
          'Statement failed: '.rtrim(pg_last_error($this->handle)),
          $sql
        ));
      }
      
      if (TRUE === $result) {
        $this->_obs && $this->notifyObservers(new DBEvent('queryend', TRUE));
        return TRUE;
      }

      $resultset= &new PostgreSQLResultSet($result);
      $this->_obs && $this->notifyObservers(new DBEvent('queryend', $resultset));

      return $resultset;
    }
    
    /**
     * Begin a transaction
     *
     * @access  public
     * @param   &rdbms.Transaction transaction
     * @return  &rdbms.Transaction
     */
    function &begin(&$transaction) {
      if (FALSE === $this->query('begin transaction')) {
        return FALSE;
      }
      $transaction->db= &$this;
      return $transaction;
    }
    
    /**
     * Retrieve transaction state
     *
     * @access  public
     * @param   string name
     * @return  mixed state
     */
    function transtate($name) { 
      return -1;
    }
    
    /**
     * Rollback a transaction
     *
     * @access  public
     * @param   string name
     * @return  bool success
     */
    function rollback($name) { 
      return $this->query('rollback transaction');
    }
    
    /**
     * Commit a transaction
     *
     * @access  public
     * @param   string name
     * @return  bool success
     */
    function commit($name) { 
      return $this->query('commit transaction');
    }
  }
?>
