<?php
/*
 *
 * $Id:$
 */

  uses('org.dia.DiaUMLConnection');

  /**
   * Represents a UML Generalization line of a DIAgram
   */
  class DiaUMLGeneralization extends DiaUMLConnection {

    /**
     * Constructor of an UML generalization
     *
     * @access  public
     */
    function __construct() {
      parent::__construct('UML - Generalization', 1);
    }
  }
?>
