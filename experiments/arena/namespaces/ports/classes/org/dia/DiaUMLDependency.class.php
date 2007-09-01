<?php
/*
 *
 * $Id:$
 */

  namespace org::dia;

  ::uses('org.dia.DiaUMLConnection');

  /**
   * Represents a UML Dependency line of a DIAgram
   */
  class DiaUMLDependency extends DiaUMLConnection {

    /**
     * Constructor of an UML dependency
     *
     */
    public function __construct() {
      parent::__construct('UML - Dependency', 1);
    }

    /**
     * Initialize this UMLDependency with default values
     *
     */
    public function initialize() {
      parent::initialize();
      $this->drawArrow(TRUE);
    }

    #[@fromDia(xpath= 'dia:attribute[@name="draw_arrow"]/dia:boolean/@val', value= 'boolean')]
    public function drawArrow($bool) {
      $this->setBoolean('draw_arrow', $bool);
    }

    /**
     * Set the ID and connection point of the object where the line begins
     *
     * @param   string id The diagram object ID
     * @param   int connpoint default 3 The connection point of the object
     */
    public function beginAt($id, $connpoint= 3) {
      parent::beginAt($id, $connpoint);
    }

    /**
     * Set the ID and connection point of the object where the line ends
     *
     * @param   string id The diagram object ID
     * @param   int connpoint default 4 The connection point of the object
     */
    public function endAt($id, $connpoint= 4) {
      parent::endAt($id, $connpoint);
    }

  }
?>
