<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('rdbms.DBTable', 'rdbms.SQLException');

  /**
   * Abstract base class for a database adapter for DBTable operations
   * 
   * @see   rdbms.DBAdapter
   */  
  class DBAdapter extends Object {
    public
      $conn=  NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &rdbms.DBConnection conn a database connection
     */
    public function __construct(&$conn) {
      $this->conn= &$conn;
      
    }

    /**
     * Get a table
     *
     * @access  abstract
     * @param   string name
     */    
    public function getTable($name) {}

    /**
     * Get tables
     *
     * @access  abstract
     * @param   string database
     */    
    public function getTables($database) {}
    
    /**
     * Get databaases
     *
     * @access  abstract
     * @param   string name
     */    
    public function getDatabases() {}
  }
?>
