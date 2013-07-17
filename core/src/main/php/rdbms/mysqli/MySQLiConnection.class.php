<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.DBConnection',
    'rdbms.mysqli.MySQLiResultSet',
    'rdbms.Transaction',
    'rdbms.StatementFormatter',
    'rdbms.mysqli.MysqliDialect'
  );

  /**
   * Connection to MySQL Databases
   *
   * @see      http://mysql.org/
   * @ext      mysqli
   * @test     xp://net.xp_framework.unittest.rdbms.TokenizerTest
   * @test     xp://net.xp_framework.unittest.rdbms.DBTest
   * @test     net.xp_framework.unittest.rdbms.integration.MySQLIntegrationTest
   * @purpose  Database connection
   */
  class MySQLiConnection extends DBConnection {
    protected $result= NULL;

    static function __static() {
      if (extension_loaded('mysqli')) {
        DriverManager::register('mysql+i', new XPClass(__CLASS__));
      }
    }

    /**
     * Constructor
     *
     * @param   rdbms.DSN dsn
     */
    public function __construct($dsn) { 
      parent::__construct($dsn);
      $this->formatter= new StatementFormatter($this, new MysqliDialect());
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
      if (is_object($this->handle)) return TRUE;  // Already connected
      if (!$reconnect && (FALSE === $this->handle)) return FALSE;    // Previously failed connecting

      $this->_obs && $this->notifyObservers(new DBEvent(DBEvent::CONNECT, $reconnect));

      // Connect via local sockets if "." is passed. This will not work on
      // Windows with the mysqlnd extension (see PHP bug #48082: "mysql_connect
      // does not work with named pipes"). For mysqlnd, we default to mysqlx
      // anyways, so this works transparently.
      $host= $this->dsn->getHost();
      $sock= NULL;
      if ('.' === $host) {
        $sock= $this->dsn->getProperty('socket', NULL);
        if (0 === strncasecmp(PHP_OS, 'Win', 3)) {
          $host= '.';
          if (NULL !== $sock) $sock= substr($sock, 9);   // 9 = strlen("\\\\.\\pipe\\")
        } else {
          $host= 'localhost';
        }
      } else if ('localhost' === $host) {
        $host= '127.0.0.1';   // Force TCP/IP
      }

      $this->handle= mysqli_connect(
        ($this->flags & DB_PERSISTENT ? 'p:' : '').$host,
        $this->dsn->getUser(), 
        $this->dsn->getPassword(),
        $this->dsn->getDatabase(),
        $this->dsn->getPort(3306),
        $sock
      );

      $this->_obs && $this->notifyObservers(new DBEvent(DBEvent::CONNECTED, $reconnect));

      if (!is_object($this->handle)) {
        $e= new SQLConnectException('#'.mysqli_connect_errno().': '.mysqli_connect_error(), $this->dsn);
        xp::gc(__FILE__);
        throw $e;
      }

      mysqli_set_charset($this->handle, 'latin1');

      // Figure out sql_mode and update formatter's escaperules accordingly
      // - See: http://bugs.mysql.com/bug.php?id=10214
      // - Possible values: http://dev.mysql.com/doc/refman/5.0/en/server-sql-mode.html
      // "modes is a list of different modes separated by comma (,) characters."
      $modes= array_flip(explode(',', current(mysqli_fetch_row(mysqli_query(
        $this->handle,
        "show variables like 'sql_mode'"
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
      if ($this->handle && $r= mysqli_close($this->handle)) {
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
      if (!mysqli_select_db($this->handle, $db)) {
        throw new SQLStatementFailedException(
          'Cannot select database: '.mysqli_error($this->handle), 
          'use '.$db,
          mysqli_errno($this->handle)
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
      $this->_obs && $this->notifyObservers(new DBEvent(DBEvent::IDENTITY, $i));
      return $i;
    }

    /**
     * Retrieve number of affected rows for last query
     *
     * @return  int
     */
    protected function affectedRows() {
      return mysqli_affected_rows($this->handle);
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
      if (!is_object($this->handle)) {
        if (!($this->flags & DB_AUTOCONNECT)) throw new SQLStateException('Not connected');
        $c= $this->connect();
        
        // Check for subsequent connection errors
        if (FALSE === $c) throw new SQLStateException('Previously failed to connect.');
      }
      
      // Clean up previous results to prevent "Commands out of sync" errors
      if (NULL !== $this->result) {
        mysqli_free_result($this->result);
        $this->result= NULL;
      }

      // Execute query
      $r= mysqli_query($this->handle, $sql, !$buffered || $this->flags & DB_UNBUFFERED ? MYSQLI_USE_RESULT : 0);
      if (FALSE === $r) {
        $code= mysqli_errno($this->handle);
        $message= 'Statement failed: '.mysqli_error($this->handle).' @ '.$this->dsn->getHost();
        switch ($code) {
          case 2006: // MySQL server has gone away
          case 2013: // Lost connection to MySQL server during query
            throw new SQLConnectionClosedException('Statement failed: '.$message, $sql, $code);

          case 1213: // Deadlock
            throw new SQLDeadlockException($message, $sql, $code);
          
          default:   // Other error
            throw new SQLStatementFailedException($message, $sql, $code);
        }
      } else if (TRUE === $r) {
        return TRUE;
      } else {
        $this->result= $r;
        return new MySQLiResultSet($this->result, $this->tz);
      }
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
