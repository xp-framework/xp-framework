<?php
/*
 *
 * $Id:$
 */

  namespace org::dia;

  ::uses(
    'org.dia.DiaComposite'
  );

  class DiaText extends DiaComposite {

    public
      $type= 'text';

    /**
     * Initializes this Text object with default values
     *
     */
    public function initialize() {
      // default values
      $this->setText('__notext__');
      $this->setTextFont(array(
        'family'  => 'monospace',
        'style'   => 0,
        'name'    => 'Courier'
      ));
      $this->setHeight(0.8);
      $this->setPosition(array(0, 0));
      $this->setTextColor('#000000');

      // default flags
      $this->setAlignment(0);
    }

    /**
     * Returns a unique name for this text object
     *
     * @return  string
     */
    public function getName() {
      return "text".$this->hashCode();
    }

    /**
     * Sets the content of this Text object
     *
     * @param   string text
     */
    #[@fromDia(xpath= 'dia:attribute[@name="string"]/dia:string', value= 'string')]
    public function setText($text) {
      $this->setString('string', $text);
    }

    /**
     * Sets the 'font' of this Text object
     *
     * @param   array font Example: array('familiy' => 'monospace', 'style' => 0, 'name' => 'Courier')
     */
    #[@fromDia(xpath= 'dia:attribute[@name="font"]/dia:font', value= 'font')]
    public function setTextFont($font) {
      $this->setFont('font', $font);
    }

    /**
     * Sets the 'height' of the font
     *
     * @param   real height
     */
    #[@fromDia(xpath= 'dia:attribute[@name="height"]/dia:real/@val', value= 'real')]
    public function setHeight($height) {
      $this->setReal('height', $height);
    }

    /**
     * Sets the 'pos' of this Text object
     *
     * @param   array position Example: array(0, 0)
     */
    #[@fromDia(xpath= 'dia:attribute[@name="pos"]/dia:point/@val', value= 'array')]
    public function setPosition($position) {
      $this->setPoint('pos', $position);
    }

    /**
     * Sets the 'color' of this Text object
     *
     * @param   string color
     */
    #[@fromDia(xpath= 'dia:attribute[@name="color"]/dia:color/@val', value= 'string')]
    public function setTextColor($color) {
      $this->setColor('color', $color);
    }

    /**
     * Sets the 'alignment' of this Text object
     *
     * @param   int alignment
     */
    #[@fromDia(xpath= 'dia:attribute[@name="alignment"]/dia:enum/@val', value= 'int')]
    public function setAlignment($alignment) {
      $this->setEnum('alignment', $alignment);
    }
 }
?>
