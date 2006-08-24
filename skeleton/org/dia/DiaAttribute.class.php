<?php

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

  class DiaAttribute extends DiaCompound {

    var
      $name= NULL, 
      $node_name= 'dia:attribute';

    function __construct($name= NULL, $value= NULL, $type= NULL) {
      if (isset($name)) $this->setName($name);

      if (isset($value)) {
        if (!isset($type)) $type= xp::typeOf($value);
        if ($type === 'integer') $type= 'int';
        if (in_array($type, array('float', 'double'))) $type= 'real';
        if ($type === 'bool') $type= 'boolean';
      }

      if (isset($type)) {
	      switch ($type) {
	        case 'int':     $this->add(new DiaInt($value)); break;
	        case 'real':    $this->add(new DiaReal($value)); break;
	        case 'string':  $this->add(new DiaString($value)); break;
	        case 'boolean': $this->add(new DiaBoolean($value)); break;
	        case 'enum':    $this->add(new DiaEnum($value)); break;
	        case 'point':   $this->add(new DiaPoint($value)); break;
	        case 'rectangle': $this->add(new DiaRectangle($value)); break;
	        case 'font':    $this->add(new DiaFont($value)); break;
	        case 'color':   $this->add(new DiaColor($value)); break;
	
	        default:
	          return throw(new IllegalArgumentException('Unkown type "'.$type.'"'));
	      }
      }
    }

    /**
     * Return the name of this DiaAttribute
     *
     * @access  protected
     * @return  string
     */
    function getName() {
      return $this->name;
    }

    /**
     * Set the name of this DiaAttribute
     *
     * @access  protected
     * @param   string name
     */
    #[@xmlmapping(xpath = '@name', type = 'string')]
    function setName($name) {
      $this->name= $name;
    }

    /************************* DiaComponent Functions *************************/

    /**
     * Return XML representation of DiaComposite
     *
     * @access  protected
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
