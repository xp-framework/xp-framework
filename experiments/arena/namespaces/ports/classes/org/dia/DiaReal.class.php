<?php
/*
 *
 * $Id:$
 */

  namespace org::dia;

  ::uses(
    'org.dia.DiaElement'
  );

  /**
   * Represents a 'dia:real' node
   */
  class DiaReal extends DiaElement {

    public
      $node_name= 'dia:real';

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
