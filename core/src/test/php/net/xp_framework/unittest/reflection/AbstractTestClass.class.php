<?php namespace net\xp_framework\unittest\reflection;

/**
 * Abstract base class
 *
 * @see      xp://net.xp_framework.unittest.reflection.ReflectionTest
 * @purpose  Test class
 */
abstract class AbstractTestClass extends \lang\Object {
  protected
    #[@type('lang.Object')]
    $inherited= null;

  /**
   * Constructor
   *
   */
  public function __construct() {}
  
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
