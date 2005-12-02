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
    var
      $_handler= NULL;

    /**
     * Commit this transaction
     *
     * @access  public
     */
    function commit() {
      $this->_handler->commitTransaction($this);
    }
    
    /**
     * Rollback this transaction
     *
     * @access  public
     */
    function rollback() {
      $this->_handler->rollbackTransaction($this);
    }  
  }
?>
