<?php
/* This class is part of the XP framework
 *
 * $Id: TargetInvocationException.class.php 10967 2007-08-27 16:53:40Z friebe $ 
 */

  namespace lang::reflect;

  ::uses('lang.ChainedException');

  /**
   * Indicates an exception was thrown while reflectively invoking
   * a method or constructor.
   *
   * @see      xp://lang.reflect.Method
   * @see      xp://lang.reflect.Constructor
   * @purpose  Exception
   */
  class TargetInvocationException extends lang::ChainedException {
    
  }
?>
