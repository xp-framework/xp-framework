<?php
/*
 *
 * $Id:$
 */

  uses('org.dia.DiaUMLConnection');

  class DiaUMLRealizes extends DiaUMLConnection {
    
    /**
     * Constructor of an UML realization
     *
     * @access  public
     */
    function __construct() {
      parent::__construct('UML - Realizes', 1);
    }
  }
?>
