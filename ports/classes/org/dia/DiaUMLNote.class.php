<?php
/*
 *
 * $Id$
 */

  uses('org.dia.DiaObject');

  class DiaUMLNote extends DiaObject {
    
    /**
     * Constructor of an UML realization
     *
     */
    public function __construct() {
      parent::__construct('UML - Note', 0);
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
      $this->set('text', new DiaAttribute('text'));
    }

    /**
     * Returns the unique name of this UMLNote
     *
     * @return  string
     */
    public function getName() {
      return 'text_'.$this->getId();
    }

  }
?>
