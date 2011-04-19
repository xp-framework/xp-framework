<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.ChainedException');

  /**
   * Indicates compilation failed. Details can be found by checking
   * this exception's cause.
   *
   * @see      xp://compiler.task.CompilationTask#run
   */
  class CompilationException extends ChainedException {
    
  }
?>
