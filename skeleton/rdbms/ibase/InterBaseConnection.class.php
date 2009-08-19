<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.DBConnection',
    'rdbms.ibase.InterBaseResultSet',
    'rdbms.Transaction',
    'rdbms.StatementFormatter',
    'rdbms.ibase.InterBaseDialect'
  );

  /**
   * Connection to InterBase/FireBird databases using client libraries
   *
   * @see      http://www.firebirdsql.org/
   * @see      http://www.borland.com/interbase/
   * @see      http://www.firebirdsql.org/doc/contrib/fb_2_1_errorcodes.pdf
   * @ext      interbase
   * @test     xp://net.xp_framework.unittest.rdbms.TokenizerTest
   * @test     xp://net.xp_framework.unittest.rdbms.DBTest
   * @purpose  Database connection
   */
  class InterBaseConnection extends DBConnection {

    /**
     * Constructor
     *
     * @param   rdbms.DSN dsn
     */
    public function __construct($dsn) {
      parent::__construct($dsn);
      $this->formatter= new StatementFormatter($this, new InterBaseDialect());
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

      $db= $this->dsn->getHost().':'.$this->dsn->getDatabase();
      if ($this->flags & DB_PERSISTENT) {
        $this->handle= ibase_pconnect(
          $db, 
          $this->dsn->getUser(), 
          $this->dsn->getPassword(),
          'ISO8859_1'
        );
      } else {
        $this->handle= ibase_connect(
          $db, 
          $this->dsn->getUser(), 
          $this->dsn->getPassword(),
          'ISO8859_1'
        );
      }

      if (!is_resource($this->handle)) {
        throw new SQLConnectException(trim(ibase_errmsg()), $this->dsn);
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
      if ($this->handle && $r= ibase_close($this->handle)) {
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
      throw new SQLStatementFailedException(
        'Cannot select database, not implemented in Interbase'
      );
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
     * Retrieve number of affected rows for last query
     *
     * @return  int
     */
    protected function affectedRows() {
      return ibase_affected_rows($this->handle);
    }
    
    /**
     * Execute any statement
     *
     * @param   mixed* args
     * @return  rdbms.InterBase.InterBaseResultSet or FALSE to indicate failure
     * @throws  rdbms.SQLException
     */
    public function query0($sql) { 
      if (!is_resource($this->handle)) {
        if (!($this->flags & DB_AUTOCONNECT)) throw new SQLStateException('Not connected');
        $c= $this->connect();
        
        // Check for subsequent connection errors
        if (FALSE === $c) throw new SQLStateException('Previously failed to connect');
      }
      
      $result= ibase_query($sql, $this->handle);
      if (FALSE === $result) {
        $message= 'Statement failed: '.trim(ibase_errmsg()).' @ '.$this->dsn->getHost();
        $code= ibase_errcode();
        switch ($code) {
          case -924:    // Connection lost
            throw new SQLConnectionClosedException($message, $sql);

          case -913:    // Deadlock
            throw new SQLDeadlockException($message, $sql, $code);

          default:      // Other error
            throw new SQLStatementFailedException($message, $sql, $code);
        }
      }
      
      return (TRUE === $result
        ? $result
        : new InterBaseResultSet($result, $this->tz)
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
  }
?>
