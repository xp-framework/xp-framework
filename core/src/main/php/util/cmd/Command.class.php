<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  uses('lang.Runnable');

  /**
   * Base class for all commands
   *
   * @purpose  Abstract base class
   */
  abstract class Command extends Object implements Runnable {
    public
      #[@type('io.streams.StringReader')]
      $in  = NULL,
      #[@type('io.streams.StringWriter')]
      $out = NULL,
      #[@type('io.streams.StringWriter')]
      $err = NULL;
    
  }
?>
