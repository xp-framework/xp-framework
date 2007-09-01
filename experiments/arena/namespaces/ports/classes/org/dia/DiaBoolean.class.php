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
   * Represents a 'dia:boolean' node
   */
  class DiaBoolean extends DiaElement {

    public
      $node_name= 'dia:boolean';

    /**
     * Return XML representation of DiaComposite
     *
     * @return  &xml.Node
     */
    public function getNode() {
      $node= parent::getNode();
      // TODO: the value should always be 'boolean'!
      if (isset($this->value)) {
        if (::xp::typeOf($this->value) === 'boolean') {
          $node->setAttribute('val', $this->value ? 'true' : 'false');
        } else {
          $node->setAttribute('val', $this->value === 'true' ? 'true' : 'false');
        }
      } else {
        $node->setAttribute('val', 'false'); // default
      }
      return $node;
    }

  }
?>
