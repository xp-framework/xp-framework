<?php
/*
 *
 * $Id$
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
  class DiaComposite extends DiaCompound implements DiaComponent {

    public
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
    public function __construct($type= NULL) {
      if (isset($type)) $this->setNodeType($type);
      $this->initialize();
    }

    /**
     * Initializes a generic 'composite' object
     *
     */
    public function initialize() {
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
          throw(new IllegalArgumentException('Undefined type "'.$type.'"'));
      }
    }

    /**
     * Return the type of this DiaComposite
     *
     * @return  int
     */
    public function getNodeType() {
      return $this->type;
    }

    /**
     * Set the type of this DiaComposite
     *
     * @param   string type
     */
    #[@fromDia(xpath= '@type', value= 'string')]
    public function setNodeType($type) {
      $this->type= $type;
    }

    /**
     * Returns the value of the object
     *
     * @return  string
     */
    public function getValue() {
      return $this->getChildValue('value');
    }

    /**
     * Sets the value of the object
     *
     * @param   string value
     */
    #[@fromDia(xpath= 'dia:attribute[@name="value"]/dia:string', value= 'string')]
    public function setValue($value) {
      $this->setString('value', $value);
    }

    /**
     * Returns the type of the object
     *
     * @return  string
     */
    public function getType() {
      return $this->getChildValue('type');
    }

    /**
     * Sets the type of the object
     *
     * @param   string type
     */
    #[@fromDia(xpath= 'dia:attribute[@name="type"]/dia:string', value= 'string')]
    public function setType($type) {
      $this->setString('type', $type);
    }

    /***************************** ******************************/

    /**
     * Return XML representation of DiaComposite
     *
     * @return  &xml.Node
     */
    public function getNode() {
      $node= parent::getNode();
      if (isset($this->type)) {
        $node->setAttribute('type', $this->type);
      } elseif (!is('org.dia.DiaRole', $this)) {
        // DiaRole has no type...
        Console::writeLine("Composite 'type' is not set!");
      }
      return $node;
    }

  } 
?>
