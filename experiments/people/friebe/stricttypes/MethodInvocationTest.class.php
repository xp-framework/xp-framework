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
        if (is($arg->getType(), $args[$pos])) continue;
        
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
     * Tests invoking a method with correct type
     *
     * @access  public
     */
    #[@test]
    function oneArgCorrectType() {
      $this->invoke('setDate', array(new Date()));
    }

    /**
     * Tests invoking a method with correct type
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function oneArgIncorrectType() {
      $this->invoke('setDate', array(new Object()));
    }
  }
?>
