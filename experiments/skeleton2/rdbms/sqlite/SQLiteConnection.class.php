<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.DBConnection',
    'rdbms.sqlite.SQLiteResultSet',
    'rdbms.Transaction'
  );

  /**
   * Connection to SQLite Databases
   *
   * @ext      sqlite
   * @see      http://sqlite.org/
   * @purpose  Database connection
   */
  class SQLiteConnection extends DBConnection {
  
    /**
     * Callback functions for dates
     *
     * @access  protected
     * @param   mixed s
     * @return  mixed
     */
    protected function _date($s) {
      return is_null($s) ? NULL : "\2".$s;
    }

    /**
     * Callback functions for numerics
     *
     * @access  protected
     * @param   mixed s
     * @return  mixed
     */
    protected function _int($s) {
      return is_null($s) ? NULL : "\3".$s;
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
        $this->handle= sqlite_open(
          $this->dsn->getUser().'.'.$this->dsn->getHost(), 
          0666,
          $err
        );
      } else {
        $this->handle= sqlite_popen(
          $this->dsn->getUser().'.'.$this->dsn->getHost(), 
          0666,
          $err
        );
      }

      if (!is_resource($this->handle)) {
        throw (new SQLConnectException($err, $this->dsn));
      }
      
      sqlite_create_function($this->handle, 'date', array($this, '_date'), 1);
      sqlite_create_function($this->handle, 'int', array($this, '_int'), 1);
      return TRUE;
    }
    
    /**
     * Disconnect
     *
     * @access  public
     * @return  bool success
     */
    public function close() { 
      if ($this->handle && $r= sqlite_close($this->handle)) {
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
      throw (new SQLStatementFailedException(
        'Cannot select database, not implemented in SQLite'
      ));
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
          switch ($tok{0 + $mod}) {
            case 'd': $r= is_null($arg) ? 'NULL' : sprintf('%.0f', $arg); break;
            case 'f': $r= is_null($arg) ? 'NULL' : floatval($arg); break;
            case 'c': $r= is_null($arg) ? 'NULL' : $arg; break;
            case 's': $r= is_null($arg) ? 'NULL' : "'".sqlite_escape_string($arg)."'"; break;
            case 'u': $r= is_null($arg) ? 'NULL' : '"'.date ('Y-m-d H:i:s', $arg).'"'; break;
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
      $i= sqlite_last_insert_rowid($this->handle);
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
      
      return sqlite_changes($this->handle);
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
      
      return sqlite_changes($this->handle);
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
      
      return sqlite_changes($this->handle);
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
    public function query() { 
      $args= func_get_args();
      $sql= self::_prepare($args);

      if (!is_resource($this->handle)) {
        if (!($this->flags & DB_AUTOCONNECT)) throw (new SQLStateException('Not connected'));
        try {
          $c= self::connect();
        }
        catch (SQLException $e) {
          throw  ($e);
        }
        
        // Check for subsequent connection errors
        if (FALSE === $c) throw (new SQLStateException('Previously failed to connect.'));
      }
      
      $this->log && $this->log->debug($sql);

      if ($this->flags & DB_UNBUFFERED) {
        $result= sqlite_unbuffered_query($sql, $this->handle, $this->flags & DB_STORE_RESULT);
      } else {
        $result= sqlite_query($sql, $this->handle);
      }

      if (FALSE === $result) {
        $e= sqlite_last_error($this->handle);
        throw (new SQLStatementFailedException(
          'Statement failed: '.sqlite_error_string($e), 
          $sql, 
          $e
        ));
      }
      return new SQLiteResultSet($result);
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
