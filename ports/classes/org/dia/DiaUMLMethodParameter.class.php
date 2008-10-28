<?php
/*
 *
 * $Id$
 */

  uses('org.dia.DiaComposite');

  /**
   * Represents a 'dia:composite type="umlparameter"' node
   *
   */
  class DiaUMLMethodParameter extends DiaComposite {

    public
      $type= 'umlparameter';

    /**
     * Initialize this UMLMethodParameter with default values
     *
     */
    public function initialize() {
      // default values
      $this->setName('__noname__');
      $this->setType(NULL);
      $this->setValue(NULL);
      $this->setComment(NULL);

      // default flags
      $this->setKind(0);
    }

    /**
     * Sets the 'value' of the UML method parameter
     *
     * @param   string value
     */
    #[@fromDia(xpath= 'dia:attribute[@name="value"]/dia:string', value= 'string')]
    public function setValue($value) {
      $this->setString('value', $value);
    }

    /**
     * Sets the 'comment' of the UML method parameter
     *
     * @param   string comment
     */
    #[@fromDia(xpath= 'dia:attribute[@name="comment"]/dia:string', value= 'string')]
    public function setComment($comment) {
      $this->setString('comment', $comment);
    }

    /**
     * Sets the 'kind' of the UML method parameter
     *
     * @param   int kind
     */
    #[@fromDia(xpath= 'dia:attribute[@name="kind"]/dia:enum/@val', value= 'enum')]
    public function setKind($kind) {
      $this->setEnum('kind', $kind);
    }
 }
?>
