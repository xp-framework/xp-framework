<?php
/* This class is part of the XP framework's people's experiment
 *
 * $Id$ 
 */

  /**
   * Test class
   *
   * @see      xp://ResourcePool
   * @purpose  Testing
   */
  class Test extends Object {
    var
      $conn   = NULL;

    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      ResourcePool::bind($this);
    }
  
    /**
     * Set connection
     *
     * @access  public
     * @param   &rdbms.DBConnection conn
     */
    #[@inject(name = 'xp://env/rdbms/orders')]
    function setConnection(&$conn) {
      $this->conn= &$conn;
    }
    
    /**
     * Creates a string representation of this object.
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return sprintf(
        '%s[%s]@{conn= %s[%s]}',
        $this->getClassName(),
        $this->hashCode(),
        $this->conn ? $this->conn->getClassName() : '(n/a)',
        $this->conn ? $this->conn->hashCode() : '(n/a)'
      );
    }
  }
?>
