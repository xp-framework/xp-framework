<?php
/*
 *
 * $Id:$
 */

  uses(
    'util.Component'
  );

  /**
   * Interface for all DiaElements and DiaCompounds
   *
   * Also extends interface 'Component' which makes the object structure visitor-ready:
   * 'accept(&$Visitor)', 'addChild(&$Comp)', 'remChild(&$Comp)' and 'getChildren()'
   * 
   * @purpose   Define a generic interface for all Dia* classes
   */
  interface DiaComponent {

    public
      $node_name= NULL;

    /**
     * Return the XML representation of this object including the child objects
     *
     * @access  public
     * @return  &xml.Node
     */
    public function &getNode();

  }
?>
