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
      // set name
      $this->setName($name);

      // set value if given
      if (isset($value)) {
        if (!isset($type)) $type= xp::typeOf($value);
        if ($type === 'integer') $type= 'int';
        if (in_array($type, array('float', 'double'))) $type= 'real';
        if ($type === 'bool') $type= 'boolean';
      }

      // set type if defined
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
