<?php
/*
 *
 * $Id:$
 */

  uses(
    'lang.Interface'
  );

  /**
   * Interface for all DiaElements and DiaCompounds
   *
   * Also defines methods, which make the object structure visitor-ready:
   * 'accept(&$Visitor)'
   * 
   * @purpose   Define a generic interface for all Dia* classes
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
    function &getNode() { }

    /**
     * Accept a Visitor instance
     *
     * @access  protected
     * @param   &lang.Visitor
     */
    function accept(&$Visitor) { }
  }
?>
