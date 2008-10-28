<?php
/*
 *
 * $Id$
 */

  uses('org.dia.DiaComposite');

  /**
   * Represents a 'dia:composite type="grid"' node
   *
   */
  class DiaGrid extends DiaComposite {

    public
      $type= 'grid';

    /**
     * Initialize this Grid object with default values
     *
     */
    public function initialize() {
      // default values
      $this->setWidthX(1);
      $this->setWidthY(1);
      $this->setVisibleX(1);
      $this->setVisibleY(1);
      $this->setGridColor(new DiaComposite('color')); // TODO?
    }

    /**
     * Returns the horizontal grid spacing
     *
     * @return  real
     */
    public function getWidthX() {
      return $this->getChildValue('width_x');
    }

    /**
     * Sets the horizontal grid spacing
     *
     * @param   real width
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="width_x"]/dia:real/@val', value= 'real')]
    public function setWidthX($width) {
      $this->setReal('width_x', $width);
    }

    /**
     * Returns the vertical grid spacing
     *
     * @return  real
     */
    public function getWidthY() {
      return $this->getChildValue('width_y');
    }

    /**
     * Sets the vertical grid spacing
     *
     * @param   real height
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="width_y"]/dia:real/@val', value= 'real')]
    public function setWidthY($width) {
      $this->setReal('width_y', $width);
    }

    /**
     * Returns the horizontal stepping of visible grid lines
     *
     * @return  real
     */
    public function getVisibleX() {
      return $this->getChildValue('visible_x');
    }

    /**
     * Sets the horizontal stepping of visible grid lines (show every line: 1)
     *
     * @param   int visible
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="visible_x"]/dia:int/@val', value= 'int')]
    public function setVisibleX($visible) {
      $this->setInt('visible_x', $visible);
    }

    /**
     * Returns the vertical stepping of visible grid lines
     *
     * @return  real
     */
    public function getVisibleY() {
      return $this->getChildValue('visible_y');
    }

    /**
     * Sets the vertical stepping of visible grid lines (show every line: 1)
     *
     * @param   int visible
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="visible_y"]/dia:int/@val', value= 'int')]
    public function setVisibleY($visible) {
      $this->setInt('visible_y', $visible);
    }

    /**
     * Returns the grid color object
     *
     * @return  &org.dia.DiaComposite
     */
    public function getGridColor() {
      return $this->getChild('color');
    }

    /**
     * Sets the color of the grid (why is this a composite???? TODO!)
     *
     * @param   &org.dia.DiaComposite Color
     */
    #[@fromDia(xpath= 'dia:composite/dia:composite[@type="color"]', class= 'org.dia.DiaComposite')]
    public function setGridColor($Color) {
      $this->set('color', $Color);
    }

  }
?>
