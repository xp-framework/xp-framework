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
    var
      $name,
      $generic;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed name
     * @param   mixed generic
     */
    function __construct($name, $generic) {
      $this->name= $name;
      $this->generic= $generic;
    }  
  }
?>
