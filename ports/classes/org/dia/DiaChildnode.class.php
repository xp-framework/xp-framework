<?php
/*
 *
 * $Id$
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
  class DiaChildNode extends Object implements DiaComponent {

    public
      $node_name= 'dia:childnode';

    /**
     * Create a new 'dia:childnode' node
     *
     * @param   string parent default 'O0' The first character is always a capital 'o', not zero!
     */
    public function __construct($parent= 'O0') {
      $this->parent= $parent;
    }

    /**
     * Returns the parent object ID
     *
     * @return  string
     */
    public function getParentId() {
      return $this->parent;
    }

    /**
     * Set the parent object ID
     *
     * @param   string parent
     */
    #[@fromDia(xpath= 'attribute::parent', value= 'string')]
    public function setParentId($parent) {
      $this->parent= $parent;
    }

    /**
     * Return the XML representation of this node
     *
     * @return  &xml.Node
     */
    public function getNode() {
      $Node= new Node($this->node_name);
      $Node->setAttribute('parent', $this->parent);
      return $Node;
    }

    /**
     * Accept a Visitor
     *
     * @param   &util.Visitor Visitor
     */
    public function accept($Visitor) {
      $Visitor->visit($this);
    }

    /**
     * DiaChildnode is a 'leaf' element which has no children
     */
    public function addChild($Comp) { }

    /**
     * DiaChildnode is a 'leaf' element which has no children
     */
    public function remChild($Comp) { }

    /**
     * DiaChildnode is a 'leaf' element which has no children
     */
    public function getChildren() { }

  } 
?>
