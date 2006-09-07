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
   * Represents a 'dia:attribute name="grid"' node with its 'dia:composite'
   * child node
   *
   */
  class DiaGrid extends DiaComposite {

    var
      $type= 'grid';

    /**
     * Initialize this Grid object with default values
     *
     * @access  protected
     */
    function initialize() {
      // default values
      $this->setWidthX(1);
      $this->setWidthY(1);
      $this->setVisibleX(1);
      $this->setVisibleY(1);
      $this->setColor(new DiaComposite('color')); // TODO
    }

    /**
     * Sets the horizontal grid spacing
     *
     * @access  protected
     * @param   real width
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="width_x"]/dia:real/@val', value= 'real')]
    function setWidthX($width) {
      $this->setReal('width_x', $width);
    }

    /**
     * Sets the vertical grid spacing
     *
     * @access  protected
     * @param   real height
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="width_y"]/dia:real/@val', value= 'real')]
    function setWidthY($width) {
      $this->setReal('width_y', $width);
    }

    /**
     * Sets the horizontal stepping of visible grid lines (show every line: 1)
     *
     * @access  protected
     * @param   int visible
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="visible_x"]/dia:int/@val', value= 'int')]
    function setVisibleX($visible) {
      $this->setInt('visible_x', $visible);
    }

    /**
     * Sets the vertical stepping of visible grid lines (show every line: 1)
     *
     * @access  protected
     * @param   int visible
     */
    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="visible_y"]/dia:int/@val', value= 'int')]
    function setVisibleY($visible) {
      $this->setInt('visible_y', $visible);
    }

    /**
     * Sets the color of the grid (why is this a composite???? TODO!)
     *
     * @access  protected
     * @param   &org.dia.DiaComposite Color
     */
    #[@fromDia(xpath= 'dia:composite/dia:composite[@type="color"]', class= 'org.dia.DiaComposite')]
    function setColor(&$Color) {
      $this->set('color', $Color);
    }

    /**
     * Returns the current object and all its children as xml.Node
     *
     * @access  protected
     * @return  &xml.Node
     */
    function &getNode() {
      $Node= &new Node('dia:attribute');
      $Node->setAttribute('name', 'grid');
      $Node->addChild(parent::getNode());
      return $Node; 
    }

  }
?>
