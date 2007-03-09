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
    public
      $type,
      $value;
      
    /**
     * Constructor
     *
     * @param   mixed type
     * @param   mixed value
     */
    public function __construct($type, $value) {
      $this->type= $type;
      $this->value= $value;
    }  
  }
?>
