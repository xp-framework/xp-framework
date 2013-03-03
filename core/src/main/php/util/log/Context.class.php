<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'util.log';

  /**
   * Interface to be implemented by all log contexts
   *
   */
  interface util·log·Context {

    /**
     * Creates the string representation of this log context (to be written
     * to log files)
     *
     * @return string
     */
    public function format();
  }
?>
