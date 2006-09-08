<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaComposite'
  );

  /**
   * Represents a 'dia:composite type="paper"' node
   *
   */
  class DiaPaper extends DiaComposite {

    var
      $type= 'paper';

    /**
     * Initializes this Paper object
     *
     * @access  protected
     */
    function initialize() {
      // default values
      $this->setName('A4');
      $this->setTopMargin(2.8);
      $this->setBottomMargin(2.8);
      $this->setLeftMargin(2.8);
      $this->setRightMargin(2.8);

      // default flags
      $this->setPortrait(TRUE);
      $this->setScaling(1);
      $this->setFitTo(TRUE);
      $this->setFitWidth(1);
      $this->setFitHeight(1);
    }

    /**
     * Sets the top margin of the Paper
     *
     * @access  protected
     * @param   real rmargin
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="tmargin"]/dia:real/@val', value= 'real')]
    function setTopMargin($tmargin) {
      $this->setReal('tmargin', $tmargin);
    }

    /**
     * Sets the bottom margin of the Paper
     *
     * @access  protected
     * @param   real bmargin
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="bmargin"]/dia:real/@val', value= 'real')]
    function setBottomMargin($bmargin) {
      $this->setReal('bmargin', $bmargin);
    }

    /**
     * Sets the left margin of the Paper
     *
     * @access  protected
     * @param   real lmargin
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="lmargin"]/dia:real/@val', value= 'real')]
    function setLeftMargin($lmargin) {
      $this->setReal('lmargin', $lmargin);
    }

    /**
     * Sets the right margin of the Paper
     *
     * @access  protected
     * @param   real rmargin
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="rmargin"]/dia:real/@val', value= 'real')]
    function setRightMargin($rmargin) {
      $this->setReal('rmargin', $rmargin);
    }

    /**
     * Sets the 'is_portrait' attribute of the Paper object
     *
     * @access  protected
     * @param   bool portrait
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="is_portrait"]/dia:boolean/@val', value= 'boolean')]
    function setPortrait($portrait) {
      $this->setBoolean('is_portrait', $portrait);
    }

    /**
     * Sets the 'scaling' of the Paper object
     *
     * @access  protected
     * @param   real scaling
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="scaling"]/dia:real/@val', value= 'real')]
    function setScaling($scaling) {
      $this->setReal('scaling', $scaling);
    }

    /**
     * If this is set to TRUE, two additional attributes names 'fitwidth' and
     * 'fitheight' tell on how many sheets to fit the diagram
     *
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="fitto"]/dia:boolean/@val', value= 'boolean')]
    function setFitTo($fitto) {
      $this->setBoolean('fitto', $fitto);
    }

    /**
     * Sets the 'fitwidth' of this Paper object: specifies on how many sheets
     * (horizontal) the diagram should be fitted if 'fitto' is TRUE
     *
     * @access  protected
     * @param   int width
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="fitwidth"]/dia:int/@val', value= 'int')]
    function setFitWidth($width) {
      $this->setInt('fitwidth', $width);
    }

    /**
     * Sets the 'fitheight' of this Paper object: specifies on how many sheets
     * (vertical) the diagram should be fitted if 'fitto' is TRUE
     *
     * @access  protected
     * @param   int height
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="fitheight"]/dia:int/@val', value= 'int')]
    function setFitHeight($height) {
      $this->setInt('fitheight', $height);
    }
            
  }
?>
