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
      $annotations  = array(),
      $fields       = array(),
      $methods      = array(),
      $constants    = array(),
      $interfaces   = NULL,
      $usedClasses  = NULL,
      $superclass   = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      $this->interfaces= &new ClassIterator();
      $this->usedClasses= &new ClassIterator();
    }
    
    function fields() {
    }
    
    function methods() {      
    }
  }
?>
