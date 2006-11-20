<?php
/*
 *
 * $Id:$
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
    
    var
      $type= 'guides';

    /**
     * Initializes this object with default values
     *
     * @access  public
     */
    function initialize() {
      $this->set('hguides', new DiaAttribute('hguides'));
      $this->set('vguides', new DiaAttribute('vguides'));
    }

    /**
     * Returns the horizontal guides object
     *
     * @access  public
     * @return  &org.dia.DiaAttribute
     */
    function &getHorizontalGuides() {
      return $this->getChild('hguides');
    }

    /**
     * TODO
     *
     * @param   string? hguides
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="hguides"]', value= 'string')]
    function setHorizontalGuides($hguides) {
      $this->set('hguides', new DiaAttribute('hguides'));
    }

    /**
     * Returns the vertical guides object
     *
     * @access  public
     * @return  &org.dia.DiaAttribute
     */
    function &getVerticalGuides() {
      return $this->getChild('vguides');
    }

    /**
     * TODO
     * @param   string? vguides
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="vguides"]', value= 'string')]
    function setVerticalGuides($vguides) {
      $this->set('vguides', new DiaAttribute('vguides'));
    }

  }
?>
