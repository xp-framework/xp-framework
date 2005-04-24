<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('Doc');

  /**
   *
   * @purpose  Documents a class
   */
  class ClassDoc extends Doc {
    var
      $annotations    = array(),
      $fields         = array(),
      $methods        = array(),
      $constants      = array(),
      $interfaces     = NULL,
      $usedClasses    = NULL,
      $superclass     = NULL,
      $root           = NULL,
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
     * Returns whether this class is an exception class.
     *
     * @access  public
     * @return  bool
     */
    function isException() {
      return $this->subclassOf($this->root->classNamed('lang.Exception'));
    }
    
    /**
     * Returns whether this class is an error class.
     *
     * @access  public
     * @return  bool
     */
    function isError() {
      return $this->subclassOf($this->root->classNamed('lang.Error'));
    }

    /**
     * Returns whether this class is an interface.
     *
     * @access  public
     * @return  bool
     */
    function isInterface() {
      return $this->subclassOf($this->root->classNamed('lang.Interface'));
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
     * Set rootdoc
     *
     * @access  public
     * @param   &RootDoc root
     */
    function setRoot(&$root) {
      $this->root= &$root;
      $this->interfaces->root= &$root;
      $this->usedClasses->root= &$root;    
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
  }
?>
