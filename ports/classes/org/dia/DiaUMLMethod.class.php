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
     * @access  public
     */
    function initialize() {
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
     * @access  public
     * @return  string
     */
    function getStereotype() {
      return $this->getChildValue('stereotype');
    }

    /**
     * Sets the 'stereotype' of the UML method
     *
     * @access  public
     * @param   string stereotype
     */
    #[@fromDia(xpath= 'dia:attribute[@name="stereotype"]/dia:string', value= 'string')]
    function setStereotype($stereotype) {
      $this->setString('stereotype', $stereotype);
    }

    /**
     * Returns the comment of the UMLMethod
     *
     * @access  public
     * @return  string
     */
    function getComment() {
      return $this->getChildValue('comment');
    }

    /**
     * Sets the 'comment' of the UML method
     *
     * @access  public
     * @param   string comment
     */
    #[@fromDia(xpath= 'dia:attribute[@name="comment"]/dia:string', value= 'string')]
    function setComment($comment) {
      $this->setString('comment', $comment);
    }

    /**
     * Returns the visibility of the UMLMethod
     *
     * @access  public
     * @return  int
     */
    function getVisibility() {
      return $this->getChildValue('visibility');
    }

    /**
     * Sets the 'visibility' of the UML method
     *
     * @access  public
     * @param   int visibility
     */
    #[@fromDia(xpath= 'dia:attribute[@name="visibility"]/dia:enum/@val', value= 'enum')]
    function setVisibility($visibility) {
      $this->setEnum('visibility', $visibility);
    }

    /**
     * Returns TRUE if the UMLMethod is abstract
     *
     * @access  public
     * @return  bool
     */
    function getAbstract() {
      return $this->getChildValue('abstract');
    }

    /**
     * Sets the 'abstract' attribute of the UML method
     *
     * @access  public
     * @param   bool abstract
     */
    #[@fromDia(xpath= 'dia:attribute[@name="abstract"]/dia:boolean/@val', value= 'boolean')]
    function setAbstract($abstract) {
      $this->setBoolean('abstract', $abstract);
    }

    /**
     * Returns TRUE if the UMLMethod is in class scope (static)
     *
     * @access  public
     * @return  bool
     */
    function getClassScope() {
      return $this->getChildValue('class_scope');
    }

    /**
     * Sets the 'class_scope' attribute of the UML method
     *
     * @access  public
     * @param   bool class_scope
     */
    #[@fromDia(xpath= 'dia:attribute[@name="class_scope"]/dia:boolean/@val', value= 'boolean')]
    function setClassScope($class_scope) {
      $this->setBoolean('class_scope', $class_scope);
    }

    /**
     * Returns the inheritance type of the UMLMethod
     *
     * @access  public
     * @return  int
     */
    function getInheritanceType() {
      return $this->getChildValue('inheritance_type');
    }

    /**
     * Sets the 'inheritance_type' of the UML method
     *
     * @access  public
     * @param   int inheritance_type
     */
    #[@fromDia(xpath= 'dia:attribute[@name="inheritance_type"]/dia:enum/@val', value= 'enum')]
    function setInheritanceType($inheritance_type) {
      $this->setEnum('inheritance_type', $inheritance_type);
    }

    /**
     * Return TRUE if the UMLMethods 'query' flag is set
     *
     * @access  public
     * @return  bool
     */
    function getQuery() {
      return $this->getChildValue('query');
    }

    /**
     * Sets the 'query' attribute of the UML method
     *
     * @access  public
     * @param   bool query
     */
    #[@fromDia(xpath= 'dia:attribute[@name="query"]/dia:boolean/@val', value= 'boolean')]
    function setQuery($query) {
      $this->setBoolean('query', $query);
    }

    /**
     * Returns a list of DiaUMLMethodParameter objects
     *
     * @access  public
     * @return  org.dia.DiaUMLMethodParameter[]
     */
    function getParameters() {
      $Parameters= &$this->getChild('parameters');
      return $Parameters->getChildren();
    }

    /**
     * Adds an UMLMethodParameter to the UML method
     *
     * @access  public
     * @param   &org.dia.DiaUMLMethodParameter Parameter
     */
    #[@fromDia(xpath= 'dia:attribute[@name="parameters"]/dia:composite[@type="umlparameter"]', class= 'org.dia.DiaUMLMethodParameter')]
    function addParameter($Parameter) {
      if (!is('org.dia.DiaUMLMethodParameter', $Parameter)) {
        return throw(new IllegalArgumentException(
          'Passed parameter is no "org.dia.DiaUMLMethodParameter"!'
        ));
      }
      $Parameters= &$this->getChild('parameters');
      $Parameters->set($Parameter->getName(), $Parameter);
    }
 }
?>
