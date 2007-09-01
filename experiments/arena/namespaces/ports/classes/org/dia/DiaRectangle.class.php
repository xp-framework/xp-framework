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
   * Represents a 'dia:rectangle' node
   */
  class DiaRectangle extends DiaElement {

    public
      $node_name= 'dia:rectangle',
      $value= array();

    /**
     * Set the corners of this DiaRectangle
     *
     * The corners-array must contain two arrays with two integer values!
     *
     * @param   array corners default array()
     * @return  bool
     */
    public function setValue($corners) {
      // $corners must be an array with two elements...
      if (!is_array($corners) or sizeof($corners) !== 2) return FALSE;
      // each element must be an array...
      if (!is_array($corners[0]) or !is_array($corners[1])) return FALSE;
      // ...containing two elements
      if (sizeof($corners[0]) !== 2 or sizeof($corners[1]) !== 2) return FALSE;
      $this->value= $corners;
      return TRUE;
    }

    /**   
     * Return XML representation of DiaComposite
     *    
     * @return  &xml.Node 
     */
    public function getNode() {
      $node= parent::getNode();
      if (
        is_array($this->value) and 
        sizeof($this->value) === 2 and
        is_array($this->value[0]) and
        sizeof($this->value[0]) === 2 and
        is_array($this->value[1]) and
        sizeof($this->value[1]) === 2
      ) {
        $node->setAttribute(
          'val', 
          implode(',', $this->value[0]).';'.implode(',', $this->value[1])
        );
      }
      return $node;
    }     

  }
?>
