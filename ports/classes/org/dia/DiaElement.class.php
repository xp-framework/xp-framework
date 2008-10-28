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
   * Base class for all simple (leaf) value DIAgram elements
   *
   */
  class DiaElement extends Object implements DiaComponent {
    
    public
      $value= NULL;

    /**
     * Create new DiaElement instance
     *
     * @param   mixed value
     */
    public function __construct($value= NULL) {
      if (isset($value)) $this->setValue($value);
    }

    /**
     * Get value of this DiaElement
     *
     * @return  mixed
     */
    public function getValue() {
      return $this->value;
    }

    /**
     * Set the value of this DiaElement
     *
     * @param   mixed value
     */
    // TODO: xpath will probably not work as expected!
    #[@fromDia(xpath= 'text() | @val', value= 'string')]
    public function setValue($value) {
      $this->value= $value;
    }

    /******* Interface Methods *********/

    /**
     * Return the XML represenation of this DiaElement
     *
     * @return  &xml.Node
     */
    public function getNode() {
      $node= new Node($this->node_name);
      return $node;
    }

    /**
     * Accept a Visitor
     *
     * @param   &util.Visitor
     */
    public function accept($Visitor) {
      $Visitor->visit($this);
    }

    /**
     * DiaElement and its child-classes are 'leaf' elements which have no
     * children
     */
    public function addChild($Comp) { }

    /**
     * DiaElement and its child-classes are 'leaf' elements which have no
     * children
     */
    public function remChild($Comp) { }

    /**
     * DiaElement and its child-classes are 'leaf' elements which have no
     * children
     */
    public function getChildren() { }

  } 
?>
