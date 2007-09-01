<?php
/*
 *
 * $Id:$
 */

  namespace org::dia;

  ::uses(
    'org.dia.DiaComposite'
  );

  /**
   * Represents a 'dia:composite type="paper"' node
   *
   */
  class DiaPaper extends DiaComposite {

    public
      $type= 'paper';

    /**
     * Initializes this Paper object
     *
     */
    public function initialize() {
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
     * Returns the top margin of the paper
     *
     * @return  float
     */
    public function getTopMargin() {
      return $this->getChildValue('tmargin');
    }

    /**
     * Sets the top margin of the Paper
     *
     * @param   float rmargin
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="tmargin"]/dia:real/@val', value= 'real')]
    public function setTopMargin($tmargin) {
      $this->setReal('tmargin', $tmargin);
    }

    /**
     * Returns the bottom margin of the paper
     *
     * @return  float
     */
    public function getBottomMargin() {
      return $this->getChildValue('bmargin');
    }

    /**
     * Sets the bottom margin of the Paper
     *
     * @param   float bmargin
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="bmargin"]/dia:real/@val', value= 'real')]
    public function setBottomMargin($bmargin) {
      $this->setReal('bmargin', $bmargin);
    }

    /**
     * Returns the left margin of the paper
     *
     * @return  float
     */
    public function getLeftMargin() {
      return $this->getChildValue('lmargin');
    }

    /**
     * Sets the left margin of the Paper
     *
     * @param   float lmargin
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="lmargin"]/dia:real/@val', value= 'real')]
    public function setLeftMargin($lmargin) {
      $this->setReal('lmargin', $lmargin);
    }

    /**
     * Returns the right margin of the paper
     *
     * @return  float
     */
    public function getRightMargin() {
      return $this->getChildValue('rmargin');
    }

    /**
     * Sets the right margin of the Paper
     *
     * @param   float rmargin
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="rmargin"]/dia:real/@val', value= 'real')]
    public function setRightMargin($rmargin) {
      $this->setReal('rmargin', $rmargin);
    }

    /**
     * Returns TRUE if the paper has 'portrait' orientation, FALSE means
     * 'landscape'
     *
     * @return  boole
     */
    public function getPortrait() {
      return $this->getChildValue('is_portrait');
    }

    /**
     * Sets the 'is_portrait' attribute of the Paper object
     *
     * @param   bool portrait
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="is_portrait"]/dia:boolean/@val', value= 'boolean')]
    public function setPortrait($portrait) {
      $this->setBoolean('is_portrait', $portrait);
    }

    /**
     * Returns the scaling of the paper
     *
     * @return  float
     */
    public function getScaling() {
      return $this->getChildValue('scaling');
    }

    /**
     * Sets the 'scaling' of the Paper object
     *
     * @param   float scaling
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="scaling"]/dia:real/@val', value= 'real')]
    public function setScaling($scaling) {
      $this->setReal('scaling', $scaling);
    }

    /**
     * Returns TRUE if the paper is to be fittet onto a fixed numer of
     * horizontal and vertical sheets
     *
     * @return  bool
     */
    public function getFitTo() {
      return $this->getChildValue('fitto');
    }

    /**
     * If this is set to TRUE, two additional attributes names 'fitwidth' and
     * 'fitheight' tell on how many sheets to fit the diagram
     *
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="fitto"]/dia:boolean/@val', value= 'boolean')]
    public function setFitTo($fitto) {
      $this->setBoolean('fitto', $fitto);
    }

    /**
     * Returns the number of sheets the diagram should be fit to horizontally
     *
     * @return  int
     */
    public function getFitWidth() {
      return $this->getChildValue('fitwidth');
    }

    /**
     * Sets the 'fitwidth' of this Paper object: specifies on how many sheets
     * (horizontal) the diagram should be fitted if 'fitto' is TRUE
     *
     * @param   int width
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="fitwidth"]/dia:int/@val', value= 'int')]
    public function setFitWidth($width) {
      $this->setInt('fitwidth', $width);
    }

    /**
     * Returns the number of sheets the diagram should be fit to vertically
     *
     * @return  int
     */
    public function getFitHeight() {
      return $this->getChildValue('fitheight');
    }

    /**
     * Sets the 'fitheight' of this Paper object: specifies on how many sheets
     * (vertical) the diagram should be fitted if 'fitto' is TRUE
     *
     * @param   int height
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="fitheight"]/dia:int/@val', value= 'int')]
    public function setFitHeight($height) {
      $this->setInt('fitheight', $height);
    }
            
  }
?>
