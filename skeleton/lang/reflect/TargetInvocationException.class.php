<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.ChainedException');

  /**
   * Indicates an exception was thrown while reflectively invoking
   * a method or constructor.
   *
   * @see      xp://lang.reflect.Method
   * @see      xp://lang.reflect.Constructor
   * @purpose  Exception
   */
  class TargetInvocationException extends ChainedException {
    
  }
?>
