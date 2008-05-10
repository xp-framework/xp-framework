<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Transaction
   *
   * <code>
   *   uses('rdbms.DriverManager');
   *   
   *   $conn= DriverManager::getConnection('sybase://user:password@server/database');
   *   try {
   *     $tran= $conn->begin(new Transaction('test'));
   *     
   *     // ... execute SQL statements
   *     
   *     $tran->commit();
   *   } catch (SQLException $e) {
   *     $tran && $tran->rollback();
   *     throw $e;
   *   }
   * </code>
   *
   * @see      xp://rdbms.DBConnection#begin
   * @purpose  Wrap a transaction
   */
  class Transaction extends Object {
    public
      $name     = '',
      $db       = NULL;
      
    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name) {
      $this->name= $name;
    }
    
    /**
     * Retrieve transaction state
     *
     */
    public function getState() { 
      return $this->db->transtate($this->name);
    }
    
    /**
     * Rollback this transaction
     *
     */
    public function rollback() { 
      return $this->db->rollback($this->name);
    }
    
    /**
     * Commit this transaction
     *
     */
    public function commit() { 
      return $this->db->commit($this->name);
    }
  }
?>
