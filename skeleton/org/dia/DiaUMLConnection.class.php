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
     * Initialize this UMLConnection with default values
     * 
     * @access  protected
     */
    function initialize() {
      // default values
      $this->setName('__noname__');
      $this->setStereotype(NULL);
      //$this->setDirection();

      // add essencial nodes
      $this->set('connections', new DiaAttribute('connections'));

      // default flags

      // positioning information defaults
      $this->setPosition(array(0, 0));
      $this->setBoundingBox(array(array(0, 0), array(1, 1)));
      //$this->setOrthPoints(array(
      //  array(0, 0), array(1, 1), array(2, 2), array(3, 3)
      //));
      // TODO: orth_points: attribute containing multiple points
      // TODO: orth_orient also...
      //$this->setOrthAutoroute(TRUE);

      // defaults colors and fonts
      $this->setTextColor('#000000');
      $this->setLineColor('#000000');
    }

    /**
     * Set the name of the UML generalization
     *
     * @access  protected
     * @param   string name
     */
    function setName($name) {
      $this->set('name', new DiaAttribute('name', $name, 'string'));
    }

    /**
     * Set the stereotype of the UML generalization
     *
     * @access  protected
     * @param   string stereotype
     */
    function setStereotype($stereotype) {
      $this->set('stereotype', new DiaAttribute('stereotype', $stereotype, 'string'));
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
      $this->connections->set('begin', new DiaConnection("0, $id, $connpoint"));
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
      $this->connections->set('end', new DiaConnection("1, $id, $connpoint"));
    }

  }
?>
