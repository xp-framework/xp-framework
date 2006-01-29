<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('BaseClass');

  /**
   * Child class
   *
   * @see      xp://ThisTestCase
   * @purpose  Test
   */
  class ChildClass extends BaseClass {
  
    /**
     * Returns this class' name
     *
     * @model   static
     * @access  public
     * @return  string
     */
    function name() {
      return 'child';
    }
  }
?>
