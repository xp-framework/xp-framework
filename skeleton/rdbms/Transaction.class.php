<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Transaction
   *
   * <code>
   *   $s= &new Sybase(new DSN('sybase://user:password@server/databse'));
   *   $tran= &$s->begin(new Transaction('test'));
   *   try(); {
   *     // ... execute SQL statements
   *   } if (catch('SQLException', $e)) {
   *     $tran->rollback();
   *     $e->printStackTrace();
   *     exit();
   *   }
   *   $tran->commit();
   * </code>
   *
   * @purpose  Wrap a transaction
   */
  class Transaction extends Object {
    var
      $name     = '',
      $db       = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     */
    function __construct($name) {
      $this->name= $name;
      parent::__construct();
    }
    
    /**
     * Retreive transaction state
     *
     * @access  public
     */
    function getState() { 
      return $this->db->transtate($name);
    }
    
    /**
     * Rollback this transaction
     *
     * @access  public
     */
    function rollback() { 
      return $this->db->rollback($name);
    }
    
    /**
     * Commit this transaction
     *
     * @access  public
     */
    function commit() { 
      return $this->db->commit($name);
    }
  }
?>
