<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaComponent',
    'org.dia.DiaCompound',
    'org.dia.DiaAttribute'
  );

  /**
   * Base class of all 'dia:composite' objects like 'paper', 'grid',
   * 'umlattribute', ...
   *
   * Implements the methods for getting/setting the following composite attributes
   * <ul>
   *  <li>@type</li>
   *  <li>value</li>
   *  <li>type</li>
   * </ul>
   */
  class DiaComposite extends DiaCompound {

    var
      $type= NULL,
      $node_name= 'dia:composite';

    /**
     * Constructor for general composite elements in 'dia' diagrams
     *
     * Predefined types: (TODO)
     * <ul>
     *  <li>paper</li>
     *  <li>grid</li>
     *  <li>guides</li>
     *  <li>text</li>
     *  <li>color</li>
     *  <li>umlattribute</li>
     *  <li>umloperation</li>
     *  <li>umlparameter</li>
     * </ul>
     *
     * @param   string type default NULL
     */
    function __construct($type= NULL) {
      if (isset($type)) $this->setNodeType($type);
      $this->initialize();
    }

    /**
     * Initializes a generic 'composite' object
     *
     * @access  public
     */
    function initialize() {
      $type= $this->getNodeType();
      if (!isset($type)) return;

      switch ($type) {

        case 'guides':
          $this->set('hguides', new DiaAttribute('hguides'));
          $this->set('vguides', new DiaAttribute('vguides'));
          break;

        case 'text':
          $this->set('string', new DiaAttribute('string', $value, 'string'));
          $this->set('font', new DiaAttribute('font', array('family' => 'sans', 'style' => 0, 'name' => 'Helvetica'), 'font'));
          $this->set('height', new DiaAttribute('height', 0.8, 'real'));
          $this->set('pos', new DiaAttribute('pos', array(3, 4), 'point'));
          $this->set('color', new DiaAttribute('color', '#000000', 'color'));
          $this->set('alignment', new DiaAttribute('alignment', 0, 'enum'));
          break;

        case 'color':
          break;

        case 'umlattribute':
        case 'umloperation':
        case 'umlparameter':
          break;

        default: 
          return throw(new IllegalArgumentException('Undefined type "'.$type.'"'));
      }
    }

    /**
     * Return the type of this DiaComposite
     *
     * @access  public
     * @return  int
     */
    function getNodeType() {
      return $this->type;
    }

    /**
     * Set the type of this DiaComposite
     *
     * @access  public
     * @param   string type
     */
    #[@fromDia(xpath= '@type', value= 'string')]
    function setNodeType($type) {
      $this->type= $type;
    }

    /**
     * Returns the value of the object
     *
     * @access  public
     * @return  string
     */
    function getValue() {
      return $this->getChildValue('value');
    }

    /**
     * Sets the value of the object
     *
     * @access  public
     * @param   string value
     */
    #[@fromDia(xpath= 'dia:attribute[@name="value"]/dia:string', value= 'string')]
    function setValue($value) {
      $this->setString('value', $value);
    }

    /**
     * Returns the type of the object
     *
     * @access  public
     * @return  string
     */
    function getType() {
      return $this->getChildValue('type');
    }

    /**
     * Sets the type of the object
     *
     * @access  public
     * @param   string type
     */
    #[@fromDia(xpath= 'dia:attribute[@name="type"]/dia:string', value= 'string')]
    function setType($type) {
      $this->setString('type', $type);
    }

    /***************************** ******************************/

    /**
     * Return XML representation of DiaComposite
     *
     * @access  public
     * @return  &xml.Node
     */
    function &getNode() {
      $node= &parent::getNode();
      if (isset($this->type)) {
        $node->setAttribute('type', $this->type);
      } elseif (!is('org.dia.DiaRole', $this)) {
        // DiaRole has no type...
        Console::writeLine("Composite 'type' is not set!");
      }
      return $node;
    }

  } implements(__FILE__, 'org.dia.DiaComponent');
?>
