<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaComposite'
  );

  class DiaUMLAttribute extends DiaComposite {

    var
      $type= 'umlattribute';

    /**
     * Initialize this UMLAttribute object with default values
     *
     * @access  protected
     */
    function initialize() {
      // default values and flags
      $this->setString('name', '__noname__');
      $this->setString('type', NULL);
      $this->setString('value', NULL);
      $this->setString('comment', NULL);
      $this->setEnum('visibility', 0);
      $this->setBoolean('abstract', FALSE);
      $this->setBoolean('class_scope', FALSE);
    }

    #[@fromDia(xpath= 'dia:attribute[@name="comment"]/dia:string', value= 'string')]
    function setComment($comment) {
      $this->set('comment', new DiaAttribute('comment', $comment, 'string'));
    }

    #[@fromDia(xpath= 'dia:attribute[@name="visibility"]/dia:enum/@val', value= 'enum')]
    function setVisibility($visiblility) {
      $this->set('visibility', new DiaAttribute('visibility', $visibility, 'enum'));
    }

    #[@fromDia(xpath= 'dia:attribute[@name="abstract"]/dia:boolean/@val', value= 'boolean')]
    function setAbstract($abstract) {
      $this->set('abstract', new DiaAttribute('abstract', $abstract, 'boolean'));
    }

    #[@fromDia(xpath= 'dia:attribute[@name="class_scope"]/dia:boolean/@val', value= 'boolean')]
    function setClassScope($class_scope) {
      $this->set('class_scope', new DiaAttribute('class_scope', $class_scope, 'boolean'));
    }
 }
?>
