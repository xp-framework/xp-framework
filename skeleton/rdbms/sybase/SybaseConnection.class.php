<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.DBConnection', 
    'rdbms.sybase.SybaseResultSet',
    'rdbms.Transaction'
  );

  /**
   * Connection to Sybase databases using client libraries
   *
   * @see      http://sybase.com/
   * @ext      sybase_ct
   * @purpose  Database connection
   */
  class SybaseConnection extends DBConnection {

    /**
     * Connect
     *
     * @access  public  
     * @return  bool success
     * @throws  rdbms.SQLException
     */
    function connect() {
      if (is_resource($this->handle)) return TRUE;  // Already connected
      if (FALSE === $this->handle) return FALSE;    // Previously failed connecting

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
        return throw(new SQLException(sprintf(
          'Unable to connect to %s@%s - using password: %s',
          $this->dsn->getUser(),
          $this->dsn->getHost(),
          $this->dsn->getPassword() ? 'yes' : 'no'
        )));
      }
      
      return parent::connect();
    }
    
    /**
     * Disconnect
     *
     * @access  public
     * @return  bool success
     */
    function close() { 
      if ($this->handle && $r= sybase_close($this->handle)) {
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
     * @throws  rdbms.SQLException
     */
    function selectdb($db) {
      if (!sybase_select_db($db, $this->handle)) {
        return throw(new SQLException('Cannot select database', 'use '.$db));
      }
      return TRUE;
    }
    
    /**
     * Protected helper methid
     *
     * @access  protected
     * @param   array args
     * @return  string
     */
    function _prepare($args) {
      $sql= $args[0];
      if (sizeof($args) <= 1) return $sql;

      $i= 0;    
      $sql= $tok= strtok($sql, '%');
      while (++$i && $tok= strtok('%')) {
      
        // Support %1$s syntax
        if (is_numeric($tok{0})) {
          sscanf($tok, '%d$', $ofs);
          $mod= strlen($ofs) + 1;
        } else {
          $ofs= $i;
          $mod= 0;
        }
        
        // Type-based conversion
        if (is_a($args[$ofs], 'Date')) {
          $arg= date('Y-m-d h:iA', $args[$ofs]->getTime());
        } elseif (is_a($args[$ofs], 'Object')) {
          $arg= $args[$ofs]->toString();
        } else {
          $arg= $args[$ofs];
        }
        
        switch ($tok{0 + $mod}) {
          case 'd': $r= is_null($arg) ? 'NULL' : intval($arg); break;
          case 'f': $r= is_null($arg) ? 'NULL' : floatval($arg); break;
          case 'c': $r= is_null($arg) ? 'NULL' : $arg; break;
          case 's': $r= is_null($arg) ? 'NULL' : '"'.str_replace('"', '""', $arg).'"'; break;
          case 'u': $r= is_null($arg) ? 'NULL' : '"'.date ('Y-m-d h:iA', $arg).'"'; break;
          default: $sql.= '%'.$tok; $i--; continue;
        }
        $sql.= $r.substr($tok, 1 + $mod);
      }
      return $sql;
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
      return $this->_prepare($args);    
    }
    
    /**
     * Retrieve identity
     *
     * @access  public
     * @return  mixed identity value
     */
    function identity() { 
      if (FALSE === ($r= &$this->query('select @@identity as i'))) {
        return FALSE;
      }
      return $r->next('i');
    }

    /**
     * Execute an insert statement
     *
     * @access  public
     * @param   mixed *args
     * @return  int number of affected rows
     * @throws  rdbms.SQLException
     */
    function insert() { 
      $args= func_get_args();
      $args[0]= 'insert '.$args[0];
      if (FALSE === ($r= &call_user_func_array(array(&$this, 'query'), $args))) {
        return FALSE;
      }
      
      return sybase_affected_rows($this->handle);
    }
    
    
    /**
     * Execute an update statement
     *
     * @access  public
     * @param   mixed* args
     * @return  int number of affected rows
     * @throws  rdbms.SQLException
     */
    function update() {
      $args= func_get_args();
      $args[0]= 'update '.$args[0];
      if (FALSE === ($r= &call_user_func_array(array(&$this, 'query'), $args))) {
        return FALSE;
      }
      
      return sybase_affected_rows($this->handle);
    }
    
    /**
     * Execute an update statement
     *
     * @access  public
     * @param   mixed* args
     * @return  int number of affected rows
     * @throws  rdbms.SQLException
     */
    function delete() { 
      $args= func_get_args();
      $args[0]= 'delete '.$args[0];
      if (FALSE === ($r= &call_user_func_array(array(&$this, 'query'), $args))) {
        return FALSE;
      }
      
      return sybase_affected_rows($this->handle);
    }
    
    /**
     * Execute a select statement and return all rows as an array
     *
     * @access  public
     * @param   mixed* args
     * @return  array rowsets
     * @throws  rdbms.SQLException
     */
    function select() { 
      $args= func_get_args();
      $args[0]= 'select '.$args[0];
      if (FALSE === ($r= &call_user_func_array(array(&$this, 'query'), $args))) {
        return FALSE;
      }
      
      $rows= array();
      while ($row= $r->next()) $rows[]= $row;
      $this->log && $this->log->debugf ('Fetched %d rows', count ($rows));
      return $rows;
    }
    
    /**
     * Execute any statement
     *
     * @access  public
     * @param   mixed* args
     * @return  &rdbms.sybase.SybaseResultSet or FALSE to indicate failure
     * @throws  rdbms.SQLException
     */
    function &query() { 
      $args= func_get_args();
      $sql= $this->_prepare($args);

      if (!is_resource($this->handle)) {
        if (!($this->flags & DB_AUTOCONNECT)) return throw(new SQLException('Not connected'));
        if (!$this->connect()) return FALSE;
      }
      
      $this->log && $this->log->debug ($sql);
      
      if ($this->flags & DB_BUFFER_RESULTS) {
        $result= sybase_unbuffered_query($sql, $this->handle, $this->flags & DB_STORE_RESULT);
      } else {
        $result= sybase_query($sql, $this->handle);
      }

      if (FALSE === $result) {
        return throw(new SQLException('Statement failed', $sql));
      } else {
        return new SybaseResultSet($result);
      }
    }
    
    /**
     * Begin a transaction
     *
     * @access  public
     * @param   &rdbms.Transaction transaction
     * @return  &rdbms.Transaction
     */
    function &begin(&$transaction) {
      if (FALSE === $this->query('begin transaction %c', $transaction->name)) {
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
      if (FALSE === ($r= &$this->query('@@transtate as transtate'))) {
        return FALSE;
      }
      return $r->next('transtate');
    }
    
    /**
     * Rollback a transaction
     *
     * @access  public
     * @param   string name
     * @return  bool success
     */
    function rollback($name) { 
      return $this->query('rollback transaction %c', $name);
    }
    
    /**
     * Commit a transaction
     *
     * @access  public
     * @param   string name
     * @return  bool success
     */
    function commit($name) { 
      return $this->query('commit transaction %c', $name);
    }
  }
?>
