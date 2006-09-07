<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaComposite'
  );

  class DiaUMLMethodParameter extends DiaComposite {

    var
      $type= 'umlparameter';

    /**
     * Initialize this UMLMethodParameter with default values
     *
     * @access  protected
     */
    function initialize() {
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
     * @access  protected
     * @param   string value
     */
    #[@fromDia(xpath= 'dia:attribute[@name="value"]/dia:string', value= 'string')]
    function setValue($value) {
      $this->setString('value', $value);
    }

    /**
     * Sets the 'comment' of the UML method parameter
     *
     * @access  protected
     * @param   string comment
     */
    #[@fromDia(xpath= 'dia:attribute[@name="comment"]/dia:string', value= 'string')]
    function setComment($comment) {
      $this->setString('comment', $comment);
    }

    /**
     * Sets the 'kind' of the UML method parameter
     *
     * @access  protected
     * @param   int kind
     */
    #[@fromDia(xpath= 'dia:attribute[@name="kind"]/dia:enum/@val', value= 'enum')]
    function setKind($kind) {
      $this->setEnum('kind', $kind);
    }
 }
?>
