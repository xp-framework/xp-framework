<?php
/*
 *
 * $Id$
 */

  uses('org.dia.DiaElement');

  /**
   * Represents a 'dia:int' node
   */
  class DiaInt extends DiaElement{

    public
      $node_name= 'dia:int';
  
    /**
     * Return XML representation of DiaComposite
     *
     * @return  &xml.Node
     */
    public function getNode() {
      $node= parent::getNode();
      if (isset($this->value)) $node->setAttribute('val', $this->value);
      return $node;
    }

  }
?>
