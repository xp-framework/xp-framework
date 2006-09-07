<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaComposite'
  );

  class DiaText extends DiaComposite {

    var
      $type= 'text';

    /**
     * Initializes this Text object with default values
     *
     * @access  protected
     */
    function initialize() {
      // default values
      $this->setText('__notext__');
      $this->setFont(array(
        'family'  => 'monospace',
        'style'   => 0,
        'name'    => 'Courier'
      ));
      $this->setHeight(0.8);
      $this->setPosition(array(0, 0));
      $this->setColor('#000000');

      // default flags
      $this->setAlignment(0);
    }

    /**
     * Sets the content of this Text object
     *
     * @access  protected
     * @param   string text
     */
    #[@fromDia(xpath= 'dia:attribute[@name="string"]/dia:string', value= 'string')]
    function setText($text) {
      $this->setString('string', $text);
    }

    /**
     * Sets the 'font' of this Text object
     *
     * @access  protected
     * @param   array font Example: array('familiy' => 'monospace', 'style' => 0, 'name' => 'Courier')
     */
    #[@fromDia(xpath= 'dia:attribute[@name="font"]/dia:font', value= 'array')]
    function setFont($font) {
      $this->setFont('font', $font);
    }

    /**
     * Sets the 'height' of the font
     *
     * @access  protected
     * @param   real height
     */
    #[@fromDia(xpath= 'dia:attribute[@name="height"]/dia:real/@val', value= 'real')]
    function setHeight($height) {
      $this->setReal('height', $height);
    }

    /**
     * Sets the 'pos' of this Text object
     *
     * @access  protected
     * @param   array position Example: array(0, 0)
     */
    #[@fromDia(xpath= 'dia:attribute[@name="pos"]/dia:point/@val', value= 'array')]
    function setPosition($position) {
      $this->setPoint('pos', $position);
    }

    /**
     * Sets the 'color' of this Text object
     *
     * @access  protected
     * @param   string color
     */
    #[@fromDia(xpath= 'dia:attribute[@name="color"]/dia:color/@val', value= 'string')]
    function setColor($color) {
      $this->setColor('color', $color);
    }

    /**
     * Sets the 'alignment' of this Text object
     *
     * @access  protected
     * @param   int alignment
     */
    #[@fromDia(xpath= 'dia:attribute[@name="alignment"]/dia:enum/@val', value= 'int')]
    function setAlignment($alignment) {
      $this->setEnum('alignment', $alignment);
    }
 }
?>
