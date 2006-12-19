<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaComposite'
  );

  /**
   * Represents a 'dia:composite type="umlattribute"' node
   */
  class DiaUMLAttribute extends DiaComposite {

    public
      $type= 'umlattribute';

    /**
     * Initialize this UMLAttribute object with default values
     *
     * @access  public
     */
    public function initialize() {
      // default values and flags
      $this->setName('__noname__');
      $this->setType(NULL);
      $this->setValue(NULL);
      $this->setComment(NULL);
      $this->setVisibility(0);
      $this->setAbstract(FALSE);
      $this->setClassScope(FALSE);
    }

    /**
     * Returns the comment of this attribute
     *
     * @access  public
     * @return  string
     */
    public function getComment() {
      return $this->getChildValue('comment');
    }

    /**
     * Sets the comment of this class attribute
     *
     * @access  public
     * @param   string comment
     */
    #[@fromDia(xpath= 'dia:attribute[@name="comment"]/dia:string', value= 'string')]
    public function setComment($comment) {
      $this->setString('comment', $comment);
    }

    /**
     * Returns the visibility of this attribute
     *
     * @access  public
     * @return  int
     */
    public function getVisibility() {
      return $this->getChildValue('visibility');
    }

    /**
     * Sets the visibility of this class attribute
     *
     * @access  public
     * @param   int visibility
     */
    #[@fromDia(xpath= 'dia:attribute[@name="visibility"]/dia:enum/@val', value= 'enum')]
    public function setVisibility($visibility) {
      $this->setEnum('visibility', $visibility);
    }

    /**
     * Returns TRUE if this is an abstract attribute
     *
     * @access  public
     * @return  bool
     */
    public function getAbstract() {
      return $this->getChildValue('abstract');
    }

    /**
     * Sets the 'abstract' flag of this class attribute
     *
     * @access  public
     * @param   bool abstract
     */
    #[@fromDia(xpath= 'dia:attribute[@name="abstract"]/dia:boolean/@val', value= 'boolean')]
    public function setAbstract($abstract) {
      $this->setBoolean('abstract', $abstract);
    }

    /**
     * Returns TRUE if this attribute has 'class_scope' set
     *
     * @access  public
     * @return  bool
     */
    public function getClassScope() {
      return $this->getChildValue('class_scope');
    }

    /**
     * Sets the 'class_scope' flag of this class attribute
     *
     * @access  public
     * @param   bool class_scope
     */
    #[@fromDia(xpath= 'dia:attribute[@name="class_scope"]/dia:boolean/@val', value= 'boolean')]
    public function setClassScope($class_scope) {
      $this->setBoolean('class_scope', $class_scope);
    }
 }
?>
