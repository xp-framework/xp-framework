<?php
/* This class is part of the XP framework
 *
 * $Id: RemoteStackTraceElement.class.php 9547 2007-03-05 10:34:50Z friebe $ 
 */

  namespace remote;

  /**
   * Represents a stack frame existing on the remote side
   *
   * @see      xp://lang.StackTraceElement
   * @purpose  Specialized StackTraceElement
   */
  class RemoteStackTraceElement extends lang::StackTraceElement {
  
    /**
     * Returns qualified class name
     *
     * @param   string class unqualified name
     * @return  string
     */
    protected function qualifiedClassName($class) {
      return $class;
    }
  }
?>
