<?php
/*
 *
 * $Id$
 */

  uses(
    'org.dia.DiaComposite',
    'org.dia.DiaAttribute'
  );

  /**
   * Represents a 'dia:composite type="guides"' node
   *
   */
  class DiaGuides extends DiaComposite {
    
    public
      $type= 'guides';

    /**
     * Initializes this object with default values
     *
     */
    public function initialize() {
      $this->set('hguides', new DiaAttribute('hguides'));
      $this->set('vguides', new DiaAttribute('vguides'));
    }

    /**
     * Returns the horizontal guides object
     *
     * @return  &org.dia.DiaAttribute
     */
    public function getHorizontalGuides() {
      return $this->getChild('hguides');
    }

    /**
     * TODO
     *
     * @param   string? hguides
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="hguides"]', value= 'string')]
    public function setHorizontalGuides($hguides) {
      $this->set('hguides', new DiaAttribute('hguides'));
    }

    /**
     * Returns the vertical guides object
     *
     * @return  &org.dia.DiaAttribute
     */
    public function getVerticalGuides() {
      return $this->getChild('vguides');
    }

    /**
     * TODO
     * @param   string? vguides
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="vguides"]', value= 'string')]
    public function setVerticalGuides($vguides) {
      $this->set('vguides', new DiaAttribute('vguides'));
    }

  }
?>
