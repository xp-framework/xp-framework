<?php
/*
 *
 * $Id:$
 */

  namespace org::dia;

  ::uses('org.dia.DiaObject');

  /**
   * Represents a 'LargePackage' which may contain other objects (which get
   * linked to this package via a 'DiaChildnode' object
   *
   */
  class DiaUMLLargePackage extends DiaObject {
    
    /**
     * Constructor of an UML realization
     *
     */
    public function __construct() {
      parent::__construct('UML - LargePackage', 0);
    }

    /**
     * Initializes the Note with default values
     *
     */
    public function initialize() {
      $this->setPosition(array(0, 0));
      $this->setBoundingBox(array(array(0, 0), array(1, 1)));

      $this->setElementCorner(array(0, 0));
      $this->setElementWidth(0.0);
      $this->setElementHeight(0.0);
      $this->setLineColour('#000000');
      $this->setFillColour('#FFFFFF');
      $this->setTextColour('#000000');
      $this->setStereotype();
      $this->setName('largepackage_'.$this->getId());
    }

  }
?>
