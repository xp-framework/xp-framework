<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('text.doclet.AnnotatedDoc', 'text.doclet.ClassIterator');

  define('EXCEPTION_CLASS',   'exception');
  define('ERROR_CLASS',       'error');
  define('INTERFACE_CLASS',   'interface');
  define('ORDINARY_CLASS',    'class');

  /**
   * Represents an XP class or interface and provides access to 
   * information about it, its comment and tags, and members.
   *
   * @purpose  Documents a class
   */
  class ClassDoc extends AnnotatedDoc {
    var
      $fields         = array(),
      $methods        = array(),
      $constants      = array(),
      $interfaces     = NULL,
      $usedClasses    = NULL,
      $superclass     = NULL,
      $type           = NULL,
      $qualifiedName  = '';
    
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      $this->interfaces= &new ClassIterator();
      $this->usedClasses= &new ClassIterator();
    }

    /**
     * Set rootdoc
     *
     * @access  public
     * @param   &RootDoc root
     */
    function setRoot(&$root) {
      parent::setRoot($root);
      $this->interfaces->root= &$root;
      $this->usedClasses->root= &$root;    
    }
    
    /**
     * Retrieve class type, which one of the following constants
     * 
     * <ul>
     *   <li>EXCEPTION_CLASS</li>
     *   <li>ERROR_CLASS</li>
     *   <li>INTERFACE_CLASS</li>
     *   <li>ORDINARY_CLASS</li>
     * </ul>
     *
     * @access  public
     * @return  string
     */
    function classType() {
      static $map= array(
        'lang.Exception' => EXCEPTION_CLASS,
        'lang.Error'     => ERROR_CLASS,
        'lang.Interface' => INTERFACE_CLASS
      );

      if ($this->type) return $this->type;    // Already known

      $cmp= &$this;
      do {
        if (isset($map[$cmp->qualifiedName])) {
          return $this->type= $map[$cmp->qualifiedName];
        }
      } while ($cmp= &$cmp->superclass);

      return $this->type= ORDINARY_CLASS;
    }
    
    /**
     * Returns whether this class is an exception class.
     *
     * @access  public
     * @return  bool
     */
    function isException() {
      return EXCEPTION_CLASS == $this->classType();
    }
    
    /**
     * Returns whether this class is an error class.
     *
     * @access  public
     * @return  bool
     */
    function isError() {
      return ERROR_CLASS == $this->classType();
    }

    /**
     * Returns whether this class is an interface.
     *
     * @access  public
     * @return  bool
     */
    function isInterface() {
      return INTERFACE_CLASS == $this->classType();
    }
    
    /**
     * Returns whether this class is a subclass of a given class.
     *
     * @access  public
     * @return  bool
     */
    function subclassOf(&$classdoc) {
      $cmp= &$this;
      do {
        if ($cmp->qualifiedName == $classdoc->qualifiedName) return TRUE;
      } while ($cmp= &$cmp->superclass);

      return FALSE;
    }
    
    /**
     * Get the fully qualified name of this program element. For example, 
     * for the class util.Date, return "util.Date". 
     *
     * @access  public
     * @return  string
     */
    function qualifiedName() {
      return $this->qualifiedName;
    }
    
    /**
     * Returns a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'<'.$this->classType().' '.$this->qualifiedName.'>';
    }

    /**
     * Returns a hashcode for this object
     *
     * @access  public
     * @return  string
     */
    function hashCode() {
      return $this->getClassName().$this->qualifiedName;
    }
  }
?>
