<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  /**
   * SQL-Exceptions
   * 
   * @purpose  Exception
   */
  class SQLException extends Exception {
    var 
      $sql  = '';

    /**
     * Constructor
     *
     * @param   string message
     * @param   string sql default NULL the SQL query string sent
     */
    function __construct($message, $sql= NULL) {
      parent::__construct($message);
      $this->sql= $sql;
    }
    
    /**
     * Retreive string representation of the stack trace
     *
     * @access  publuc
     * @return  string
     */
    function getStackTrace() {
      return parent::getStackTrace().($this->sql 
        ? "  SQL [\n".$this->sql."\n  ]\n"
        : ''
      );
    }
  }
?>
