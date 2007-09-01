<?php
/* This class is part of the XP framework
 *
 * $Id: Transaction.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace rdbms;
 
  /**
   * Transaction
   *
   * <code>
   *   uses('rdbms.DriverManager');
   *
   *   $conn= &DriverManager::getConnection('sybase://user:password@server/database');
   *   $tran= &$conn->begin(new Transaction('test'));
   *   try(); {
   *     // ... execute SQL statements
   *   } if (catch('SQLException', $e)) {
   *     $tran->rollback();
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *   $tran->commit();
   * </code>
   *
   * @purpose  Wrap a transaction
   */
  class Transaction extends lang::Object {
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
