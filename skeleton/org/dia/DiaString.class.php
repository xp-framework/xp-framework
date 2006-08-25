<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaElement'
  ); 

  /**
   * Represents a 'dia:string' node
   */
  class DiaString extends DiaElement {

    var
      $node_name= 'dia:string';

    /**
     * Return XML representation of DiaComposite
     *
     * @access  protected
     * @return  &xml.Node
     */
    function &getNode() {
      $node= &parent::getNode();
      if (isset($this->value) and $this->value !== '') 
        $node->setContent('#'.$this->value.'#');
      return $node;
    }

  }
?>
