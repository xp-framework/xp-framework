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
   * Represents a 'dia:enum' node
   */
  class DiaEnum extends DiaElement {

    public
      $node_name= 'dia:enum';

    /**
     * Return XML representation of DiaComposite
     *
     * @return  &xml.Node
     */
    public function getNode() {
      $node= parent::getNode();
      if (isset($this->value)) {
        $node->setAttribute('val', $this->value);
      } else {
        $node->setAttribute('val', 0); // default
      }
      return $node;
    }

  }
?>
