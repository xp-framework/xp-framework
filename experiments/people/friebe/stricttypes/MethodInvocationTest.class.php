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
     * Verify type / argument match
     *
     * @access  protected
     * @param   string type
     * @param   &mixed arg
     * @return  bool
     */
    function verifyType($type, &$arg) {
    
      // Handle types arrays
      if ('[]' == substr($type, -2)) {
        $componentType= substr($type, 0, -2);
        for ($i= 0, $s= sizeof($arg); $i < $s; $i++) {
          if (!$this->verifyType($componentType, $arg[$i])) return FALSE;
        }
        return TRUE;
      }
      
      // Handle other types
      switch ($type) {
        case 'int': return is_int($arg);
        case 'bool': return is_bool($arg);
        case 'float': return is_float($arg);
        case 'string': return is_string($arg);
        case 'array': return is_array($args);
        default: return NULL === $arg || is($type, $arg);
      }
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
      $arguments= $method->getArguments();
      
      // Check number of arguments
      if (sizeof($arguments) != sizeof($args)) {
        return throw(new IllegalArgumentException(
          'Incorrect number of arguments (expected: '.sizeof($arguments).', have: '.sizeof($args).')'
        ));
      }
      
      // Check types
      foreach ($arguments as $pos => $arg) {
        if ($this->verifyType($arg->getType(), $args[$pos])) continue;

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

    /**
     * Tests invoking TestClass::add() with two floats
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function addWithFloats() {
      $this->invoke('add', array(1.0, 2.0));
    }

    /**
     * Tests invoking TestClass::add() with two objects
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function addWithObjects() {
      $this->invoke('add', array(new Date(), new Object()));
    }

    /**
     * Tests invoking TestClass::add() with NULLs
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function addWithNulls() {
      $this->invoke('add', array(NULL, NULL));
    }

    /**
     * Tests invoking TestClass::add() with only argument
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function addWithOnlyOneArgument() {
      $this->invoke('add', array(1));
    }

    /**
     * Tests invoking TestClass::add() with too many arguments
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function addWithTooManyArguments() {
      $this->invoke('add', array(1, 2, 3));
    }
    
    /**
     * Tests invoking TestClass::setNames() with an array of strings
     *
     * @access  public
     */
    #[@test]
    function setNamesWithStringArray() {
      $this->invoke('setNames', array(array('Timm', 'Alex')));
    }

    /**
     * Tests invoking TestClass::setNames() with a mixed array
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function setNamesWithMixedArray() {
      $this->invoke('setNames', array(array('Timm', FALSE, 1)));
    }
  }
?>
