<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('rdbms.DBTable');

  /**
   * Abstract base class for a database adapter for DBTable operations
   * 
   * @see   rdbms.DBAdapter
   */  
  class DBAdapter extends Object {
    var
      $conn=  NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &Object a database connection
     */
    function __construct(&$conn) {
      $this->conn= &$conn;
      parent::__construct();
    }

    /**
     * Get a table
     *
     * @access  abstract
     * @param   string name
     */    
    function getTable($name) {}

    /**
     * Get tables
     *
     * @access  abstract
     * @param   string database
     */    
    function getTables($database) {}
    
    /**
     * Get databaases
     *
     * @access  abstract
     * @param   string name
     */    
    function getDatabases() {}
  }
?>
