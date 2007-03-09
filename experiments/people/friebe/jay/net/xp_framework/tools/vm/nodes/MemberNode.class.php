<?php
/* This class is part of the XP framework
 *
 * $Id: VariableNode.class.php 7467 2006-07-23 16:24:40Z friebe $
 */

  uses('net.xp_framework.tools.vm.VNode');

  /**
   * Object member (either a member variable or a method)
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class MemberNode extends VNode {
    public
      $name,
      $offset;
      
    /**
     * Constructor
     *
     * @param   mixed name
     */
    public function __construct($name) {
      $this->name= $name;
    }
  }
?>
