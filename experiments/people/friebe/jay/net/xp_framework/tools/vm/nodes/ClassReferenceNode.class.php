<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * ClassReference
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class ClassReferenceNode extends VNode {
    public
      $name,
      $generic;
      
    /**
     * Constructor
     *
     * @param   mixed name
     * @param   mixed generic
     */
    public function __construct($name, $generic) {
      $this->name= $name;
      $this->generic= $generic;
    }  
  }
?>
