<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Annotation
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class AnnotationNode extends VNode {
    var
      $type,
      $value;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed type
     * @param   mixed value
     */
    function __construct($type, $value) {
      $this->type= $type;
      $this->value= $value;
    }  
  }
?>
