<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  /**
   * Base class for all commands
   *
   * @purpose  Abstract base class
   */
  class Command extends Object {
    var
      $out = NULL,
      $err = NULL;
    
    /**
     * Run method
     *
     * @model   abstract
     * @access  public
     */
    function run() { }
  }
?>
