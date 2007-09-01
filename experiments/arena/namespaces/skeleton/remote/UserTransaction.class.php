<?php
/* This class is part of the XP framework
 *
 * $Id: UserTransaction.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace remote;

  /**
   * Remote transaction
   *
   * @see      xp://remote.Remote#begin
   * @purpose  Transaction
   */
  class UserTransaction extends lang::Object {
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
