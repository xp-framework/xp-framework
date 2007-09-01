<?php
/* This file is part of the XP framework's experiments
 *
 * $Id: Command.class.php 9895 2007-04-05 14:55:39Z friebe $
 */

  namespace util::cmd;

  ::uses('lang.Runnable');

  /**
   * Base class for all commands
   *
   * @purpose  Abstract base class
   */
  abstract class Command extends lang::Object implements lang::Runnable {
    public
      $out = NULL,
      $err = NULL;
    
  }
?>
