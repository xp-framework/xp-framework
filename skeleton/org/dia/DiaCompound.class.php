<?php
/*
 *
 * $Id:$
 */

  uses(
    'xml.Node',
    'org.dia.DiaComponent',
    'org.dia.DiaAttribute',
    'lang.IllegalArgumentException'
  );

  /**
   * Base class of all complex elements in a DIAgram
   *
   * Implements the methods for getting/setting the following attributes:
   * <ul>
   *  <li>name</li>
   * </ul>
   *
   */
  class DiaCompound extends Object {

    var
      $children= array();

    /**
     * Set the DiaComponent object of the specified name
     *
     * @param   string name
     * @param   &org.dia.DiaComponent Component
     * @throws  lang.IllegalArgumentException
     */
    function set($name, &$Component) {
      if (!is('org.dia.DiaComponent', $Component)) {
        $name= xp::typeOf($Component);
        if (is_object($Component)) $name= $Component->getClassName();
        return throw(new IllegalArgumentException("Wrong object type: $name"));
      }
      $this->children[$name]= &$Component;
    }

    /**
     * Creates a new 'boolean' node and assigns it to $name
     *
     * @access  protected
     * @param   string name
     * @param   bool boolean
     */
    function setBoolean($name, $boolean) {
      $this->set($name, new DiaAttribute($name, $boolean, 'boolean'));
    }

    /**
     * Creates a new 'int' node and assigns it to $name
     *
     * @access  protected
     * @param   string name
     * @param   int int
     */
    function setInt($name, $int) {
      $this->set($name, new DiaAttribute($name, $int, 'int'));
    }

    /**
     * Creates a new 'real' node and assigns it to $name
     *
     * @access  protected
     * @param   string name
     * @param   float real
     */
    function setReal($name, $real) {
      $this->set($name, new DiaAttribute($name, $real, 'real'));
    }

    /**
     * Creates a new 'string' node and assigns it to $name
     *
     * @access  protected
     * @param   string name
     * @param   string string
     */
    function setString($name, $string) {
      $this->set($name, new DiaAttribute($name, $string, 'string'));
    }

    /**
     * Creates a new 'enum' node and assigns it to $name
     *
     * @access  protected
     * @param   string name
     * @param   int enum
     */
    function setEnum($name, $enum) {
      $this->set($name, new DiaAttribute($name, $enum, 'enum'));
    }

    /**
     * Creates a new 'point' node and assigns it to $name
     *
     * @access  protected
     * @param   string name
     * @param   array point
     */
    function setPoint($name, $point) {
      $this->set($name, new DiaAttribute($name, $point, 'point'));
    }

    /**
     * Creates a new 'rectangle' node and assigns it to $name
     *
     * @access  protected
     * @param   string name
     * @param   array points
     */
    function setRectangle($name, $points) {
      $this->set($name, new DiaAttribute($name, $points, 'rectangle'));
    }

    /**
     * Creates a new 'color' node and assigns it to $name
     *
     * @access  protected
     * @param   string name
     * @param   string color
     */
    function setColor($name, $color) {
      $this->set($name, new DiaAttribute($name, $color, 'color'));
    }

    /**
     * Creates a new 'font' node and assigns it to $name
     *
     * @access  protected
     * @param   string name
     * @param   array font
     */
    function setFont($name, $font) {
      $this->set($name, new DiaAttribute($name, $font, 'font'));
    }

    /**
     * Returns DiaComponent child by the given name of the component
     *
     * @access  protected
     * @param   string name
     * @return  &org.dia.DiaComponent
     */
    function &getChild($name) {
      return $this->children[$name];
    }

    /**
     * Returns the value of the DiaComponent by its name
     *
     * @param   string name
     * @return  mixed
     */
    function getChildValue($name) {
      if (NULL === ($Child= &$this->getChild($name))) return NULL;
      if (NULL === ($Value= &$Child->getChild('value'))) return NULL;
      return $Value->getValue();
    }

    /**
     * Returns all DiaComponent children of given object-type
     *
     * @access  protected
     * @param   string type
     * @return  &org.dia.DiaComponent
     */
    function &getChildByType($type) {
      $objs= array();
      foreach (array_keys($this->children) as $key) {
        if (is($type, $this->children[$key])) {
          $objs[]= &$this->children[$key];
        }
      }
      return $objs;
    }
    /**
     * TODO: better?
     * childByType: getType() composite, object
     * childByName: getName() attribute, layer(, font)
     */
    function &getChildAttributeByName($name) {
      $attrs= &$this->getChildByType('DiaAttribute');
      foreach (array_keys($attrs) as $key) {
        if ($attrs[$key]->getName() === $name) {
          return $attrs[$key];
        }
      }
      return NULL;
    }

    /*************************** Methods with annotations ****************************/

    /**
     * Return the name of the object
     *
     * @access  protected
     * @return  string
     */
    function getName() {
      return $this->getChildValue('name');
    }

    /**
     * Set the name of the object
     *
     * @access  protected
     * @param   string name
     */
    #[@fromDia(xpath= 'dia:attribute[@name="name"]/dia:string', value= 'string')]
    function setName($name) {
      $this->setString('name', $name);
    }

    /********** Interface Methods *************/

    /**
     * Returns XML representation of this DiaCompound
     *
     * @access  protected
     * @return  &xml.Node
     */
    function &getNode() {
      $node= &new Node($this->node_name);
      foreach (array_keys($this->children) as $key) {
        /*if (!is_object($this->children[$key])) {
          Console::writeLine('Node: '.xp::stringOf($node));
          Console::writeLine('NON-object: '.xp::stringOf($this->children));
        } else { */
          $node->addChild($this->children[$key]->getNode());
        //}
      }
      return $node;
    }

    /**
     * Accepts a Visitor object
     * 
     * @access  protected
     * @param   &lang.Visitor Visitor
     */
    function accept(&$Visitor) {
      $Visitor->visit($this);
      foreach (array_keys($this->children) as $key) {
        if (!is_object($this->children[$key])) {
          Console::writeLine('NON-object: '.xp::stringOf($this->children[$key]));
        } else {
          $this->children[$key]->accept($Visitor);
        }
      }
    }

  } implements(__FILE__, 'org.dia.DiaComponent');
?>
