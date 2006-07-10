<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('util.profiling.unittest.TestCase', 'TestClass');

  /**
   * Tests method invocations
   *
   * @purpose  Unit Test
   */
  class MethodInvocationTest extends TestCase {
    var
      $fixture= NULL;

    /**
     * Setup method
     *
     * @access  public
     */
    function setUp() {
      $this->fixture= &XPClass::forName('TestClass');
    }
    
    /**
     * Helper method
     *
     * @access  protected
     * @param   string name
     * @param   mixed[] args
     */
    function invoke($name, $args= array()) {
      $method= &$this->fixture->getMethod($name);
      foreach ($method->getArguments() as $pos => $arg) {
        if (
          ('int' == $arg->getType() && is_int($args[$pos])) ||
          ('bool' == $arg->getType() && is_bool($args[$pos])) ||
          ('float' == $arg->getType() && is_float($args[$pos])) ||
          ('string' == $arg->getType() && is_string($args[$pos])) ||
          ('array' == $arg->getType() && is_array($args[$pos])) ||
          (is($arg->getType(), $args[$pos])) ||
          (NULL === $args[$pos])
        ) continue;
        
        return throw(new IllegalArgumentException(
          'Argument #'.$pos.': '.xp::typeOf($args[$pos]).' does not match '.$arg->getType()
        ));
      }

      $method->invoke($this->fixture->newInstance(), $args);
    }

    /**
     * Tests invoking a method without arguments
     *
     * @access  public
     */
    #[@test]
    function noArgsMethod() {
      $this->invoke('toString');
    }

    /**
     * Tests invoking TestClass::setDate() with a Date instance
     *
     * @access  public
     */
    #[@test]
    function setDateWithDateInstance() {
      $this->invoke('setDate', array(new Date()));
    }

    /**
     * Tests invoking TestClass::setDate() with NULL
     *
     * @access  public
     */
    #[@test]
    function setDateWithNull() {
      $this->invoke('setDate', array(NULL));
    }

    /**
     * Tests invoking TestClass::setDate() with an Object instance
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function setDateWithObjectInstance() {
      $this->invoke('setDate', array(new Object()));
    }

    /**
     * Tests invoking TestClass::setDate() with a primitive
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function setDateWithPrimitive() {
      $this->invoke('setDate', array('2007-07-07'));
    }

    /**
     * Tests invoking TestClass::add() with two ints
     *
     * @access  public
     */
    #[@test]
    function addWithInts() {
      $this->invoke('add', array(1, 2));
    }
  }
?>
