<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses ('rdbms.mysql.MySQLConnection');

  /**
   * Bugzilla MySQL connection. Automatically handles shadow db
   * writing.
   *
   * @see      xp://rdbms.mysql.MySQLConnection
   * @purpose  Specialized connection class
   */
  class BugzillaMySQLConnection extends MySQLConnection {
    var
      $shadow   = TRUE;

    /**
     * Constructor
     *
     * @access  public
     * @param   &rdbms.DSN dsn
     */
    function __construct(&$dsn) { 
      parent::__construct($dsn); 
            
      $this->shadow= $this->dsn->getValue('shadow', FALSE);
    }

    /**
     * Execute any statement
     *
     * @access  public
     * @param   mixed* args
     * @return  &rdbms.mysql.MySQLResultSet or FALSE to indicate failure
     * @throws  rdbms.SQLException
     */
    function &query() {
      $args= func_get_args();
      $sql= $this->_prepare($args);

      try(); {
        $res= &parent::query($sql);
      } if (catch('SQLException', $e)) {
        return throw($e);
      }
      
      if (TRUE !== $res || !$this->shadow) return $res;
      
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
