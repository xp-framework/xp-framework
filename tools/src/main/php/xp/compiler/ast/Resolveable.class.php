<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Indicates a node is resolveable at compile-time
   *
   */
  interface Resolveable {
   
    /**
     * Resolve this node's value.
     *
     * @return  var
     */
    public function resolve(); 
  }
?>
