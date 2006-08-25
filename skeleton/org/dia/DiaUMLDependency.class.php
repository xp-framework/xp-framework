<?php
/*
 *
 * $Id:$
 */

  uses('org.dia.DiaUMLConnection');

  /**
   * Represents a UML Dependency line of a DIAgram
   */
  class DiaUMLDependency extends DiaUMLConnection {

    /**
     * Constructor of an UML dependency
     *
     * @access  public
     */
    function __construct() {
      parent::__construct('UML - Dependency', 1);
    }

  }
?>
