<?php
/*
 *
 * $Id:$
 */

  namespace org::dia;

  ::uses(
    'org.dia.DiaElement'
  );

  /**
   * Represents a 'dia:font' node
   */
  class DiaFont extends DiaElement {

    public
      $family= NULL,
      $style= NULL,
      $name= NULL,
      $node_name= 'dia:font';

    /**
     * Creates new instance of DiaFont
     *
     * @param   array font default NULL
     */
    public function __construct($font= NULL) {
      if (isset($font['family'])) $this->setFamily($font['family']);
      if (isset($font['style'])) $this->setStyle($font['style']);
      if (isset($font['name'])) $this->setName($font['name']);
      $this->initialize();
    }

    public function initialize() {
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
     * @return  string
     */
    public function getFamily() {
      return $this->family;
    }

    /**
     * Set the font family of this DiaFont
     *
     * @param   string family
     */
    #[@fromDia(xpath= '@family', value= 'string')]
    public function setFamily($family) {
      $this->family= $family;
    }

    /**
     * Get the font style of this DiaFont
     *
     * @return  int
     */
    public function getStyle() {
      return $this->style;
    }

    /**
     * Set the font style of this DiaFont
     *
     * @param   int style
     */
    #[@fromDia(xpath= '@style', value= 'int')]
    public function setStyle($style) {
      $this->style= $style;
    }

    /**
     * Get the font name of this DiaFont
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Set the font name of this DiaFont
     *
     * @param   string name
     */
    #[@fromDia(xpath = '@name', value= 'string')]
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Return XML representation of DiaComposite
     *
     * @return  &xml.Node
     */
    public function getNode() {
      $Node= parent::getNode();
      $Node->setAttribute('family', $this->family);
      $Node->setAttribute('style', $this->style);
      $Node->setAttribute('name', $this->name);
      return $Node;
    }

  }
?>
