<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('rdbms.DBTable', 'rdbms.SQLException');

  /**
   * Abstract base class for a database adapter for DBTable operations
   * 
   * @see      xp://rdbms.DBTable
   * @purpose  RDBMS reflection
   */  
  abstract class DBAdapter extends Object {
    public
      $conn=  NULL;
      
    /**
     * Constructor
     *
     * @param   rdbms.DBConnection conn a database connection
     */
    public function __construct($conn) {
      $this->conn= $conn;
    }

    /**
     * Get a table in the current database
     *
     * @param   string name
     * @param   string database default NULL if omitted, uses current database
     * @return  rdbms.DBTable
     */    
    public abstract function getTable($name, $database= NULL);

    /**
     * Get tables by database
     *
     * @param   string database default NULL if omitted, uses current database
     * @return  rdbms.DBTable[] array of DBTable objects
     */
    public abstract function getTables($database= NULL);
    
    /**
     * Get databaases
     *
     * @return  string[]
     */    
    public abstract function getDatabases();
  }
?>
