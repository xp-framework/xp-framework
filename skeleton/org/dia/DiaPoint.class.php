<?php

  uses(
    'org.dia.DiaElement'
  );

  class DiaPoint extends DiaElement {

    var
      $node_name= 'dia:point',
      $value= array();

    /**
     * Set the coordinates of this DiaPoint
     *
     * The size of the given coordinate-array must be zero or two!
     *
     * @access  protected
     * @param   array coordinates default array()
     * @return  bool
     */
    function setValue($coords) {
      // $coords must be an array with zero or two elements:
      if (!is_array($coords) or (!empty($coords) and sizeof($coords) !== 2)) return FALSE;
      $this->value= $coords;
      return TRUE;
    }

    /************************* DiaComponent Functions *************************/
        
    /**   
     * Return XML representation of DiaComposite
     *    
     * @access  protected 
     * @return  &xml.Node 
     */
    function &getNode() {
      $node= &parent::getNode();
      if (isset($this->value))
        $node->setAttribute('val', implode(',', $this->value));
      return $node;
    }     

  }
?>
