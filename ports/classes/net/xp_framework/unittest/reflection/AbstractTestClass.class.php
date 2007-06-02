<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Abstract base class
   *
   * @see      xp://net.xp_framework.unittest.reflection.ReflectionTest
   * @purpose  Test class
   */
  abstract class AbstractTestClass extends Object {
    protected
      $inherited= NULL;

    /**
     * Child classes must have a constructor
     *
     */
    abstract public function __construct();
    
    /**
     * Retrieve date
     *
     * @return  util.Date
     */    
    abstract public function getDate();

    /**
     * NOOP.
     *
     */    
    public function clearDate() {
    }
  } 
?>
