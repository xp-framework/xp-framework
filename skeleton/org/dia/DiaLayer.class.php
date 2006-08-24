<?php

  uses(
    'org.dia.DiaCompound'
  );

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
    #[@xmlmapping(xpath = '@name', type = 'string')]
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
    #[@xmlmapping(xpath = '@visible', type = 'bool')]
    function setVisibility($visible) {
      $this->visibility= $visible;
    }

    /************************* Parent Functions *******************************/

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
      if (isset($this->visibility))
        $node->setAttribute('visible', $this->visibility ? 'true' : 'false');
      return $node;
    }    
  }
?>
