<?php
/*
 *
 * $Id:$
 */

  namespace org::dia;

  ::uses(
    'org.dia.DiaComposite'
  );

  class DiaUMLMethod extends DiaComposite {

    public
      $type= 'umloperation';

    /**
     * Initialize this UMLMethod object with default values
     *
     */
    public function initialize() {
      // default values
      $this->setName('__noname__');
      $this->setStereotype(NULL);
      $this->setString('type', 'void');
      $this->setComment(NULL);

      // add essencial nodes
      $this->set('parameters', new DiaAttribute('parameters'));

      // default flags
      $this->setVisibility(0);
      $this->setAbstract(FALSE);
      $this->setInheritanceType(2);
      $this->setQuery(FALSE);
      $this->setClassScope(FALSE);
    }

    /**
     * Returns the stereotype of the UMLMethod
     *
     * @return  string
     */
    public function getStereotype() {
      return $this->getChildValue('stereotype');
    }

    /**
     * Sets the 'stereotype' of the UML method
     *
     * @param   string stereotype
     */
    #[@fromDia(xpath= 'dia:attribute[@name="stereotype"]/dia:string', value= 'string')]
    public function setStereotype($stereotype) {
      $this->setString('stereotype', $stereotype);
    }

    /**
     * Returns the comment of the UMLMethod
     *
     * @return  string
     */
    public function getComment() {
      return $this->getChildValue('comment');
    }

    /**
     * Sets the 'comment' of the UML method
     *
     * @param   string comment
     */
    #[@fromDia(xpath= 'dia:attribute[@name="comment"]/dia:string', value= 'string')]
    public function setComment($comment) {
      $this->setString('comment', $comment);
    }

    /**
     * Returns the visibility of the UMLMethod
     *
     * @return  int
     */
    public function getVisibility() {
      return $this->getChildValue('visibility');
    }

    /**
     * Sets the 'visibility' of the UML method
     *
     * @param   int visibility
     */
    #[@fromDia(xpath= 'dia:attribute[@name="visibility"]/dia:enum/@val', value= 'enum')]
    public function setVisibility($visibility) {
      $this->setEnum('visibility', $visibility);
    }

    /**
     * Returns TRUE if the UMLMethod is abstract
     *
     * @return  bool
     */
    public function getAbstract() {
      return $this->getChildValue('abstract');
    }

    /**
     * Sets the 'abstract' attribute of the UML method
     *
     * @param   bool abstract
     */
    #[@fromDia(xpath= 'dia:attribute[@name="abstract"]/dia:boolean/@val', value= 'boolean')]
    public function setAbstract($abstract) {
      $this->setBoolean('abstract', $abstract);
    }

    /**
     * Returns TRUE if the UMLMethod is in class scope (static)
     *
     * @return  bool
     */
    public function getClassScope() {
      return $this->getChildValue('class_scope');
    }

    /**
     * Sets the 'class_scope' attribute of the UML method
     *
     * @param   bool class_scope
     */
    #[@fromDia(xpath= 'dia:attribute[@name="class_scope"]/dia:boolean/@val', value= 'boolean')]
    public function setClassScope($class_scope) {
      $this->setBoolean('class_scope', $class_scope);
    }

    /**
     * Returns the inheritance type of the UMLMethod
     *
     * @return  int
     */
    public function getInheritanceType() {
      return $this->getChildValue('inheritance_type');
    }

    /**
     * Sets the 'inheritance_type' of the UML method
     *
     * @param   int inheritance_type
     */
    #[@fromDia(xpath= 'dia:attribute[@name="inheritance_type"]/dia:enum/@val', value= 'enum')]
    public function setInheritanceType($inheritance_type) {
      $this->setEnum('inheritance_type', $inheritance_type);
    }

    /**
     * Return TRUE if the UMLMethods 'query' flag is set
     *
     * @return  bool
     */
    public function getQuery() {
      return $this->getChildValue('query');
    }

    /**
     * Sets the 'query' attribute of the UML method
     *
     * @param   bool query
     */
    #[@fromDia(xpath= 'dia:attribute[@name="query"]/dia:boolean/@val', value= 'boolean')]
    public function setQuery($query) {
      $this->setBoolean('query', $query);
    }

    /**
     * Returns a list of DiaUMLMethodParameter objects
     *
     * @return  org.dia.DiaUMLMethodParameter[]
     */
    public function getParameters() {
      $Parameters= $this->getChild('parameters');
      return $Parameters->getChildren();
    }

    /**
     * Adds an UMLMethodParameter to the UML method
     *
     * @param   &org.dia.DiaUMLMethodParameter Parameter
     */
    #[@fromDia(xpath= 'dia:attribute[@name="parameters"]/dia:composite[@type="umlparameter"]', class= 'org.dia.DiaUMLMethodParameter')]
    public function addParameter($Parameter) {
      if (!is('org.dia.DiaUMLMethodParameter', $Parameter)) {
        throw(new lang::IllegalArgumentException(
          'Passed parameter is no "org.dia.DiaUMLMethodParameter"!'
        ));
      }
      $Parameters= $this->getChild('parameters');
      $Parameters->set($Parameter->getName(), $Parameter);
    }
 }
?>
