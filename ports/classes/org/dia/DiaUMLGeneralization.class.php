<?php
/*
 *
 * $Id$
 */

  uses('org.dia.DiaUMLConnection');

  /**
   * Represents a UML Generalization line of a DIAgram
   */
  class DiaUMLGeneralization extends DiaUMLConnection {

    public
      $conn_assoc= array(0 => 'end', 1 => 'begin');

    /**
     * Constructor of an UML generalization
     *
     */
    public function __construct() {
      parent::__construct('UML - Generalization', 1);
    }

    /**
     * Set the ID of the object, where the line begins
     *
     * UML - Generalization somehow 'begins' at the parent class...
     *
     * @param   string id The diagram object ID
     * @param   int connpoint default 1 The connection point of the object
     */
    public function beginAt($id, $connpoint= 1) {
      parent::endAt($id, $connpoint);
    }

    /**
     * Set the ID of the object where the line ends
     *
     * UML - Generalization somehow 'ends' at the child class...
     *
     * @param   string id The diagram object ID
     * @param   int connpoint default 6 The connection point of the object
     */
    public function endAt($id, $connpoint= 6) {
      parent::beginAt($id, $connpoint);
    }
  }
?>
