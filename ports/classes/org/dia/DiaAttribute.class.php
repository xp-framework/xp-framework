<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaCompound',
    'org.dia.DiaInt',
    'org.dia.DiaReal',
    'org.dia.DiaString',
    'org.dia.DiaBoolean',
    'org.dia.DiaEnum',
    'org.dia.DiaPoint',
    'org.dia.DiaRectangle',
    'org.dia.DiaFont',
    'org.dia.DiaColor'
  );

  /**
   * Representation of a 'dia:attribute' node of a DIAgram
   *
   */
  class DiaAttribute extends DiaCompound {

    var
      $name= NULL, 
      $_type= NULL,
      $_value= NULL,
      $node_name= 'dia:attribute';

    /**
     * Constructor of 'dia:attribute'. If value (and type) are specified,
     * automatically creates child-node of appr. type, containting the value:
     * (i.e. <dia:string>#my_string#</dia:string>)
     *
     * @param   string name Attribute name
     * @param   mixed value default NULL Attribute value 
     * @param   string type default NULL Attrubute type
     * @throws  lang.IllegalArgumentException
     */
    function __construct($name, $value= NULL, $type= NULL) {
      if (!isset($name)) 
        return throw(new IllegalArgumentException('Parameter "name" is required!'));

      $this->setName($name);
      if (isset($value)) $this->_value= $value;
      if (isset($type)) $this->_type= $type;

      $this->initialize();
    }

    /**
     * Initializes the attribute with default values, depending on $type
     *
     * @access  public
     */
    function initialize() {
      // determine type from value if type is not set
      if (isset($this->_value)) {
        if (!isset($this->_type)) $this->_type= xp::typeOf($this->_value);
        if ($this->_type === 'integer') $this->_type= 'int';
        if (in_array($this->_type, array('float', 'double'))) $this->_type= 'real';
        if ($this->_type === 'bool') $this->_type= 'boolean';
        $value= $this->_value;
      } else {
        $value= NULL;
      }

      // set type if defined
      if (isset($this->_type)) {
        switch ($this->_type) {
          case 'int':     $this->set('value', new DiaInt($value)); break;
          case 'real':    $this->set('value', new DiaReal($value)); break;
          case 'string':  $this->set('value', new DiaString($value)); break;
          case 'boolean': $this->set('value', new DiaBoolean($value)); break;
          case 'enum':    $this->set('value', new DiaEnum($value)); break;
          case 'point':   $this->set('value', new DiaPoint($value)); break;
          case 'rectangle': $this->set('value', new DiaRectangle($value)); break;
          case 'font':    $this->set('value', new DiaFont($value)); break;
          case 'color':   $this->set('value', new DiaColor($value)); break;
  
          default:
            return throw(new IllegalArgumentException('Unkown type "'.$this->_type.'"'));
        }
      }
    }

    /**
     * Return the name of this DiaAttribute
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }

    /**
     * Set the name of this DiaAttribute
     *
     * @access  public
     * @param   string name
     */
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
      $node= &parent::getNode();
      if (isset($this->name))
        $node->setAttribute('name', $this->name);
      return $node;
    }

  }
?>
