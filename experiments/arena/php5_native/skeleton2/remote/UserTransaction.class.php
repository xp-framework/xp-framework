<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Remote transaction
   *
   * @see      xp://Remote#begin
   * @purpose  Transaction
   */
  class UserTransaction extends Object {
    public
      $_handler= NULL;

    /**
     * Commit this transaction
     *
     * @access  public
     */
    public function commit() {
      $this->_handler->commit($this);
    }
    
    /**
     * Rollback this transaction
     *
     * @access  public
     */
    public function rollback() {
      $this->_handler->rollback($this);
    }  
  }
?>
