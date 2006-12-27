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
  abstract class Command extends Object {
    public
      $out = NULL,
      $err = NULL;
    
    /**
     * Run method. Implemented by subclasses.
     *
     */
    abstract function run();
  }
?>
