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
    protected
      $_oid = NULL;

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
        throw (new SQLConnectException(rtrim(pg_last_error()), $this->dsn));
      }
      
      return TRUE;
    }
    
    /**
     * Disconnect
     *
     * @access  public
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
     * @access  public
     * @param   string db name of database to select
     * @return  bool success
     * @throws  rdbms.SQLStatementFailedException
     */
    public function selectdb($db) {
      throw (new SQLStatementFailedException(
        'Cannot select database, not implemented in PostgreSQL'
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
        if (is_a($args[$ofs], 'Date')) {
          $tok{$mod}= 'u';
          $a= array($args[$ofs]->getTime());
        } elseif (is_a($args[$ofs], 'Object')) {
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
            case 's': $r= is_null($arg) ? 'NULL' : "'".pg_escape_string($arg)."'"; break;
            case 'u': $r= is_null($arg) ? 'NULL' : "'".date ('Y-m-d H:i:s', $arg)."'"; break;
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
      $this->log && $this->log->debug('Identity is', $this->_oid);
      return $this->_oid;
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
    public function update() {
      $args= func_get_args();
      $args[0]= 'update '.$args[0];
      if (!($r= call_user_func_array(array(&$this, 'query'), $args))) {
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
    public function delete() { 
      $args= func_get_args();
      $args[0]= 'delete '.$args[0];
      if (!($r= call_user_func_array(array(&$this, 'query'), $args))) {
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
     * @return  &rdbms.pgsql.PostgreSQLResultSet or FALSE to indicate failure
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

      $result= pg_query($this->handle, $sql);

      if (empty($result)) {
        throw (new SQLStatementFailedException(
          'Statement failed: '.rtrim(pg_last_error($this->handle)),
          $sql
        ));
      }

      $this->_oid= (1 == pg_result_status($result)
        ? pg_last_oid($result)
        : NULL
      );

      return new PostgreSQLResultSet($result);
    }
    
    /**
     * Begin a transaction
     *
     * @access  public
     * @param   &rdbms.Transaction transaction
     * @return  &rdbms.Transaction
     */
    public function begin(Transaction $transaction) {
      if (FALSE === self::query('begin transaction')) {
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
      return -1;
    }
    
    /**
     * Rollback a transaction
     *
     * @access  public
     * @param   string name
     * @return  bool success
     */
    public function rollback($name) { 
      return self::query('rollback transaction');
    }
    
    /**
     * Commit a transaction
     *
     * @access  public
     * @param   string name
     * @return  bool success
     */
    public function commit($name) { 
      return self::query('commit transaction');
    }
  }
?>
