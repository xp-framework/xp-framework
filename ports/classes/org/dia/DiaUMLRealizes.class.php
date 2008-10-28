<?php
/*
 *
 * $Id$
 */

  uses('org.dia.DiaUMLConnection');

  class DiaUMLRealizes extends DiaUMLConnection {

    public
      $conn_assoc= array(0 => 'end', 1 => 'begin');
    
    /**
     * Constructor of an UML realization
     *
     */
    public function __construct() {
      parent::__construct('UML - Realizes', 1);
    }

    /**
     * Set the ID and connection point of the object where the line begins
     *
     * UML - Realizes somehow 'begins' at the interface class...
     *
     * @param   string id The diagram object ID
     * @param   int connpoint default 0 The connection point of the object
     */
    public function beginAt($id, $connpoint= 0) {
      parent::endAt($id, $connpoint);
    }

    /**
     * Set the ID and connection point of the object where the line ends
     *
     * UML - Realizes somehow 'ends' at the implementing class...
     *
     * @param   string id The diagram object ID
     * @param   int connpoint default 5 The connection point of the object
     */
    public function endAt($id, $connpoint= 5) {
      parent::beginAt($id, $connpoint);
    }
  }
?>
