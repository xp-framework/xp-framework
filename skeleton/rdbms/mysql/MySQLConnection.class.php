<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.DBConnection',
    'rdbms.mysql.MySQLResultSet',
    'rdbms.Transaction',
    'rdbms.StatementFormatter',
    'rdbms.mysql.MysqlDialect'
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

    /**
     * Constructor
     *
     * @param   rdbms.DSN dsn
     */
    public function __construct($dsn) { 
      parent::__construct($dsn);
      $this->formatter= new StatementFormatter($this, new MysqlDialect());
    }

    /**
     * Set Timeout
     *
     * @param   int timeout
     */
    public function setTimeout($timeout) {
      ini_set('mysql.connect_timeout', $timeout);
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
        $this->handle= mysql_pconnect(
          $this->dsn->getHost().':'.$this->dsn->getPort(3306), 
          $this->dsn->getUser(), 
          $this->dsn->getPassword()
        );
      } else {
        $this->handle= mysql_connect(
          $this->dsn->getHost().':'.$this->dsn->getPort(3306), 
          $this->dsn->getUser(), 
          $this->dsn->getPassword(),
          ($this->flags & DB_NEWLINK)
        );
      }
      
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $reconnect));

      if (!is_resource($this->handle)) {
        throw new SQLConnectException('#'.mysql_errno().': '.mysql_error(), $this->dsn);
      }
      
      mysql_query('set names UTF8', $this->handle);

      // Figure out sql_mode and update formatter's escaperules accordingly
      // - See: http://bugs.mysql.com/bug.php?id=10214
      // - Possible values: http://dev.mysql.com/doc/refman/5.0/en/server-sql-mode.html
      // "modes is a list of different modes separated by comma (,) characters."
      $modes= array_flip(explode(',', current(mysql_fetch_row(mysql_query(
        "show variables like 'sql_mode'", 
        $this->handle
      )))));
      
      // NO_BACKSLASH_ESCAPES: Disable the use of the backslash character 
      // (\) as an escape character within strings. With this mode enabled, 
      // backslash becomes any ordinary character like any other. 
      // (Implemented in MySQL 5.0.1)
      isset($modes['NO_BACKSLASH_ESCAPES']) && $this->formatter->dialect->setEscapeRules(array(
        '"'   => '""'
      ));

      return parent::connect();
    }
    
    /**
     * Disconnect
     *
     * @return  bool success
     */
    public function close() { 
      if ($this->handle && $r= mysql_close($this->handle)) {
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
      if (!mysql_select_db($db, $this->handle)) {
        throw new SQLStatementFailedException(
          'Cannot select database: '.mysql_error($this->handle), 
          'use '.$db,
          mysql_errno($this->handle)
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
      $i= $this->query('select last_insert_id() as xp_id')->next('xp_id');
      $this->_obs && $this->notifyObservers(new DBEvent(__FUNCTION__, $i));
      return $i;
    }

    /**
     * Retrieve number of affected rows for last query
     *
     * @return  int
     */
    protected function affectedRows() {
      return mysql_affected_rows($this->handle);
    }    
    
    /**
     * Execute any statement
     *
     * @param   string sql
     * @param   bool buffered default TRUE
     * @return  rdbms.ResultSet or TRUE if no resultset was created
     * @throws  rdbms.SQLException
     */
    protected function query0($sql, $buffered= TRUE) {
      if (!is_resource($this->handle)) {
        if (!($this->flags & DB_AUTOCONNECT)) throw new SQLStateException('Not connected');
        $c= $this->connect();
        
        // Check for subsequent connection errors
        if (FALSE === $c) throw new SQLStateException('Previously failed to connect.');
      }
      
      if (!$buffered || $this->flags & DB_UNBUFFERED) {
        $result= mysql_unbuffered_query($sql, $this->handle);
      } else {
        $result= mysql_query($sql, $this->handle);
      }
      
      if (FALSE === $result) {
        $code= mysql_errno($this->handle);
        $message= 'Statement failed: '.mysql_error($this->handle).' @ '.$this->dsn->getHost();
        switch ($code) {
          case 2006: // MySQL server has gone away
          case 2013: // Lost connection to MySQL server during query
            throw new SQLConnectionClosedException('Statement failed: '.$message, $sql, $code);

          case 1213: // Deadlock
            throw new SQLDeadlockException($message, $sql, $code);
          
          default:   // Other error
            throw new SQLStatementFailedException($message, $sql, $code);
        }
      }
      
      return (TRUE === $result
        ? $result
        : new MySQLResultSet($result, $this->tz)
      );
    }

    /**
     * Begin a transaction
     *
     * @param   rdbms.Transaction transaction
     * @return  rdbms.Transaction
     */
    public function begin($transaction) {
      if (!$this->query('begin')) return FALSE;
      $transaction->db= $this;
      return $transaction;
    }
    
    /**
     * Rollback a transaction
     *
     * @param   string name
     * @return  bool success
     */
    public function rollback($name) { 
      return $this->query('rollback');
    }
    
    /**
     * Commit a transaction
     *
     * @param   string name
     * @return  bool success
     */
    public function commit($name) { 
      return $this->query('commit');
    }
  }
?>
