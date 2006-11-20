<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaComponent',
    'xml.Node'
  );

  /**
   * Represents a 'dia:childnode' which is a link to the parent
   * object ID where this object is contained within.
   *
   */
  class DiaChildNode extends Object {

    var
      $node_name= 'dia:childnode';

    /**
     * Create a new 'dia:childnode' node
     *
     * @access  public
     * @param   string parent default 'O0' The first character is always a capital 'o', not zero!
     */
    function __construct($parent= 'O0') {
      $this->parent= $parent;
    }

    /**
     * Returns the parent object ID
     *
     * @access  public
     * @return  string
     */
    function getParentId() {
      return $this->parent;
    }

    /**
     * Set the parent object ID
     *
     * @access  public
     * @param   string parent
     */
    #[@fromDia(xpath= 'attribute::parent', value= 'string')]
    function setParentId($parent) {
      $this->parent= $parent;
    }

    /**
     * Return the XML representation of this node
     *
     * @access  public
     * @return  &xml.Node
     */
    function &getNode() {
      $Node= &new Node($this->node_name);
      $Node->setAttribute('parent', $this->parent);
      return $Node;
    }

    /**
     * Accept a Visitor
     *
     * @access  public
     * @param   &util.Visitor Visitor
     */
    function accept(&$Visitor) {
      $Visitor->visit($this);
    }

    /**
     * DiaChildnode is a 'leaf' element which has no children
     */
    function addChild(&$Comp) { }

    /**
     * DiaChildnode is a 'leaf' element which has no children
     */
    function remChild(&$Comp) { }

    /**
     * DiaChildnode is a 'leaf' element which has no children
     */
    function getChildren() { }

  } implements(__FILE__, 'org.dia.DiaComponent');
?>
