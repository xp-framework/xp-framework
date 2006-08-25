<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaElement'
  );

  /**
   * Represents a 'dia:real' node
   */
  class DiaReal extends DiaElement {

    var
      $node_name= 'dia:real';

    /**
     * Return XML representation of DiaComposite
     *
     * @access  protected
     * @return  &xml.Node
     */
    function &getNode() {
      $node= &parent::getNode();
      if (isset($this->value)) $node->setAttribute('val', $this->value);
      return $node;
    }

  }
?>
