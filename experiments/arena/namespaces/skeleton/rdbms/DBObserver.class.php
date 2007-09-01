<?php
/* This class is part of the XP framework
 *
 * $Id: DBObserver.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace rdbms;

  uses('util.Observer');

  /**
   * Generic DB observer interface.
   *
   * @purpose  DB observer interface
   */
  interface DBObserver extends util::Observer {
  
    /**
     * Retrieves an instance.
     *
     * @param   mixed argument
     * @return  rdbms.DBObserver
     */
    public static function instanceFor($arg);
  }
?>
