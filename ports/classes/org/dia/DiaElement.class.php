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
   * Base class for all simple (leaf) value DIAgram elements
   *
   */
  class DiaElement extends Object {
    
    var
      $value= NULL;

    /**
     * Create new DiaElement instance
     *
     * @access  public
     * @param   mixed value
     */
    function __construct($value= NULL) {
      if (isset($value)) $this->setValue($value);
    }

    /**
     * Get value of this DiaElement
     *
     * @access  public
     * @return  mixed
     */
    function getValue() {
      return $this->value;
    }

    /**
     * Set the value of this DiaElement
     *
     * @access  public
     * @param   mixed value
     */
    // TODO: xpath will probably not work as expected!
    #[@fromDia(xpath= 'text() | @val', value= 'string')]
    function setValue($value) {
      $this->value= $value;
    }

    /******* Interface Methods *********/

    /**
     * Return the XML represenation of this DiaElement
     *
     * @access  public
     * @return  &xml.Node
     */
    function &getNode() {
      $node= &new Node($this->node_name);
      return $node;
    }

    /**
     * Accept a Visitor
     *
     * @access  public
     * @param   &util.Visitor
     */
    function accept(&$Visitor) {
      $Visitor->visit($this);
    }

    /**
     * DiaElement and its child-classes are 'leaf' elements which have no
     * children
     */
    function addChild(&$Comp) { }

    /**
     * DiaElement and its child-classes are 'leaf' elements which have no
     * children
     */
    function remChild(&$Comp) { }

    /**
     * DiaElement and its child-classes are 'leaf' elements which have no
     * children
     */
    function getChildren() { }

  } implements(__FILE__, 'org.dia.DiaComponent');
?>
