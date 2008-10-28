<?php
/*
 *
 * $Id$
 */

  uses(
    'org.dia.DiaObject',
    'org.dia.DiaAttribute'
  );

  /**
   * Represents a 'dia:object type="Standard - Text"' node
   *
   */
  class DiaTextObject extends DiaObject {

    /**
     * Initializes this TextObject with default values
     *
     */
    public function initialize() {
      // default positioning information
      $this->setPosition(array(0, 0));
      $this->setBoundingBox(array(array(0, 0), array(1, 1)));

      // add Text
      $this->set('text', new DiaAttribute('text'));
      $this->setText(new DiaText());

      // alignment
      $this->setVerticalAlign(3);
    }

    /**
     * Returns a unique name of this TextObject
     *
     * @return  string
     */
    public function getName() {
      return 'text_'.$this->getId();
    }

  }
?>
