<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a stack frame existing on the remote side
   *
   * @see      xp://lang.StackTraceElement
   * @purpose  Specialized StackTraceElement
   */
  class RemoteStackTraceElement extends StackTraceElement {
  
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
