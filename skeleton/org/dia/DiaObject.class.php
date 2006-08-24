<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaComponent',
    'org.dia.DiaCompound',
    'org.dia.DiaAttribute',
    'org.dia.DiaComposite'
  );

  /**
   * Class representing an object in a DIAgram
   *
   */
  class DiaObject extends DiaCompound {

    var
      $type= NULL,
      $version= NULL,
      $id= NULL,
      $node_name= 'dia:object';

    /**
     * Constructor of an dia object
     *
     */
    function __construct($type= NULL, $version= NULL, $id= NULL) {
      if (!isset($type)) return;
      $this->setType($type);
      if (isset($version)) $this->setVersion($version);
      if (isset($id)) {
        $this->setId($id);
      } else {
        $this->setId(DiaDiagram::getId());
      }
      
      return;
      switch ($type) {

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

    function getVersion() {
      return $this->version;
    }

    function setVersion($version) {
      $this->version= $version;
    }

    function getId() {
      return $this->id;
    }

    function setId($id) {
      $this->id= $id;
    }

    /************************* Parent Methods *************************/

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
      if (isset($this->version))
        $node->setAttribute('version', $this->version);
      if (isset($this->id))
        $node->setAttribute('id', $this->id);
      return $node;
    }

  } implements(__FILE__, 'DiaComponent');
?>
