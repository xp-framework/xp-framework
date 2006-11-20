<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaElement'
  );

  /**
   * Represents a 'dia:font' node
   */
  class DiaFont extends DiaElement {

    var
      $family= NULL,
      $style= NULL,
      $name= NULL,
      $node_name= 'dia:font';

    /**
     * Creates new instance of DiaFont
     *
     * @access  public
     * @param   array font default NULL
     */
    function __construct($font= NULL) {
      if (isset($font['family'])) $this->setFamily($font['family']);
      if (isset($font['style'])) $this->setStyle($font['style']);
      if (isset($font['name'])) $this->setName($font['name']);
      $this->initialize();
    }

    function initialize() {
      if (!isset($this->family))
        $this->family= 'monospace';
      if (!isset($this->style)) 
        $this->style= 0;
      if (!isset($this->name))
        $this->name= 'Courier';
    }

    /**
     * Get the font family of this DiaFont
     *
     * @access  public
     * @return  string
     */
    function getFamily() {
      return $this->family;
    }

    /**
     * Set the font family of this DiaFont
     *
     * @access  public
     * @param   string family
     */
    #[@fromDia(xpath= '@family', value= 'string')]
    function setFamily($family) {
      $this->family= $family;
    }

    /**
     * Get the font style of this DiaFont
     *
     * @access  public
     * @return  int
     */
    function getStyle() {
      return $this->style;
    }

    /**
     * Set the font style of this DiaFont
     *
     * @access  public
     * @param   int style
     */
    #[@fromDia(xpath= '@style', value= 'int')]
    function setStyle($style) {
      $this->style= $style;
    }

    /**
     * Get the font name of this DiaFont
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }

    /**
     * Set the font name of this DiaFont
     *
     * @access  public
     * @param   string name
     */
    #[@fromDia(xpath = '@name', value= 'string')]
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Return XML representation of DiaComposite
     *
     * @access  public
     * @return  &xml.Node
     */
    function &getNode() {
      $Node= &parent::getNode();
      $Node->setAttribute('family', $this->family);
      $Node->setAttribute('style', $this->style);
      $Node->setAttribute('name', $this->name);
      return $Node;
    }

  }
?>
