<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Remote transaction
   *
   * @see      xp://remote.Remote#begin
   * @purpose  Transaction
   */
  class UserTransaction extends Object {
    public
      $_handler= NULL;

    /**
     * Commit this transaction
     *
     */
    public function commit() {
      $this->_handler->commit($this);
    }
    
    /**
     * Rollback this transaction
     *
     */
    public function rollback() {
      $this->_handler->rollback($this);
    }  
  }
?>
