<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaCompound'
  );

  /**
   * Representation of a 'dia:layer' node. Every diagram may have multiple
   * layers, each containing objects (shapes) of the diagram.
   *
   */
  class DiaLayer extends DiaCompound {

    var
      $name= NULL, 
      $visibility= NULL,
      $node_name= 'dia:layer';


    /**
     * Create new instance of DiaLayer with the given name.
     *
     * @access  public
     * @param   string name
     * @param   bool visible default NULL
     * @throws  lang.IllegalArgumentException
     */
    function __construct($name, $visible= NULL) {
      if (!isset($name))
        return throw(new IllegalArgumentException('Parameter "name" is required!'));

      $this->setName($name);
      if (isset($visible)) $this->setVisibility($visible);

      parent::__construct();
    }

    /**
     * Get the name of this DiaLayer
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }

    /**
     * Set the name of this DiaLayer
     *
     * @access  public
     * @param   string name
     */
    #[@fromDia(xpath= '@name', value= 'string')]
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get the visibility of this DiaLayer
     *
     * @access  public
     * @return  bool
     */
    function getVisibility() {
      return $this->visibility;
    }

    /**
     * Set the visibility of this DiaLayer
     *
     * @access  public
     * @param   bool visible
     */
    #[@fromDia(xpath= 'attribute::visible', value= 'boolean')]
    function setVisibility($visible) {
      $this->visibility= $visible;
    }

    /**
     * Adds a DiaUMLClass object to the layer
     *
     * @access  public
     * @param   &org.dia.DiaUMLClass Class
     */
    #[@fromDia(xpath= 'dia:object[@type="UML - Class"]', class= 'org.dia.DiaUMLClass')]
    function addClass(&$Class) {
      $this->set($Class->getName(), $Class);
    }

    /**
     * Adds a non-UML object to the layer
     *
     * @access  public
     * @param   &org.dia.DiaObject Object
     */
    #[@fromDia(xpath= 'dia:object[not(starts-with(@type, "UML"))]', class= 'org.dia.DiaObject')]
    function addObject(&$Object) {
      $this->set($Object->getName(), $Object);
    }

    /**
     * Return XML representation of DiaComposite
     *    
     * @access  public
     * @return  &xml.Node
     */ 
    function &getNode() {
      $Node= &parent::getNode(); 
      if (isset($this->name))
        $Node->setAttribute('name', $this->name);
      if (isset($this->visibility))
        $Node->setAttribute('visible', $this->visibility ? 'true' : 'false');
      return $Node;
    }    
  }
?>
