<?php

  uses(
    'lang.Interface'
  );

  /**
   * Interface for all DiaElements and DiaCompounds
   */
  class DiaComponent extends Interface {

    var
      $node_name= NULL;

    /**
     * Return the XML representation of this object including the child objects
     *
     * @access  protected
     * @return  &xml.Node
     */
    function &getNode() {}
  }
?>
