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
     * @param   font font default NULL
     */
    function __construct($family= NULL, $style= NULL, $name= NULL) {
      if (isset($family)) $this->setFamily($family);
      if (isset($style)) $this->setStyle($style);
      if (isset($name)) $this->setName($name);
    }

    /**
     * Get the font family of this DiaFont
     *
     * @access  protected
     * @return  string
     */
    function getFamily() {
      return $this->family;
    }

    /**
     * Set the font family of this DiaFont
     *
     * @access  protected
     * @param   string family
     */
    function setFamily($family) {
      $this->family= $family;
    }

    /**
     * Get the font style of this DiaFont
     *
     * @access  protected
     * @return  int
     */
    function getStyle() {
      return $this->style;
    }

    /**
     * Set the font style of this DiaFont
     *
     * @access  protected
     * @param   int style
     */
    function setStyle($style) {
      $this->style= $style;
    }

    /**
     * Get the font name of this DiaFont
     *
     * @access  protected
     * @return  string
     */
    function getName() {
      return $this->name;
    }

    /**
     * Set the font name of this DiaFont
     *
     * @access  protected
     * @param   string name
     */
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Return XML representation of DiaComposite
     *
     * @access  protected
     * @return  &xml.Node
     */
    function &getNode() {
      $node= &parent::getNode();
      if (isset($this->family))
        $node->setAttribute('family', $this->family);
      if (isset($this->style))
        $node->setAttribute('style', $this->style);
      if (isset($this->name))
        $node->setAttribute('name', $this->name);
      return $node;
    }

  }
?>
