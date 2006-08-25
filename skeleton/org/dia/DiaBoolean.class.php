<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaElement'
  );


  /**
   * Represents a 'dia:boolean' node
   */
  class DiaBoolean extends DiaElement {

    var
      $node_name= 'dia:boolean';

    /**
     * Return XML representation of DiaComposite
     *
     * @access  protected
     * @return  &xml.Node
     */
    function &getNode() {
      $node= &parent::getNode();
      if (isset($this->value)) 
        if (xp::typeOf($this->value) === 'boolean') {
          $node->setAttribute('val', $this->value ? 'true' : 'false');
        } else {
          $node->setAttribute('val', $this->value === 'true' ? 'true' : 'false');
        }
      return $node;
    }

  }
?>
