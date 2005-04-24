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
