<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Observer');

  /**
   * Generic DB observer interface.
   *
   */
  interface BoundLogObserver extends Observer {
  
    /**
     * Retrieves an instance.
     *
     * @param   var argument
     * @return  rdbms.DBObserver
     */
    public static function instanceFor($arg);
  }
?>
