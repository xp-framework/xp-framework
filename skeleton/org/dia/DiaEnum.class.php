<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaElement'
  );

  /**
   * Represents a 'dia:enum' node
   */
  class DiaEnum extends DiaElement {

    var
      $node_name= 'dia:enum';

    /**
     * Return XML representation of DiaComposite
     *
     * @access  protected
     * @return  &xml.Node
     */
    function &getNode() {
      $node= &parent::getNode();
      if (isset($this->value))
        $node->setAttribute('val', $this->value);
      return $node;
    }

  }
?>
