<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.DBConnection', 'rdbms.mysql.MySQLResultSet');

  /**
   * Connection to MySQL Databases
   *
   * @see      http://mysql.org/
   * @ext      mysql
   * @purpose  Database connection
   */
  class MySQLConnection extends DBConnection {

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
     * @throws  rdbms.SQLException
     */
    function selectdb($db) {
      if (!mysql_select_db($db, $this->handle)) {
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
        if (is_a($args[$i], 'Date')) {
          $arg= date('Y-m-d H:i:s', $args[$i]->getTime());
        } elseif (is_a($args[$i], 'Object')) {
          $arg= $args[$i]->toString();
        } else {
          $arg= $args[$i];
        }
        switch ($tok{0}) {
          case 'd': is_null($arg) ? 'NULL' : $r= intval($arg); break;
          case 'f': is_null($arg) ? 'NULL' : $r= floatval($arg); break;
          case 'c': is_null($arg) ? 'NULL' : $r= $arg; break;
          case 's': is_null($arg) ? 'NULL' : $r= '"'.str_replace('"', '\"', $arg).'"'; break;
          case 'u': is_null($arg) ? 'NULL' : $r= '"'.date ('Y-m-d h:i:s', $arg).'"'; break;
          default: $sql.= '%'.$tok; $i--; continue;
        }
        $sql.= $r.substr($tok, 1);
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
      if (FALSE === ($r= &$this->query('@@identity as identity'))) {
        return FALSE;
      }
      return $r->next('identity');
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
      
      return mysql_affected_rows($this->handle);
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
      
      return mysql_affected_rows($this->handle);
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
      
      return mysql_affected_rows($this->handle);
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
      return $rows;
    }
    
    /**
     * Execute any statement
     *
     * @access  public
     * @param   mixed* args
     * @return  &rdbms.sybase.MySQLResultSet or FALSE to indicate failure
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
        $result= mysql_unbuffered_query($sql, $this->handle, $this->flags & DB_STORE_RESULT);
      } else {
        $result= mysql_query($sql, $this->handle);
      }

      if (FALSE === $result) {
        return throw(new SQLException(
          'Statement failed: '.mysql_error($this->handle), 
          $sql, 
          mysql_errno($this->handle)
        ));
      } else {
        return new MySQLResultSet($result);
      }
    }
  }
?>
