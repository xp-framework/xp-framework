<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Observer');

  /**
   * Generic DB observer interface.
   *
   * @purpose  DB observer interface
   */
  class DBObserver extends Observer {
  
    /**
     * Retrieves an instance.
     *
     * @model   static
     * @access  public
     * @param   mixed argument
     * @return  &rdbms.DBObserver
     */
    function &instanceFor($arg) { }
  }
?>
