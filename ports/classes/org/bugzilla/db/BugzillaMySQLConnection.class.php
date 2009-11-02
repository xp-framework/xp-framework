<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.mysql.MySQLConnection');

  /**
   * Bugzilla MySQL connection. Automatically handles shadow db
   * writing.
   *
   * @see      xp://rdbms.mysql.MySQLConnection
   * @purpose  Specialized connection class
   */
  class BugzillaMySQLConnection extends MySQLConnection {
    public
      $shadow    = TRUE;
    
    public
      $_affected = -1,
      $_insert_id= -1;

    /**
     * Constructor
     *
     * @param   rdbms.DSN dsn
     */
    public function __construct($dsn) { 
      parent::__construct($dsn);
      $this->shadow= $this->dsn->getValue('shadow', FALSE);
    }
    
    /**
     * Retrieve identity
     *
     * @return  mixed identity value
     */
    public function identity() {
      return $this->_insert_id;
    }      

    /**
     * Execute an insert statement
     *
     * @param   mixed* args
     * @return  int number of affected rows
     * @throws  rdbms.SQLStatementFailedException
     */
    public function insert() { 
      $args= func_get_args();
      $args[0]= 'insert '.$args[0];
      if (!($r= call_user_func_array(array($this, 'query'), $args))) {
        return FALSE;
      }
      
      return $this->_insert_id;
    }
    
    
    /**
     * Execute an update statement
     *
     * @param   mixed* args
     * @return  int number of affected rows
     * @throws  rdbms.SQLStatementFailedException
     */
    public function update() {
      $args= func_get_args();
      $args[0]= 'update '.$args[0];
      if (!($r= call_user_func_array(array($this, 'query'), $args))) {
        return FALSE;
      }
      
      return $this->_affected;
    }
    
    /**
     * Execute an update statement
     *
     * @param   mixed* args
     * @return  int number of affected rows
     * @throws  rdbms.SQLStatementFailedException
     */
    public function delete() { 
      $args= func_get_args();
      $args[0]= 'delete '.$args[0];
      if (!($r= call_user_func_array(array($this, 'query'), $args))) {
        return FALSE;
      }
      
      return $this->_affected;
    }

    /**
     * Execute any statement
     *
     * @param   mixed* args
     * @return  rdbms.mysql.MySQLResultSet or FALSE to indicate failure
     * @throws  rdbms.SQLException
     */
    public function query() {
      $args= func_get_args();
      $sql= $this->_prepare($args);

      $res= parent::query($sql);
      
      if (TRUE !== $res || !$this->shadow) return $res;

      // Get the affected rows / insert_id of the query on the bugs db to check for
      // midway collisions. Otherwise you get the affected rows / insert_id of the 
      // shadow db insert    
      $this->_affected= mysql_affected_rows($this->handle);
      $this->_insert_id= mysql_insert_id($this->handle);

      // This was an SQL update, insert or delete, or: something that does
      // not return a resultset. Write it into the shadow log
      mysql_query(
        $this->_prepare(array('insert into shadowlog (command) values (%s)', $sql)),
        $this->handle
      );
      
      return $res;
    }
  }
?>
