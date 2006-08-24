<?php

  uses(
    'org.dia.DiaElement'
  ); 

  class DiaString extends DiaElement {

    var
      $node_name= 'dia:string';

    /************************* Parent Functions *************************/

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
