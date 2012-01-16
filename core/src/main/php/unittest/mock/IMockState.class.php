<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Interface for mock states (record/replay/verify)
   *
   */
  interface IMockState {

    /**
     * Handles a method invocation.
     *
     * @param   string method the method name
     * @param   var[] args an array of arguments
     * @return  var
     */
    public function handleInvocation($method, $args);
  }
?>
