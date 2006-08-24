<?php

  uses(
    'org.dia.DiaComponent'
  );

  /**
   * Base class for all simple DIAgram elements
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
     * @access  protected
     * @return  mixed
     */
    function getValue() {
      return $this->value;
    }

    /**
     * Set the value of this DiaElement
     *
     * @access  protected
     * @param   mixed value
     */
    #[@xmlmapping(xpath = '@val', type = 'string')]
    function setValue($value) {
      $this->value= $value;
    }

    /******* Interface Methods *********/

    /**
     * Return the XML represenation of this DiaElement
     *
     * @access  protected
     * @return  &xml.Node
     */
    function &getNode() {
      $node= &new Node($this->node_name);
      return $node;
    }

  } implements(__FILE__, 'DiaComponent');
?>
