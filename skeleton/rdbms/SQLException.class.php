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
      $sql  = '',
      $code = 0;

    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   string sql default NULL the SQL query string sent
     * @param   int code default -1
     */
    function __construct($message, $sql= NULL, $code= -1) {
      parent::__construct($message);
      $this->sql= $sql;
      $this->code= $code;
    }
    
    /**
     * Retrieve string representation of the stack trace
     *
     * @access  publuc
     * @return  string
     */
    function toString() {
      return parent::toString().($this->sql 
        ? "  Code ".$this->code.", SQL [\n".$this->sql."\n  ]\n"
        : ''
      );
    }
  }
?>
