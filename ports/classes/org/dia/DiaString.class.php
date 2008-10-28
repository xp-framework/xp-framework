<?php
/*
 *
 * $Id$
 */

  uses('org.dia.DiaElement'); 

  /**
   * Represents a 'dia:string' node
   */
  class DiaString extends DiaElement {

    public
      $node_name= 'dia:string';

    /**
     * Return XML representation of DiaComposite
     *
     * @return  &xml.Node
     */
    public function getNode() {
      $node= parent::getNode();
      if (isset($this->value)) {
        $node->setContent('#'.$this->value.'#');
      } else {
        $node->setContent('##');
      }
      return $node;
    }

  }
?>
