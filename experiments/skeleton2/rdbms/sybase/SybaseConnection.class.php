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
     * Set Timeout
     *
     * @access  public
     * @param   int timeout
     */
    public function setTimeout($timeout) {
      ini_set('sybct.login_timeout', $timeout);
      parent::setTimeout($timeout);
    }

    /**
     * Connect
     *
     * @access  public  
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
        throw (new SQLConnectException(trim(sybase_get_last_message()), $this->dsn));
      }
      
      return parent::connect();
    }
    
    /**
     * Disconnect
     *
     * @access  public
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
     * @access  public
     * @param   string db name of database to select
     * @return  bool success
     * @throws  rdbms.SQLStatementFailedException
     */
    public function selectdb($db) {
      if (!sybase_select_db($db, $this->handle)) {
        throw (new SQLStatementFailedException(
          'Cannot select database: '.trim(sybase_get_last_message()),
          'use '.$db,
          array_pop(sybase_fetch_row(sybase_query('select @@error', $this->handle)))
        ));
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
    protected function _prepare($args) {
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
        if ($args[$ofs] instanceof Date) {
          $tok{$mod}= 'u';
          $a= array($args[$ofs]->getTime());
        } elseif ($args[$ofs] instanceof Generic) {
          $a= array($args[$ofs]->toString());
        } elseif (is_array($args[$ofs])) {
          $a= $args[$ofs];
        } else {
          $a= array($args[$ofs]);
        }
        
        foreach ($a as $arg) {
          switch ($tok{$mod}) {
            case 'd': $r= is_null($arg) ? 'NULL' : sprintf('%.0f', $arg); break;
            case 'f': $r= is_null($arg) ? 'NULL' : floatval($arg); break;
            case 'c': $r= is_null($arg) ? 'NULL' : $arg; break;
            case 's': $r= is_null($arg) ? 'NULL' : '"'.str_replace('"', '""', $arg).'"'; break;
            case 'u': $r= is_null($arg) ? 'NULL' : '"'.date ('Y-m-d h:iA', $arg).'"'; break;
            default: $sql.= '%'.$tok; $i--; continue;
          }
          $sql.= $r.', ';
        }
        $sql= substr($sql, 0, -2).substr($tok, 1 + $mod);
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
    public function prepare() {
      $args= func_get_args();
      return self::_prepare($args);    
    }
    
    /**
     * Retrieve identity
     *
     * @access  public
     * @return  mixed identity value
     */
    public function identity() { 
      if (!($r= self::query('select @@identity as i'))) {
        return FALSE;
      }
      $i= $r->next('i');
      $this->log && $this->log->debug('Identity is', $i);
      return $i;
    }

    /**
     * Execute an insert statement
     *
     * @access  public
     * @param   mixed *args
     * @return  int number of affected rows
     * @throws  rdbms.SQLStatementFailedException
     */
    public function insert() { 
      $args= func_get_args();
      $args[0]= 'insert '.$args[0];
      if (!($r= call_user_func_array(array(&$this, 'query'), $args))) {
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
     * @throws  rdbms.SQLStatementFailedException
     */
    public function update() {
      $args= func_get_args();
      $args[0]= 'update '.$args[0];
      if (!($r= call_user_func_array(array(&$this, 'query'), $args))) {
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
     * @throws  rdbms.SQLStatementFailedException
     */
    public function delete() { 
      $args= func_get_args();
      $args[0]= 'delete '.$args[0];
      if (!($r= call_user_func_array(array(&$this, 'query'), $args))) {
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
     * @throws  rdbms.SQLStatementFailedException
     */
    public function select() { 
      $args= func_get_args();
      $args[0]= 'select '.$args[0];
      if (!($r= call_user_func_array(array(&$this, 'query'), $args))) {
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
    public function query() { 
      $args= func_get_args();
      $sql= self::_prepare($args);

      if (!is_resource($this->handle)) {
        if (!($this->flags & DB_AUTOCONNECT)) throw (new SQLStateException('Not connected'));
        try {
          $c= self::connect();
        } catch (SQLException $e) {
          throw  ($e);
        }
        
        // Check for subsequent connection errors
        if (FALSE === $c) throw (new SQLStateException('Previously failed to connect'));
      }
      
      $this->log && $this->log->debug($sql);
      if ($this->flags & DB_UNBUFFERED) {
        $result= sybase_unbuffered_query($sql, $this->handle, $this->flags & DB_STORE_RESULT);
      } else {
        $result= sybase_query($sql, $this->handle);
      }

      if (FALSE === $result) {
        throw (new SQLStatementFailedException(
          'Statement failed: '.trim(sybase_get_last_message()), 
          $sql,
          array_pop(sybase_fetch_row(sybase_query('select @@error', $this->handle)))
        ));
      }

      return new SybaseResultSet($result);
    }
    
    /**
     * Begin a transaction
     *
     * @access  public
     * @param   &rdbms.Transaction transaction
     * @return  &rdbms.Transaction
     */
    public function begin(Transaction $transaction) {
      if (FALSE === self::query('begin transaction xp_%c', $transaction->name)) {
        return FALSE;
      }
      $transaction->db= $this;
      return $transaction;
    }
    
    /**
     * Retrieve transaction state
     *
     * @access  public
     * @param   string name
     * @return  mixed state
     */
    public function transtate($name) { 
      if (FALSE === ($r= self::query('@@transtate as transtate'))) {
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
    public function rollback($name) { 
      return self::query('rollback transaction xp_%c', $name);
    }
    
    /**
     * Commit a transaction
     *
     * @access  public
     * @param   string name
     * @return  bool success
     */
    public function commit($name) { 
      return self::query('commit transaction xp_%c', $name);
    }
  }
?>
