<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaCompound'
  );

  /**
   * Represents a 'dia:layer' node
   */
  class DiaLayer extends DiaCompound {

    var
      $name= NULL, 
      $visibility= NULL,
      $node_name= 'dia:layer';


    /**
     * Create new instance of DiaLayer
     *
     * @access  public
     * @param   string name default NULL
     * @param   bool visible default NULL
     */
    function __construct($name= NULL, $visible= NULL) {
      if (isset($name)) $this->setName($name);
      if (isset($visible)) $this->setVisibility($visible);
    }

    // function initialize()

    /**
     * Get the name of this DiaLayer
     *
     * @access  protected
     * @return  string
     */
    function getName() {
      return $this->name;
    }

    /**
     * Set the name of this DiaLayer
     *
     * @access  protected
     * @param   string name
     */
    #[@fromDia(xpath= '@name', value= 'string')]
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get the visibility of this DiaLayer
     *
     * @access  protected
     * @return  bool
     */
    function getVisibility() {
      return $this->visibility;
    }

    /**
     * Set the visibility of this DiaLayer
     *
     * @access  protected
     * @param   bool visible
     */
    #[@fromDia(xpath= 'attribute::visible', value= 'boolean')]
    function setVisibility($visible) {
      $this->visibility= $visible;
    }

    #[@fromDia(xpath= 'dia:object[@type="UML - Class"]', class= 'org.dia.DiaUMLClass')]
    function addClass($Class) {
      $this->set($Class->getName(), $Class);
    }

    /**
     * Return XML representation of DiaComposite
     *    
     * @access  protected
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
