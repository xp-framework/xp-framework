<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.DBConnection', 
    'rdbms.mysql.MySQLResultSet',
    'rdbms.Transaction',
    'rdbms.StatementFormatter'
  );

  /**
   * Connection to MySQL Databases
   *
   * @see      http://mysql.org/
   * @ext      mysql
   * @test     xp://net.xp_framework.unittest.rdbms.TokenizerTest
   * @test     xp://net.xp_framework.unittest.rdbms.DBTest
   * @purpose  Database connection
   */
  class MySQLConnection extends DBConnection {
    var
      $formatter= NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   &rdbms.DSN dsn
     */
    function __construct(&$dsn) { 
      parent::__construct($dsn);
      $this->formatter= new StatementFormatter();
      $this->formatter->setEscape('"');
      $this->formatter->setEscapeRules(array(
        '"'   => '\"',
        '\\'  => '\\\\'
      ));
      $this->formatter->setDateFormat('Y-m-d H:i:s');
    }

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

      if ($this->flags & DB_PERSISTENT) {
        $this->handle= mysql_pconnect(
          $this->dsn->getHost(), 
          $this->dsn->getUser(), 
          $this->dsn->getPassword()
        );
      } else {
        $this->handle= mysql_connect(
          $this->dsn->getHost(), 
          $this->dsn->getUser(), 
          $this->dsn->getPassword()
        );
      }
      
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $reconnect));

      if (!is_resource($this->handle)) {
        return throw(new SQLConnectException(mysql_error(), $this->dsn));
      }

      // Figure out sql_mode and update formatter's escaperules accordingly
      // - See: http://bugs.mysql.com/bug.php?id=10214
      // - Possible values: http://dev.mysql.com/doc/refman/5.0/en/server-sql-mode.html
      // "modes is a list of different modes separated by comma (,) characters."
      $modes= array_flip(explode(',', array_pop(mysql_fetch_row(mysql_query(
        'show variables like "sql_mode"', 
        $this->handle
      )))));
      
      // NO_BACKSLASH_ESCAPES: Disable the use of the backslash character 
      // (\) as an escape character within strings. With this mode enabled, 
      // backslash becomes any ordinary character like any other. 
      // (Implemented in MySQL 5.0.1)
      isset($modes['NO_BACKSLASH_ESCAPES']) && $this->formatter->setEscapeRules(array(
        '"'   => '""'
      ));

      return parent::connect();
    }
    
    /**
     * Disconnect
     *
     * @access  public
     * @return  bool success
     */
    function close() { 
      if ($this->handle && $r= mysql_close($this->handle)) {
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
      if (!mysql_select_db($db, $this->handle)) {
        return throw(new SQLStatementFailedException(
          'Cannot select database: '.mysql_error($this->handle), 
          'use '.$db,
          mysql_errno($this->handle)
        ));
      }
      return TRUE;
    }

    /**
     * Prepare an SQL statement
     *
     * @access  public
     * @param   mixed* args
     * @return  string
     */
    function prepare() {
      $args= func_get_args();
      return $this->formatter->format(array_shift($args), $args);
    }
    
    /**
     * Retrieve identity
     *
     * @access  public
     * @return  mixed identity value
     */
    function identity() { 
      $i= mysql_insert_id($this->handle);
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $i));
      return $i;
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
      if (!($r= call_user_func_array(array(&$this, 'query'), $args))) {
        return FALSE;
      }
      
      return mysql_affected_rows($this->handle);
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
      
      return mysql_affected_rows($this->handle);
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
      
      return mysql_affected_rows($this->handle);
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
     * @return  &rdbms.mysql.MySQLResultSet or FALSE to indicate failure
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

      if ($this->flags & DB_UNBUFFERED) {
        $result= mysql_unbuffered_query($sql, $this->handle, $this->flags & DB_STORE_RESULT);
      } else {
        $result= mysql_query($sql, $this->handle);
      }
      
      if (FALSE === $result) {
        return throw(new SQLStatementFailedException(
          'Statement failed: '.mysql_error($this->handle), 
          $sql, 
          mysql_errno($this->handle)
        ));
      }
      
      if (TRUE === $result) {
        $this->_obs && $this->notifyObservers(new DBEvent('queryend', TRUE));
        return $result;
      }

      $resultset= &new MySQLResultSet($result);
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
      if (!$this->query('begin')) return FALSE;
      $transaction->db= &$this;
      return $transaction;
    }
    
    /**
     * Rollback a transaction
     *
     * @access  public
     * @param   string name
     * @return  bool success
     */
    function rollback($name) { 
      return $this->query('rollback');
    }
    
    /**
     * Commit a transaction
     *
     * @access  public
     * @param   string name
     * @return  bool success
     */
    function commit($name) { 
      return $this->query('commit');
    }
  }
?>
