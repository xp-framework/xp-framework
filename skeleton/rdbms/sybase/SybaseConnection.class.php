<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.DBConnection',
    'rdbms.sybase.SybaseResultSet',
    'rdbms.Transaction',
    'rdbms.StatementFormatter',
    'rdbms.sybase.SybaseDialect'
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

    static function __static() {
      ini_set('sybct.deadlock_retry_count', 0);
    }
    
    /**
     * Constructor
     *
     * @param   rdbms.DSN dsn
     */
    public function __construct($dsn) {
      parent::__construct($dsn);
      $this->formatter= new StatementFormatter($this, new SybaseDialect());
    }

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
          $this->dsn->getPassword(),
          'iso_1'
        );
      } else {
        $this->handle= sybase_connect(
          $this->dsn->getHost(), 
          $this->dsn->getUser(), 
          $this->dsn->getPassword(),
          'iso_1'
        );
      }

      if (!is_resource($this->handle)) {
        $e= new SQLConnectException(trim(sybase_get_last_message()), $this->dsn);
        xp::gc(__FILE__);
        throw $e;
      }
      xp::gc(__FILE__);

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
        throw new SQLStatementFailedException(
          'Cannot select database: '.trim(sybase_get_last_message()),
          'use '.$db,
          current(sybase_fetch_row(sybase_query('select @@error', $this->handle)))
        );
      }
      return TRUE;
    }
    
    /**
     * Retrieve identity
     *
     * @return  var identity value
     */
    public function identity($field= NULL) {
      $i= $this->query('select @@identity as i')->next('i');
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $i));
      return $i;
    }

    /**
     * Retrieve number of affected rows for last query
     *
     * @return  int
     */
    protected function affectedRows() {
      return sybase_affected_rows($this->handle);
    }
    
    /**
     * Execute any statement
     *
     * @param   string sql
     * @param   bool buffered default TRUE
     * @return  rdbms.sybase.SybaseResultSet or TRUE if no resultset was created
     * @throws  rdbms.SQLException
     */
    protected function query0($sql, $buffered= TRUE) {
      if (!is_resource($this->handle)) {
        if (!($this->flags & DB_AUTOCONNECT)) throw new SQLStateException('Not connected');
        $c= $this->connect();
        
        // Check for subsequent connection errors
        if (FALSE === $c) throw new SQLStateException('Previously failed to connect');
      }
      
      if (!$buffered) {
        $result= sybase_unbuffered_query($sql, $this->handle, FALSE);
      } else if ($this->flags & DB_UNBUFFERED) {
        $result= sybase_unbuffered_query($sql, $this->handle, $this->flags & DB_STORE_RESULT);
      } else {
        $result= sybase_query($sql, $this->handle);
      }

      if (FALSE === $result) {
        $message= 'Statement failed: '.trim(sybase_get_last_message()).' @ '.$this->dsn->getHost();
        if (!is_resource($error= sybase_query('select @@error', $this->handle))) {
        
          // The only case selecting @@error should fail is if we receive a
          // disconnect. We could also check on the warnings stack if we can
          // find the following:
          //
          // Sybase:  Client message:  Read from SQL server failed. (severity 78)
          //
          // but that seems a bit errorprone. 
          throw new SQLConnectionClosedException($message, $sql);
        }

        $code= current(sybase_fetch_row($error));
        switch ($code) {
          case 1205:    // Deadlock
            throw new SQLDeadlockException($message, $sql, $code);

          default:      // Other error
            throw new SQLStatementFailedException($message, $sql, $code);
        }
      }
      
      return (TRUE === $result
        ? $result
        : new SybaseResultSet($result, $this->tz)
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
     * @return  var state
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
  }
?>
