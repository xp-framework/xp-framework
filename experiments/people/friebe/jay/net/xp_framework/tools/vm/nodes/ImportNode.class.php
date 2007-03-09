<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Import
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class ImportNode extends VNode {
    public
      $source,
      $destination;
      
    /**
     * Constructor
     *
     * @param   mixed source
     * @param   mixed destincation
     */
    public function __construct($source, $destination) {
      $this->source= $source;
      $this->destination= $destination;
    }  
  }
?>
