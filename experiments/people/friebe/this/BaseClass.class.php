<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Base class
   *
   * @see      xp://ThisTestCase
   * @purpose  Test
   */
  class BaseClass extends Object {
  
    /**
     * Static method
     *
     * @model   static
     * @access  public
     * @return  string
     */
    function staticMethod() {
      return this::invoke('name');
    }
    
    /**
     * Returns this class' name
     *
     * @model   static
     * @access  public
     * @return  string
     */
    function name() {
      return 'base';
    }
  }
?>
