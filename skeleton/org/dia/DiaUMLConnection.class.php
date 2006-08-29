<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaObject',
    'org.dia.DiaAttribute',
    'org.dia.DiaConnections',
    'org.dia.DiaConnection'
  );

  /**
   * Base class for all kinds of UML connections (dependency, generalization,
   * realization, association, ...)
   */
  class DiaUMLConnection extends DiaObject {

    var
      $connections= NULL;

    /**
     * Constructor of an UML generalization
     *
     * @access  public
     * @param   string type Typically 'UML - $conntype'
     * @param   int version
     */
    function __construct($type, $version) {
      parent::__construct($type, $version);

      // positioning elements default to 0
      $this->add(new DiaAttribute('obj_pos', array(0, 0), 'point'));
      $this->add(new DiaAttribute('obj_bb', array(array(0, 0), array(0, 0)), 'rectangle'));
      // TODO: orth_points: attribute containing multiple points
      // TODO: orth_orient also...

      // defaults
      $this->add(new DiaAttribute('orth_autoroute', TRUE, 'boolean'));
      $this->add(new DiaAttribute('text_color', '#000000', 'color'));
      $this->add(new DiaAttribute('line_color', '#000000', 'color'));
      $this->setName();
      $this->setStereotype();

      // keep reference to 'connections' node
      $this->connections= &new DiaConnections();
      $this->add($this->connections);
    }

    /**
     * Set the name of the UML generalization
     *
     * @access  protected
     * @param   string name
     */
    function setName($name) {
      $this->add(new DiaAttribute('name', $name, 'string'));
    }

    /**
     * Set the stereotype of the UML generalization
     *
     * @access  protected
     * @param   string stereotype
     */
    function setStereotype($stereotype) {
      $this->add(new DiaAttribute('stereotype', $stereotype, 'string'));
    }

    /**
     * Set the ID of the object where the line begins
     *
     * HINT: UML generalization and realizes 'begins' at the object depended-on
     *
     * @param   string id
     * @param   int connpoint default 0
     */
    function beginAt($id, $connpoint= 0) {
      // TODO: check if already set!
      $this->connections->add(new DiaConnection("0, $id, $connpoint"));
    }

    /**
     * Set the ID of the object where line ends
     *
     * HINT: UML generalization and realizes 'ends' at the depending object
     *
     * @param   string id
     * @param   int connpoint default 0
     */
    function endAt($id, $connpoint= 0) {
      // TODO: check if already set!
      $this->connections->add(new DiaConnection("1, $id, $connpoint"));
    }

  }
?>
