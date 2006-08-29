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
   * Predefined DIA composite elements like 'paper', 'grid', ...
   *
   */
  class DiaComposite extends DiaCompound {

    var
      $type= NULL,
      $node_name= 'dia:composite';

    /**
     * Constructor for general composite elements in 'dia' diagrams
     *
     * Predefined types:
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
      if (!isset($type)) return;

      $this->setType($type);
      switch ($type) {
        case 'paper':
          $this->add(new DiaAttribute('name', 'A4', 'string'));
          $this->add(new DiaAttribute('tmargin', 2.8222, 'real'));
          $this->add(new DiaAttribute('bmargin', 2.8222, 'real'));
          $this->add(new DiaAttribute('lmargin', 2.8222, 'real'));
          $this->add(new DiaAttribute('rmargin', 2.8222, 'real'));
          $this->add(new DiaAttribute('is_portrait', TRUE, 'boolean'));
          $this->add(new DiaAttribute('scaling', 1, 'real'));
          $this->add(new DiaAttribute('fitto', FALSE, 'boolean'));
          break;

        case 'grid':
          $this->add(new DiaAttribute('width_x', 1, 'float'));
          $this->add(new DiaAttribute('width_y', 1, 'float'));
          $this->add(new DiaAttribute('visible_x', 1, 'int'));
          $this->add(new DiaAttribute('visible_y', 1, 'int'));
          $this->add(new DiaComposite('color'));
          break;
          
        case 'guides':
          $this->add(new DiaAttribute('hguides'));
          $this->add(new DiaAttribute('vguides'));
          break;

        case 'text':
          $this->add(new DiaAttribute('string', $value, 'string'));
          $this->add(new DiaAttribute('font', array('sans', 0, 'Helvetica'), 'font'));
          $this->add(new DiaAttribute('height', 0.8, 'real'));
          $this->add(new DiaAttribute('pos', array(3, 4), 'point'));
          $this->add(new DiaAttribute('color', '#000000', 'color'));
          $this->add(new DiaAttribute('alignment', 0, 'enum'));
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
    function getType() {
      return $this->type;
    }

    /**
     * Set the type of this DiaComposite
     *
     * @access  protected
     * @param   string type
     */
    function setType($type) {
      $this->type= $type;
    }

    /**
     * Return XML representation of DiaComposite
     *
     * @access  protected
     * @return  &xml.Node
     */
    function &getNode() {
      $node= &parent::getNode();
      if (isset($this->type))
        $node->setAttribute('type', $this->type);
      return $node;
    }

  } implements(__FILE__, 'org.dia.DiaComponent');
?>
