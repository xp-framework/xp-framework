<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaComposite'
  );

  class DiaUMLMethod extends DiaComposite {

    var
      $type= 'umloperation';

    /**
     * Initialize this UMLMethod object with default values
     *
     * @access  protected
     */
    function initialize() {
      // default values
      $this->setString('name', '__noname__');
      $this->setString('stereotype', NULL);
      $this->setString('type', 'void');
      $this->setString('comment', NULL);

      // add essencial nodes
      $this->set('parameters', new DiaAttribute('parameters'));

      // default flags
      $this->setEnum('visibility', 0);
      $this->setBoolean('abstract', FALSE);
      $this->setEnum('inheritance_type', 2);
      $this->setBoolean('query', FALSE);
      $this->setBoolean('class_scope', FALSE);
    }

    /**
     * Sets the 'stereotype' of the UML method
     *
     * @access  protected
     * @param   string stereotype
     */
    #[@fromDia(xpath= 'dia:attribute[@name="stereotype"]/dia:string', value= 'string')]
    function setStereotype($stereotype) {
      $this->setString('stereotype', $stereotype);
    }

    /**
     * Sets the 'comment' of the UML method
     *
     * @access  protected
     * @param   string comment
     */
    #[@fromDia(xpath= 'dia:attribute[@name="comment"]/dia:string', value= 'string')]
    function setComment($comment) {
      $this->setString('comment', $comment);
    }

    /**
     * Sets the 'visibility' of the UML method
     *
     * @access  protected
     * @param   int visibility
     */
    #[@fromDia(xpath= 'dia:attribute[@name="visibility"]/dia:enum/@val', value= 'enum')]
    function setVisibility($visiblility) {
      $this->setEnum('visibility', $visibility);
    }

    /**
     * Sets the 'abstract' attribute of the UML method
     *
     * @access  protected
     * @param   bool abstract
     */
    #[@fromDia(xpath= 'dia:attribute[@name="abstract"]/dia:boolean/@val', value= 'boolean')]
    function setAbstract($abstract) {
      $this->setBoolean('abstract', $abstract);
    }

    /**
     * Sets the 'class_scope' attribute of the UML method
     *
     * @access  protected
     * @param   bool class_scope
     */
    #[@fromDia(xpath= 'dia:attribute[@name="class_scope"]/dia:boolean/@val', value= 'boolean')]
    function setClassScope($class_scope) {
      $this->setBoolean('class_scope', $class_scope);
    }

    /**
     * Sets the 'inheritance_type' of the UML method
     *
     * @access  protected
     * @param   int inheritance_type
     */
    #[@fromDia(xpath= 'dia:attribute[@name="inheritance_type"]/dia:enum/@val', value= 'enum')]
    function setInheritanceType($inheritance_type) {
      $this->setEnum('inheritance_type', $inheritance_type);
    }

    /**
     * Sets the 'query' attribute of the UML method
     *
     * @access  protected
     * @param   bool query
     */
    #[@fromDia(xpath= 'dia:attribute[@name="query"]/dia:boolean/@val', value= 'boolean')]
    function setQuery($query) {
      $this->setBoolean('query', $query);
    }

    /**
     * Adds an UMLMethodParameter to the UML method
     *
     * @access  protected
     * @param   &org.dia.DiaUMLMethodParameter Parameter
     */
    #[@fromDia(xpath= 'dia:attribute[@name="parameters"]/*', class= 'org.dia.DiaUMLMethodParameter')]
    function addParameter($Parameter) {
      $Parameters= &$this->getChild('parameters');
      $Parameters->set($Parameter->getName(), $Parameter);
    }
 }
?>
